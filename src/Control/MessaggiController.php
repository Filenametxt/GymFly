<?php
namespace App\Control;

use App\View\Interface\MessaggiView;
use App\View\MessaggiViewSmarty;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Cliente;
use App\Entity\Allenatore;
use App\Entity\Amministratore;
use App\Entity\Messaggio;
use App\Entity\Repository\MessaggioRepositoryInterface;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\UtenteRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineMessaggioRepository;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;

class MessaggiController
{
    private MessaggioRepositoryInterface $messaggioRepo;
    private PalestraRepositoryInterface $palestraRepo;
    private ClienteRepositoryInterface $clienteRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private UtenteRepositoryInterface $utenteRepo;
    private MessaggiView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->messaggioRepo = new DoctrineMessaggioRepository($this->entityManager);
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->utenteRepo = new DoctrineUtenteRepository($this->entityManager);
        $this->view = new MessaggiViewSmarty();
    }

    public function mostraMessaggi(): void
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente || !$ruolo) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }

        $utente = $this->utenteRepo->findById($idUtente);
        if (!$utente) {
            $this->view->mostraErrore("Utente non trovato.");
            return;
        }

        // Recupera la palestra associata in base al ruolo dell'utente loggato
        $palestra = null;
        if ($ruolo === 'cliente' && $utente instanceof Cliente) {
            $palestra = $utente->getPalestra();
        } elseif ($ruolo === 'allenatore' && $utente instanceof Allenatore) {
            $palestra = $utente->getPalestra();
        } elseif ($ruolo === 'amministratore' && $utente instanceof Amministratore) {
            $palestra = $this->palestraRepo->findByAmministratore($utente);
        }

        if (!$palestra) {
            $this->view->mostraErrore("Palestra associata non trovata.");
            return;
        }

        // Posta in arrivo per tutti gli utenti via custom repository
        $messaggiRicevuti = $this->messaggioRepo->findByDestinatario($utente);

        // Se l'utente è autorizzato ad inviare messaggi, recupera la posta in uscita e i candidati destinatari
        $messaggiInviati = [];
        $clientiCandidati = [];
        $allenatoriCandidati = [];
        $adminCandidati = [];

        if ($utente->mssAllowed()) {
            $messaggiInviati = $this->messaggioRepo->findByMittente($utente);

            // Carica gli utenti candidati della stessa palestra per l'invio individuale
            $clientiCandidati = $this->clienteRepo->findByPalestra($palestra);
            
            if ($ruolo === 'amministratore') {
                $allenatoriCandidati = $this->allenatoreRepo->findByPalestra($palestra);
                
                // Eventuali altri amministratori (nel nostro caso c'è l'amministratore principale della palestra)
                $adminGym = $palestra->getAmministratore();
                if ($adminGym && $adminGym->getId() !== $utente->getId()) {
                    $adminCandidati = [$adminGym];
                }
            }
        }

        $this->view->mostraBachecaMessaggi([
            'utenteLoggato' => $utente,
            'ruolo' => $ruolo,
            'messaggiRicevuti' => $messaggiRicevuti,
            'messaggiInviati' => $messaggiInviati,
            'clientiCandidati' => $clientiCandidati,
            'allenatoriCandidati' => $allenatoriCandidati,
            'adminCandidati' => $adminCandidati,
            'invioConsentito' => $utente->mssAllowed()
        ]);
    }

    public function inviaMessaggio(): void
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente || !$ruolo) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }

        $utente = $this->utenteRepo->findById($idUtente);
        if (!$utente) {
            $this->view->mostraErrore("Utente non trovato.");
            return;
        }

        if (!$utente->mssAllowed()) {
            $this->view->mostraErrore("Non sei autorizzato ad inviare messaggi.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->mostraErrore("Richiesta non valida.");
            return;
        }

        // Estrae la palestra
        $palestra = null;
        if ($ruolo === 'allenatore' && $utente instanceof Allenatore) {
            $palestra = $utente->getPalestra();
        } elseif ($ruolo === 'amministratore' && $utente instanceof Amministratore) {
            $palestra = $this->palestraRepo->findByAmministratore($utente);
        }

        if (!$palestra) {
            $this->view->mostraErrore("Palestra associata non trovata.");
            return;
        }

        $oggetto = $_POST['oggetto'] ?? '';
        $contenuto = $_POST['contenuto'] ?? '';
        $destinatariTipo = $_POST['destinatari_tipo'] ?? 'selezionati';

        if (trim($oggetto) === '' || trim($contenuto) === '') {
            $this->view->mostraErrore("L'oggetto ed il contenuto del messaggio sono obbligatori.");
            return;
        }

        $recipients = [];

        if ($destinatariTipo === 'selezionati') {
            $destinatariIds = $_POST['destinatari_ids'] ?? [];
            if (!is_array($destinatariIds) || empty($destinatariIds)) {
                $this->view->mostraErrore("Nessun destinatario selezionato.");
                return;
            }

            foreach ($destinatariIds as $idStr) {
                $recipient = $this->utenteRepo->findById((int)$idStr);
                if ($recipient) {
                    // Controllo di sicurezza Anti-IDOR
                    $recipientPalestra = null;
                    if ($recipient instanceof Cliente || $recipient instanceof Allenatore) {
                        $recipientPalestra = $recipient->getPalestra();
                    } elseif ($recipient instanceof Amministratore) {
                        $recipientPalestra = $this->palestraRepo->findByAmministratore($recipient);
                    }

                    if ($recipientPalestra && $recipientPalestra->getId() === $palestra->getId()) {
                        $recipients[] = $recipient;
                    }
                }
            }
        } elseif ($destinatariTipo === 'gruppo') {
            $gruppoTipo = $_POST['gruppo_tipo'] ?? '';
            
            if ($gruppoTipo === 'tutti_clienti') {
                $recipients = $this->clienteRepo->findByPalestra($palestra);
            } elseif ($gruppoTipo === 'tutti_allenatori' && $ruolo === 'amministratore') {
                $recipients = $this->allenatoreRepo->findByPalestra($palestra);
            } elseif ($gruppoTipo === 'tutti_palestra') {
                $clienti = $this->clienteRepo->findByPalestra($palestra);
                $recipients = $clienti;

                if ($ruolo === 'amministratore') {
                    $allenatori = $this->allenatoreRepo->findByPalestra($palestra);
                    $recipients = array_merge($recipients, $allenatori);
                } elseif ($ruolo === 'allenatore') {
                    $admin = $palestra->getAmministratore();
                    if ($admin) {
                        $recipients[] = $admin;
                    }
                }
            }
        }

        // Rimuove il mittente stesso dai destinatari se presente
        $recipients = array_filter($recipients, fn($r) => $r->getId() !== $utente->getId());

        if (empty($recipients)) {
            $this->view->mostraErrore("Nessun destinatario valido trovato all'interno della tua palestra.");
            return;
        }

        try {
            $messaggio = new Messaggio($utente, $oggetto, $contenuto);
            foreach ($recipients as $recipient) {
                $messaggio->aggiungiDestinatario($recipient);
            }
            $this->entityManager->persist($messaggio);
            $this->entityManager->flush();

            $this->view->mostraConfermaInviato("Messaggio inviato con successo!");
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraErrore("Errore di validazione: " . $e->getMessage());
        }
    }
}
