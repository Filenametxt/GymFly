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
use App\Entity\Palestra;
use App\Entity\Utente;
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

    // =========================================================================
    // 1. MOSTRA MESSAGGI (/messaggi)
    // =========================================================================

    public function mostraMessaggi(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $ut = ($idUt && $ruolo) ? $this->utenteRepo->findById($idUt) : null;
        $pal = $ut ? $this->recuperaPalestraMessaggi($ut, $ruolo) : null;
        if (!$ut || !$pal) {
            $this->view->mostraErrore("Sessione non valida, utente o palestra non trovata.");
            return;
        }
        $dati = [
            'utenteLoggato' => $ut, 'ruolo' => $ruolo,
            'messaggiRicevuti' => $this->messaggioRepo->findByDestinatario($ut),
            'messaggiInviati' => [], 'clientiCandidati' => [],
            'allenatoriCandidati' => [], 'adminCandidati' => [], 'invioConsentito' => $ut->mssAllowed()
        ];
        if ($ut->mssAllowed()) {
            $this->caricaCandidatiDestinatari($ut, $ruolo, $pal, $dati);
        }
        $this->view->mostraBachecaMessaggi($dati);
    }

    private function recuperaPalestraMessaggi(Utente $utente, string $ruolo): ?Palestra
    {
        if ($ruolo === 'cliente' && $utente instanceof Cliente) {
            return $utente->getPalestra();
        }
        if ($ruolo === 'allenatore' && $utente instanceof Allenatore) {
            return $utente->getPalestra();
        }
        if ($ruolo === 'amministratore' && $utente instanceof Amministratore) {
            return $this->palestraRepo->findByAmministratore($utente);
        }
        return null;
    }

    private function caricaCandidatiDestinatari(Utente $ut, string $ruolo, Palestra $pal, array &$dati): void
    {
        $dati['messaggiInviati'] = $this->messaggioRepo->findByMittente($ut);
        $dati['clientiCandidati'] = $this->clienteRepo->findByPalestra($pal);
        if ($ruolo === 'amministratore') {
            $dati['allenatoriCandidati'] = $this->allenatoreRepo->findByPalestra($pal);
            $adminGym = $pal->getAmministratore();
            if ($adminGym && $adminGym->getId() !== $ut->getId()) {
                $dati['adminCandidati'] = [$adminGym];
            }
        }
    }

    // =========================================================================
    // 2. INVIA MESSAGGIO (/invia-messaggio)
    // =========================================================================

    public function inviaMessaggio(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $utente = ($idUt && $ruolo) ? $this->utenteRepo->findById($idUt) : null;
        if (!$utente || !$utente->mssAllowed() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->mostraErrore("Azione non autorizzata o richiesta non valida.", "messaggi", "Torna alla Bacheca");
            return;
        }
        $palestra = $this->recuperaPalestraMessaggi($utente, $ruolo);
        $oggetto = trim($_POST['oggetto'] ?? '');
        $contenuto = trim($_POST['contenuto'] ?? '');
        if (!$palestra || $oggetto === '' || $contenuto === '') {
            $this->view->mostraErrore("Dati del messaggio o palestra non trovati.", "messaggi", "Torna alla Bacheca");
            return;
        }
        $this->eseguiInvio($utente, $ruolo, $palestra, $oggetto, $contenuto);
    }

    private function eseguiInvio(Utente $ut, string $ruolo, Palestra $pal, string $ogg, string $cont): void
    {
        $recipients = $this->recuperaDestinatari($ut, $ruolo, $pal);
        if (empty($recipients)) {
            $this->view->mostraErrore("Nessun destinatario valido trovato.", "messaggi", "Torna alla Bacheca");
            return;
        }
        try {
            $messaggio = new Messaggio($ut, $ogg, $cont);
            foreach ($recipients as $recipient) {
                $messaggio->aggiungiDestinatario($recipient);
            }
            $this->messaggioRepo->save($messaggio);
            $this->view->mostraConfermaInviato("Messaggio inviato con successo!", "messaggi", "Torna alla Bacheca");
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraErrore("Errore di validazione: " . $e->getMessage(), "messaggi", "Torna alla Bacheca");
        }
    }

    private function recuperaDestinatari(Utente $ut, string $ruolo, Palestra $pal): array
    {
        $recipients = [];
        $destinatariTipo = $_POST['destinatari_tipo'] ?? 'selezionati';
        if ($destinatariTipo === 'selezionati') {
            $this->filtraDestinatariSelezionati($_POST['destinatari_ids'] ?? [], $pal, $recipients);
        } else {
            $this->raccogliDestinatariGruppo($_POST['gruppo_tipo'] ?? '', $ruolo, $pal, $recipients);
        }
        return array_filter($recipients, fn($r) => $r->getId() !== $ut->getId());
    }

    private function filtraDestinatariSelezionati(array $ids, Palestra $pal, array &$recipients): void
    {
        foreach ($ids as $idStr) {
            $recipient = $this->utenteRepo->findById((int)$idStr);
            if ($recipient) {
                $palRecipient = $this->recuperaPalestraMessaggi($recipient, $recipient->getRuolo());
                if ($palRecipient && $palRecipient->getId() === $pal->getId()) {
                    $recipients[] = $recipient;
                }
            }
        }
    }

    private function raccogliDestinatariGruppo(string $tipo, string $ruolo, Palestra $pal, array &$recipients): void
    {
        if ($tipo === 'tutti_clienti') {
            $recipients = $this->clienteRepo->findByPalestra($pal);
        } elseif ($tipo === 'tutti_allenatori' && $ruolo === 'amministratore') {
            $recipients = $this->allenatoreRepo->findByPalestra($pal);
        } elseif ($tipo === 'tutti_palestra') {
            $recipients = $this->clienteRepo->findByPalestra($pal);
            if ($ruolo === 'amministratore') {
                $recipients = array_merge($recipients, $this->allenatoreRepo->findByPalestra($pal));
            } elseif ($ruolo === 'allenatore' && $pal->getAmministratore()) {
                $recipients[] = $pal->getAmministratore();
            }
        }
    }
}
