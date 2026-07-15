<?php
namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\AttivitaRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaRepository;
use App\View\Interface\AmministratoreView;
use App\View\AmministratoreViewSmarty;
use App\Foundation\Session;
use App\Enum\Sesso;
use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\Attivita;
use App\Entity\Palestra;
use App\Entity\Utente;
use Doctrine\ORM\EntityManagerInterface;

class AmministratoreController
{
    private ClienteRepositoryInterface $clienteRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private AttivitaRepositoryInterface $attivitaRepo;
    private AmministratoreView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->attivitaRepo = new DoctrineAttivitaRepository($this->entityManager);
        $this->view = new AmministratoreViewSmarty();
    }

    /**
     * Recupera la palestra gestita dall'amministratore attualmente loggato.
     */
    private function recuperaPalestraAdmin(): ?Palestra
    {
        $idAdmin = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAdmin || $ruolo !== 'amministratore') {
            return null;
        }

        $admin = $this->entityManager->find(Amministratore::class, $idAdmin);
        if (!$admin) {
            return null;
        }

        return $this->entityManager->getRepository(Palestra::class)->findOneBy(['amministratore' => $admin]);
    }

    /**
     * Genera una password temporanea di 8 caratteri.
     */
    private function generaPasswordTemporanea(): string
    {
        return bin2hex(random_bytes(4));
    }

    /**
     * Invia l'email con la password temporanea generata.
     * Ritorna true se l'invio ha successo, false altrimenti.
     */
    private function inviaMailPasswordTemporanea(string $email, string $nome, string $password): bool
    {
        $oggetto = "Benvenuto in GymFly - Credenziali di Accesso";
        $messaggio = "Ciao $nome,\n\nil tuo account su GymFly è stato creato.\nEcco le tue credenziali temporanee:\n\nEmail: $email\nPassword: $password\n\nTi consigliamo di cambiare la password al primo accesso.\n\nSaluti,\nLo staff di GymFly";
        $headers = "From: no-reply@gymfly.com\r\nReply-To: support@gymfly.com\r\nContent-Type: text/plain; charset=utf-8";

        // Invio tramite mail di PHP
        return @mail($email, $oggetto, $messaggio, $headers);
    }

    /**
     * Creazione Cliente
     */
    public function creaCliente(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Solo l'amministratore di una palestra può effettuare questa operazione.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCreaCliente([]);
            return;
        }

        // Recupero campi e conversione delle stringhe vuote in null
        $nome = !empty($_POST['nome']) ? trim($_POST['nome']) : '';
        $cognome = !empty($_POST['cognome']) ? trim($_POST['cognome']) : '';
        $email = !empty($_POST['email']) ? trim($_POST['email']) : '';
        $cf = !empty($_POST['cf']) ? trim($_POST['cf']) : '';
        $indirizzo = !empty($_POST['indirizzo']) ? trim($_POST['indirizzo']) : '';
        $sessoVal = !empty($_POST['sesso']) ? trim($_POST['sesso']) : '';
        $dataNascitaStr = !empty($_POST['data_nascita']) ? trim($_POST['data_nascita']) : '';
        $luogoNascita = !empty($_POST['luogo_nascita']) ? trim($_POST['luogo_nascita']) : '';
        $metodoPagamento = !empty($_POST['metodo_pagamento']) ? trim($_POST['metodo_pagamento']) : '';
        
        $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
        $indirizzoDomicilio = !empty($_POST['indirizzo_domicilio']) ? trim($_POST['indirizzo_domicilio']) : null;

        if ($nome === '' || $cognome === '' || $email === '' || $cf === '' || $indirizzo === '' || $sessoVal === '' || $dataNascitaStr === '' || $luogoNascita === '' || $metodoPagamento === '') {
            $this->view->mostraStatoOperazione(false, "Tutti i campi contrassegnati con l'asterisco sono obbligatori.");
            return;
        }

        // Verifica unicità email
        $existingUser = $this->entityManager->getRepository(Utente::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $this->view->mostraStatoOperazione(false, "Errore: l'indirizzo email inserito è già associato ad un altro utente.");
            return;
        }

        try {
            $dataDiNascita = new \DateTimeImmutable($dataNascitaStr);
            if ($dataDiNascita > new \DateTimeImmutable()) {
                $this->view->mostraStatoOperazione(false, "Errore: la data di nascita non può essere futura.");
                return;
            }

            $sesso = Sesso::from($sessoVal);
            $tempPassword = $this->generaPasswordTemporanea();

            $cliente = new Cliente(
                $nome,
                $cognome,
                $email,
                $cf,
                $indirizzo,
                $sesso,
                $dataDiNascita,
                $luogoNascita,
                $indirizzoDomicilio,
                $metodoPagamento,
                $tempPassword, // Verrà cifrata all'interno del costruttore o tramite setter
                null, // profile picture
                $telefono
            );
            $cliente->setPalestra($palestra);

            $this->clienteRepo->save($cliente);
            $invioOk = $this->inviaMailPasswordTemporanea($email, $nome, $tempPassword);

            if ($invioOk) {
                $this->view->mostraStatoOperazione(true, "Cliente registrato con successo. Le credenziali temporanee sono state inviate via email.");
            } else {
                $this->view->mostraStatoOperazione(true, "Cliente registrato con successo. Nota: Impossibile inviare l'email (SMTP locale non configurato). La password temporanea generata è: " . $tempPassword);
            }
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraStatoOperazione(false, "Errore di validazione: " . $e->getMessage());
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore generico durante la creazione del cliente: " . $e->getMessage());
        }
    }

    /**
     * Creazione Allenatore
     */
    public function creaAllenatore(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Solo l'amministratore di una palestra può effettuare questa operazione.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCreaAllenatore([]);
            return;
        }

        $nome = !empty($_POST['nome']) ? trim($_POST['nome']) : '';
        $cognome = !empty($_POST['cognome']) ? trim($_POST['cognome']) : '';
        $email = !empty($_POST['email']) ? trim($_POST['email']) : '';
        $cf = !empty($_POST['cf']) ? trim($_POST['cf']) : '';
        $indirizzo = !empty($_POST['indirizzo']) ? trim($_POST['indirizzo']) : '';
        $sessoVal = !empty($_POST['sesso']) ? trim($_POST['sesso']) : '';
        $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;

        if ($nome === '' || $cognome === '' || $email === '' || $cf === '' || $indirizzo === '' || $sessoVal === '') {
            $this->view->mostraStatoOperazione(false, "Tutti i campi contrassegnati con l'asterisco sono obbligatori.");
            return;
        }

        $existingUser = $this->entityManager->getRepository(Utente::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $this->view->mostraStatoOperazione(false, "Errore: l'indirizzo email inserito è già associato ad un altro utente.");
            return;
        }

        try {
            $sesso = Sesso::from($sessoVal);
            $tempPassword = $this->generaPasswordTemporanea();

            $allenatore = new Allenatore(
                $nome,
                $cognome,
                $email,
                $cf,
                $indirizzo,
                $sesso,
                $tempPassword,
                null, // profile picture
                $telefono,
                $palestra
            );

            $this->allenatoreRepo->save($allenatore);
            $invioOk = $this->inviaMailPasswordTemporanea($email, $nome, $tempPassword);

            if ($invioOk) {
                $this->view->mostraStatoOperazione(true, "Allenatore creato con successo. Le credenziali temporanee sono state inviate via email.");
            } else {
                $this->view->mostraStatoOperazione(true, "Allenatore creato con successo. Nota: Impossibile inviare l'email (SMTP locale non configurato). La password temporanea generata è: " . $tempPassword);
            }
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraStatoOperazione(false, "Errore di validazione: " . $e->getMessage());
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore generico durante la creazione del preparatore: " . $e->getMessage());
        }
    }

    /**
     * Creazione Attività
     */
    public function creaAttivita(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Solo l'amministratore può registrare nuove attività.", "dashboard-admin", "Torna alla Dashboard");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCreaAttivita([]);
            return;
        }

        $nome = !empty($_POST['nome']) ? trim($_POST['nome']) : '';
        $descrizione = !empty($_POST['descrizione']) ? trim($_POST['descrizione']) : '';
        $maxPartecipanti = isset($_POST['max_partecipanti']) ? (int)$_POST['max_partecipanti'] : 0;

        $ritorno = "crea-attivita";

        if ($nome === '' || $descrizione === '' || $maxPartecipanti <= 0) {
            $this->view->mostraStatoOperazione(false, "Tutti i campi sono obbligatori e il limite di partecipanti deve essere maggiore di zero.", $ritorno, "Torna all'Attività");
            return;
        }

        // Evita duplicati per nome
        if ($this->attivitaRepo->existsByNome($nome)) {
            $this->view->mostraStatoOperazione(false, "Errore: esiste già un'attività registrata con questo nome.", $ritorno, "Torna all'Attività");
            return;
        }

        try {
            $attivita = new Attivita($nome, $descrizione, $maxPartecipanti);
            $this->attivitaRepo->save($attivita);

            $this->view->mostraStatoOperazione(true, "Nuova attività '" . $nome . "' creata con successo nel catalogo.", $ritorno, "Torna all'Attività");
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraStatoOperazione(false, "Errore di validazione: " . $e->getMessage(), $ritorno, "Torna all'Attività");
        }
    }

    /**
     * Rimozione Attività
     */
    public function rimuoviAttivita(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $idAttivita = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $attivita = $this->attivitaRepo->findById($idAttivita);

        if (!$attivita) {
            $this->view->mostraStatoOperazione(false, "Attività non trovata.");
            return;
        }

        try {
            $nomeAttivita = $attivita->getNome();
            $this->attivitaRepo->delete($attivita);
            $this->view->mostraStatoOperazione(true, "Attività '" . $nomeAttivita . "' rimossa con successo dal catalogo.");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile rimuovere l'attività: " . $e->getMessage());
        }
    }

    /**
     * Abilitazione / Disabilitazione dell'attività per un allenatore
     */
    public function abilitaAttivitaAllenatore(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Mostra il form di associazione: carica allenatori della palestra e catalogo attività
            $allenatori = $this->allenatoreRepo->findByPalestra($palestra);
            $attivita = $this->attivitaRepo->findAll();

            $this->view->mostraFormAbilitaAttivita([
                'allenatori' => $allenatori,
                'attivita' => $attivita
            ]);
            return;
        }

        $idAllenatore = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;
        $idAttivita = isset($_POST['id_attivita']) ? (int)$_POST['id_attivita'] : 0;
        $azione = isset($_POST['azione']) ? $_POST['azione'] : 'abilita'; // 'abilita' o 'disabilita'

        $allenatore = $this->allenatoreRepo->findById($idAllenatore);
        $attivita = $this->attivitaRepo->findById($idAttivita);

        if (!$allenatore || !$attivita) {
            $this->view->mostraStatoOperazione(false, "Allenatore o Attività non validi.");
            return;
        }

        // Controllo IDOR: l'allenatore deve appartenere alla palestra dell'admin loggato
        if ($allenatore->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. L'allenatore non appartiene al tuo centro sportivo.");
            return;
        }

        try {
            if ($azione === 'abilita') {
                $allenatore->addAbilitazione($attivita);
                $messaggio = "Allenatore " . $allenatore->getNome() . " " . $allenatore->getCognome() . " abilitato con successo all'attività " . $attivita->getNome() . ".";
            } else {
                $allenatore->removeAbilitazione($attivita);
                $messaggio = "Abilitazione all'attività " . $attivita->getNome() . " rimossa con successo per l'allenatore " . $allenatore->getNome() . " " . $allenatore->getCognome() . ".";
            }

            $this->entityManager->flush();
            $this->view->mostraStatoOperazione(true, $messaggio);
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore durante l'aggiornamento delle abilitazioni: " . $e->getMessage());
        }
    }

    /**
     * Rimozione Cliente
     */
    public function rimuoviCliente(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $idCliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $cliente = $this->clienteRepo->findById($idCliente);

        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato.");
            return;
        }

        // Controllo di sicurezza Anti-IDOR
        if ($cliente->getPalestra() === null || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Il cliente indicato non appartiene alla tua palestra.");
            return;
        }

        try {
            $nomeCompleto = $cliente->getNome() . " " . $cliente->getCognome();

            // 1. Disiscrizione da tutte le attività pianificate e gestione coda
            foreach ($cliente->getAttivitaPianificate() as $attivita) {
                $cliente->cancellaIscrizioneAttivita($attivita);
                $attivita->setPrenotati(max(0, $attivita->getPrenotati() - 1));

                // Scorrimento della coda
                $codaRepo = $this->entityManager->getRepository(\App\Entity\CodaAttesa::class);
                $codaPrimo = $codaRepo->findOneBy(
                    ['attivitaPianificata' => $attivita],
                    ['dataInserimento' => 'ASC']
                );

                if ($codaPrimo) {
                    $clienteScelto = $codaPrimo->getCliente();
                    $clienteScelto->iscriviAAttivita($attivita);
                    $attivita->setPrenotati($attivita->getPrenotati() + 1);

                    // Rimuovi dalla coda
                    $this->entityManager->remove($codaPrimo);

                    // Invia messaggio in bacheca
                    $mittente = $attivita->getAllenatore();
                    $oggettoMsg = "Iscrizione automatica all'attività";
                    $contenutoMsg = "Ciao " . $clienteScelto->getNome() . ",\n\nti informiamo che si è liberato un posto e sei stato iscritto automaticamente all'attività: " . $attivita->getAttivita()->getNome() . " in data " . $attivita->getGiorno()->format('d/m/Y') . " alle ore " . $attivita->getOrario() . ":00.\n\nSaluti,\nLo staff di GymFly";
                    
                    $messaggio = new \App\Entity\Messaggio($mittente, $oggettoMsg, $contenutoMsg);
                    $messaggio->aggiungiDestinatario($clienteScelto);
                    $this->entityManager->persist($messaggio);

                    // Invia email di notifica (se SMTP non è configurato non fa nulla)
                    $headers = "From: no-reply@gymfly.com\r\nReply-To: support@gymfly.com\r\nContent-Type: text/plain; charset=utf-8";
                    @mail($clienteScelto->getEmail(), $oggettoMsg, $contenutoMsg, $headers);
                }
            }

            // 2. Rimuovi iscrizioni alle code d'attesa per altre attività
            $codaRepo = $this->entityManager->getRepository(\App\Entity\CodaAttesa::class);
            $codas = $codaRepo->findBy(['cliente' => $cliente]);
            foreach ($codas as $c) {
                $this->entityManager->remove($c);
            }

            // 3. Rimuovi sessioni private associate
            $sessRepo = $this->entityManager->getRepository(\App\Entity\SessionePrivata::class);
            $sessions = $sessRepo->findBy(['atleta' => $cliente]);
            foreach ($sessions as $s) {
                $this->entityManager->remove($s);
            }

            // 4. Rimuovi parametri biometrici associati
            $paramRepo = $this->entityManager->getRepository(\App\Entity\Parametri::class);
            $params = $paramRepo->findBy(['cliente' => $cliente]);
            foreach ($params as $p) {
                $this->entityManager->remove($p);
            }

            // 5. Rimuovi scheda di allenamento (con i relativi allenamenti in cascata)
            // Cerchiamo la scheda direttamente dal repository per essere sicuri di trovarla
            // anche se l'associazione id_scheda su Cliente è null o disallineata.
            $schRepo = $this->entityManager->getRepository(\App\Entity\Scheda::class);
            $sch = $schRepo->findOneBy(['cliente' => $cliente]);
            if ($sch) {
                $cliente->setScheda(null);
                $this->entityManager->remove($sch);
            }

            // Salva gli oggetti 1-1 prima di nullificarli sul cliente per evitare constraint violations al flush
            $cert = $cliente->getCertificatoMedico();
            $abb = $cliente->getAbbonamento();
            $isc = $cliente->getIscrizione();

            if ($cert) {
                $cliente->setCertificatoMedico(null);
            }
            if ($abb) {
                $cliente->setAbbonamento(null);
            }
            if ($isc) {
                $cliente->setIscrizione(null);
            }

            // Eseguiamo un primo flush per scrivere a NULL i campi FK (id_abbonamento_attivo, id_certificato_medico, id_iscrizione ed id_scheda)
            // ed eliminare effettivamente i Parametri, SessioniPrivate, CodeAttesa e Schede
            $this->entityManager->flush();

            // Ora eliminiamo il Cliente stesso (e quindi la riga in Cliente ed Utente)
            $this->clienteRepo->delete($cliente);

            // Infine, ora che non c'è più alcun riferimento FK da parte del cliente, eliminiamo le entità 1-1 orfane
            if ($cert) {
                $this->entityManager->remove($cert);
            }
            if ($abb) {
                $this->entityManager->remove($abb);
            }
            if ($isc) {
                $this->entityManager->remove($isc);
            }
            $this->entityManager->flush();

            $this->view->mostraStatoOperazione(true, "Cliente " . $nomeCompleto . " rimosso con successo dal database.");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile eliminare il cliente: " . $e->getMessage());
        }
    }

    /**
     * Rimozione Allenatore
     */
    public function rimuoviAllenatore(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $idAllenatore = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $allenatore = $this->allenatoreRepo->findById($idAllenatore);

        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.");
            return;
        }

        // Controllo di sicurezza Anti-IDOR
        if ($allenatore->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. L'allenatore indicato non appartiene alla tua palestra.");
            return;
        }

        try {
            $nomeCompleto = $allenatore->getNome() . " " . $allenatore->getCognome();
            $this->allenatoreRepo->delete($allenatore);
            $this->view->mostraStatoOperazione(true, "Allenatore " . $nomeCompleto . " rimosso con successo dal database.");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile eliminare l'allenatore: " . $e->getMessage());
        }
    }
}
