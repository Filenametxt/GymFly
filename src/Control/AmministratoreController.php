<?php
namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Control\AttivitaPianificataController;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\AttivitaRepositoryInterface;
use App\Entity\Repository\CodaAttesaRepositoryInterface;
use App\Entity\Repository\UtenteRepositoryInterface;
use App\Entity\Repository\SessionePrivataRepositoryInterface;
use App\Entity\Repository\ParametriRepositoryInterface;
use App\Entity\Repository\SchedaRepositoryInterface;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaRepository;
use App\Foundation\Persistence\Repository\DoctrineCodaAttesaRepository;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;
use App\Foundation\Persistence\Repository\DoctrineSessionePrivataRepository;
use App\Foundation\Persistence\Repository\DoctrineParametriRepository;
use App\Foundation\Persistence\Repository\DoctrineSchedaRepository;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\View\Interface\AmministratoreView;
use App\View\AmministratoreViewSmarty;
use App\View\VisualizzazioneViewSmarty;
use App\Foundation\Session;
use App\Enum\Sesso;
use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\Attivita;
use App\Entity\Palestra;
use App\Entity\CertificatoMedico;
use App\Entity\AbbonamentoAttivo;
use App\Entity\Iscrizione;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Repository\MessaggioRepositoryInterface;
use \App\Foundation\Persistence\Repository\DoctrineMessaggioRepository;

class AmministratoreController
{
    private ClienteRepositoryInterface $clienteRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private AttivitaRepositoryInterface $attivitaRepo;
    private CodaAttesaRepositoryInterface $codaAttesaRepo;
    private UtenteRepositoryInterface $utenteRepo;
    private SessionePrivataRepositoryInterface $sessionePrivataRepo;
    private ParametriRepositoryInterface $parametriRepo;
    private SchedaRepositoryInterface $schedaRepo;
    private PalestraRepositoryInterface $palestraRepo;
    private MessaggioRepositoryInterface $messaggioRepo;
    private AmministratoreView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->attivitaRepo = new DoctrineAttivitaRepository($this->entityManager);
        $this->codaAttesaRepo = new DoctrineCodaAttesaRepository($this->entityManager);
        $this->utenteRepo = new DoctrineUtenteRepository($this->entityManager);
        $this->sessionePrivataRepo = new DoctrineSessionePrivataRepository($this->entityManager);
        $this->parametriRepo = new DoctrineParametriRepository($this->entityManager);
        $this->schedaRepo = new DoctrineSchedaRepository($this->entityManager);
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->view = new AmministratoreViewSmarty();
        $this->messaggioRepo = new DoctrineMessaggioRepository($this->entityManager);
    }

    // =========================================================================
    // 1. CREAZIONE CLIENTE
    // =========================================================================

    public function creaCliente(): void     //va a recuperare la palestra dall'amministratore e mostra il form per la creazione del cliente
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->mostraStatoOperazione(false, "Accesso negato. Nessuna palestra associata all'utente.", "login", "Torna al Login");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCreaCliente([]);
            return;
        }
        $this->eseguiCreazioneCliente($palestra);       //se non è GET è POST, quinid la form è stata già inviata
    }

    private function eseguiCreazioneCliente(Palestra $palestra): void
    {
        $dati = $this->estraiDatiClientePost();
        if ($dati['nome'] === '' || $dati['cognome'] === '' || $dati['email'] === '' || $dati['cf'] === '' || $dati['indirizzo'] === '' || $dati['sessoVal'] === '' || $dati['dataNascitaStr'] === '' || $dati['luogoNascita'] === '' || $dati['metodoPagamento'] === '') {
            $this->mostraStatoOperazione(false, "Campi obbligatori mancanti.", "clienti", "Torna a Gestione Clienti");
            return;
        }
        if ($this->utenteRepo->findByEmail($dati['email'])) {
            $this->mostraStatoOperazione(false, "Email già associata ad un altro utente.", "clienti", "Torna a Gestione Clienti");
            return;
        }
        $this->salvaEInviaMailCliente($palestra, $dati);
    }

    private function estraiDatiClientePost(): array     //ritorna tutte le infrormazioni inviate con il POST
    {
        return [
            'nome' => !empty($_POST['nome']) ? trim($_POST['nome']) : '',
            'cognome' => !empty($_POST['cognome']) ? trim($_POST['cognome']) : '',
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : '',
            'cf' => !empty($_POST['cf']) ? trim($_POST['cf']) : '',
            'indirizzo' => !empty($_POST['indirizzo']) ? trim($_POST['indirizzo']) : '',
            'sessoVal' => !empty($_POST['sesso']) ? trim($_POST['sesso']) : '',
            'dataNascitaStr' => !empty($_POST['data_nascita']) ? trim($_POST['data_nascita']) : '',
            'luogoNascita' => !empty($_POST['luogo_nascita']) ? trim($_POST['luogo_nascita']) : '',
            'metodoPagamento' => !empty($_POST['metodo_pagamento']) ? trim($_POST['metodo_pagamento']) : '',
            'telefono' => !empty($_POST['telefono']) ? trim($_POST['telefono']) : null,
            'indirizzoDomicilio' => !empty($_POST['indirizzo_domicilio']) ? trim($_POST['indirizzo_domicilio']) : null,
        ];
    }

    private function salvaEInviaMailCliente(Palestra $palestra, array $dati): void      //salva il cliente e invia la mail con la password temporanea
    {
        try {           //vincolo sulla data di nascita
            $dataDiNascita = new \DateTimeImmutable($dati['dataNascitaStr']);
            if ($dataDiNascita > new \DateTimeImmutable()) {
                $this->mostraStatoOperazione(false, "La data di nascita non può essere futura.", "clienti", "Torna a Gestione Clienti");
                return;
            }
            $tempPassword = $this->generaPasswordTemporanea();
            $cliente = new Cliente($dati['nome'], $dati['cognome'], $dati['email'], $dati['cf'], $dati['indirizzo'], Sesso::from($dati['sessoVal']), $dataDiNascita, $dati['luogoNascita'], $dati['indirizzoDomicilio'], $dati['metodoPagamento'], $tempPassword, null, $dati['telefono']);
            $cliente->setPalestra($palestra);
            $this->clienteRepo->save($cliente);
            $invioOk = $this->inviaMailPasswordTemporanea($dati['email'], $dati['nome'], $tempPassword);
            $msg = "Cliente registrato con successo. " . ($invioOk ? "Le credenziali sono state inviate via email." : "Nota: SMTP locale non configurato. Password temporanea: " . $tempPassword);
            $this->mostraStatoOperazione(true, $msg, "clienti", "Torna a Gestione Clienti");
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Errore durante la creazione: " . $e->getMessage(), "clienti", "Torna a Gestione Clienti");
        }
    }

    // =========================================================================
    // 2. CREAZIONE ALLENATORE
    // =========================================================================

    public function creaAllenatore(): void      //va a recuperare la palestra dall'amministratore e mostra il form per la creazione dell'allenatore
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->mostraStatoOperazione(false, "Accesso negato. Nessuna palestra associata all'utente.", "login", "Torna al Login");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCreaAllenatore([]);
            return;
        }
        $this->eseguiCreazioneAllenatore($palestra);
    }

    private function eseguiCreazioneAllenatore(Palestra $palestra): void
    {
        $nome = !empty($_POST['nome']) ? trim($_POST['nome']) : '';             //se il nome è vuoto allora ritorna stringa vuota, altrimenti ritorna il nome senza spazi
        $cognome = !empty($_POST['cognome']) ? trim($_POST['cognome']) : '';
        $email = !empty($_POST['email']) ? trim($_POST['email']) : '';
        $cf = !empty($_POST['cf']) ? trim($_POST['cf']) : '';
        $indirizzo = !empty($_POST['indirizzo']) ? trim($_POST['indirizzo']) : '';
        $sessoVal = !empty($_POST['sesso']) ? trim($_POST['sesso']) : '';
        $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;

        if ($nome === '' || $cognome === '' || $email === '' || $cf === '' || $indirizzo === '' || $sessoVal === '') {
            $this->mostraStatoOperazione(false, "Campi obbligatori mancanti.", "allenatori", "Torna a Gestione Allenatori");
            return;
        }
        if ($this->utenteRepo->findByEmail($email)) {
            $this->mostraStatoOperazione(false, "Email già associata ad un altro utente.", "allenatori", "Torna a Gestione Allenatori");
            return;
        }
        $this->salvaEInviaMailAllenatore($palestra, $nome, $cognome, $email, $cf, $indirizzo, $sessoVal, $telefono);
    }

    private function salvaEInviaMailAllenatore(Palestra $palestra, string $nome, string $cognome, string $email, string $cf, string $indirizzo, string $sessoVal, ?string $telefono): void
    {
        try {
            $tempPassword = $this->generaPasswordTemporanea();
            $allenatore = new Allenatore($nome, $cognome, $email, $cf, $indirizzo, Sesso::from($sessoVal), $tempPassword, null, $telefono, $palestra);
            $this->allenatoreRepo->save($allenatore);
            $invioOk = $this->inviaMailPasswordTemporanea($email, $nome, $tempPassword);
            $msg = "Allenatore creato con successo. " . ($invioOk ? "Le credenziali sono state inviate via email." : "Nota: SMTP locale non configurato. Password temporanea: " . $tempPassword);
            $this->mostraStatoOperazione(true, $msg, "allenatori", "Torna a Gestione Allenatori");
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Errore durante la creazione: " . $e->getMessage(), "allenatori", "Torna a Gestione Allenatori");
        }
    }

    // =========================================================================
    // 3. CREAZIONE ATTIVITA
    // =========================================================================

    public function creaAttivita(): void        //va a recuperare la palestra dall'amministratore e mostra il form per la creazione dell'attivita
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->mostraStatoOperazione(false, "Accesso negato. Nessuna palestra associata all'utente.", "login", "Torna al Login");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCreaAttivita([]);
            return;
        }
        $this->eseguiCreazioneAttivita();
    }

    private function eseguiCreazioneAttivita(): void  //esegue la creazione dell'attività, validando i dati e salvando 
    {
        $nome = !empty($_POST['nome']) ? trim($_POST['nome']) : '';
        $descrizione = !empty($_POST['descrizione']) ? trim($_POST['descrizione']) : '';
        $maxPartecipanti = isset($_POST['max_partecipanti']) ? (int)$_POST['max_partecipanti'] : 0;

        if ($nome === '' || $descrizione === '' || $maxPartecipanti <= 0) {
            $this->mostraStatoOperazione(false, "Tutti i campi sono obbligatori e partecipanti > 0.", "crea-attivita", "Torna all'Attività");
            return;
        }
        if ($this->attivitaRepo->existsByNome($nome)) {
            $this->mostraStatoOperazione(false, "Attività già esistente.", "crea-attivita", "Torna all'Attività");
            return;
        }
        try {
            $attivita = new Attivita($nome, $descrizione, $maxPartecipanti);
            $this->attivitaRepo->save($attivita);
            $this->mostraStatoOperazione(true, "Attività '" . $nome . "' creata con successo.", "crea-attivita", "Torna all'Attività");
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Errore di validazione: " . $e->getMessage(), "crea-attivita", "Torna all'Attività");
        }
    }

    // =========================================================================
    // 4. ABILITAZIONE ATTIVITA ALLENATORE
    // =========================================================================



    // =========================================================================
    // 5. RIMOZIONE CLIENTE
    // =========================================================================

    public function rimuoviCliente(): void      //recupera la palestra dall'amministratore e rimuove il cliente selezionato, gestendo eventuali errori
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->mostraStatoOperazione(false, "Accesso negato. Nessuna palestra associata all'utente.", "login", "Torna al Login");
            return;
        }
        $idCliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $cliente = $this->clienteRepo->findById($idCliente);
        if (!$cliente) {
            $this->mostraStatoOperazione(false, "Cliente non trovato.", "clienti", "Torna a Gestione Clienti");
            return;
        }
        if ($cliente->getPalestra() === null || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->mostraStatoOperazione(false, "Accesso negato. Il cliente non appartiene alla tua palestra.", "clienti", "Torna a Gestione Clienti");
            return;
        }
        $this->eseguiRimozioneCliente($cliente);
    }

    private function eseguiRimozioneCliente(Cliente $cliente): void
    {
        try {
            $nomeCompleto = $cliente->getNome() . " " . $cliente->getCognome();
            $this->rimuoviDipendenzeCliente($cliente);
            
            $cert = $cliente->getCertificatoMedico();
            $abb = $cliente->getAbbonamento();
            $isc = $cliente->getIscrizione();
            
            $this->scollegaEntitaUnoAUno($cliente, $cert, $abb, $isc);  //elimina la relazione
            $this->clienteRepo->delete($cliente);
            $this->rimuoviEntitaOrfane($cert, $abb, $isc);      //elimina le entità orfane
            
            $this->mostraStatoOperazione(true, "Rimozione del cliente " . $nomeCompleto . " avvenuta con successo.", "clienti", "Torna a Gestione Clienti");
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Impossibile eliminare il cliente: " . $e->getMessage(), "clienti", "Torna a Gestione Clienti");
        }
    }

    private function rimuoviDipendenzeCliente(Cliente $cliente): void
    {
        foreach ($cliente->getAttivitaPianificate() as $attivita) {     //per tutte le attività pianificate a cui il cliente è iscritto, rimuove l'iscrizione e decrementa il numero di prenotati
            $cliente->cancellaIscrizioneAttivita($attivita);
            $attivita->setPrenotati(max(0, $attivita->getPrenotati() - 1));
            AttivitaPianificataController::scorriCodaEnotifica($attivita, $this->codaAttesaRepo, $this->clienteRepo, $this->messaggioRepo);
        }
        $this->pulisciListeSecondarieCliente($cliente);
    }

    private function pulisciListeSecondarieCliente(Cliente $cliente): void
    {
        foreach ($this->codaAttesaRepo->findByCliente($cliente) as $c) {  //cancella il cliente da tutte le code d'attesa in cui è presente
            $this->codaAttesaRepo->delete($c);
        }
        foreach ($this->sessionePrivataRepo->findByCliente($cliente) as $s) {
            $this->sessionePrivataRepo->delete($s);
        }
        foreach ($this->parametriRepo->findByCliente($cliente) as $p) {
            $this->parametriRepo->delete($p);
        }
        $s = $this->schedaRepo->findByCliente($cliente);
        if ($s) {
            $cliente->setScheda(null);
            $this->schedaRepo->delete($s);
        }
    }


    private function scollegaEntitaUnoAUno(Cliente $cliente, ?CertificatoMedico $cert, ?AbbonamentoAttivo $abb, ?Iscrizione $isc): void
    {
        if ($cert) {
            $cliente->setCertificatoMedico(null);
        }
        if ($abb) {
            $cliente->setAbbonamento(null);
        }
        if ($isc) {
            $cliente->setIscrizione(null);
        }
        $this->entityManager->flush();
    }

    private function rimuoviEntitaOrfane(?CertificatoMedico $cert, ?AbbonamentoAttivo $abb, ?Iscrizione $isc): void
    {
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
    }

    // =========================================================================
    // 6. RIMOZIONE ALLENATORE
    // =========================================================================

    public function rimuoviAllenatore(): void       //recupera la palestra dall'amministratore e rimuove l'allenatore selezionato, gestendo eventuali errori
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->mostraStatoOperazione(false, "Accesso negato. Nessuna palestra associata all'utente.", "login", "Torna al Login");
            return;
        }
        $idAllenatore = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $allenatore = $this->allenatoreRepo->findById($idAllenatore);
        if (!$allenatore) {
            $this->mostraStatoOperazione(false, "Allenatore non trovato.", "allenatori", "Torna a Gestione Allenatori");
            return;
        }
        if ($allenatore->getPalestra()->getId() !== $palestra->getId()) {
            $this->mostraStatoOperazione(false, "L'allenatore indicato non appartiene alla tua palestra.", "allenatori", "Torna a Gestione Allenatori");
            return;
        }
        $this->eseguiRimozioneAllenatore($allenatore);
    }

    private function eseguiRimozioneAllenatore(Allenatore $allenatore): void
    {
        try {
            $nomeCompleto = $allenatore->getNome() . " " . $allenatore->getCognome();
            $this->allenatoreRepo->delete($allenatore);
            $this->mostraStatoOperazione(true, "Rimozione dell'allenatore " . $nomeCompleto . " avvenuta con successo.", "allenatori", "Torna a Gestione Allenatori");
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Impossibile eliminare l'allenatore: " . $e->getMessage(), "allenatori", "Torna a Gestione Allenatori");
        }
    }

    // =========================================================================
    // 7. RIMOZIONE ATTIVITA
    // =========================================================================


    // =========================================================================
    // HELPER PRIVATI GENERALI
    // =========================================================================

    private function recuperaPalestraAdmin(): ?Palestra     //recupera la palestra dall'utente loggato se autorizzato
    {
        $ruolo = $this->session->getLoggedUserRole();
        if ($ruolo !== 'amministratore' && $ruolo !== 'allenatore') {
            return null;
        }
        return AttivitaPianificataController::recuperaPalestraUtenteStatic(
            $this->session,
            $this->utenteRepo,
            $this->palestraRepo,
            $this->clienteRepo
        );
    }

    private function generaPasswordTemporanea(): string
    {
        return bin2hex(random_bytes(4));
    }

    private function inviaMailPasswordTemporanea(string $email, string $nome, string $password): bool
    {
        $oggetto = "Benvenuto in GymFly - Credenziali di Accesso";
        $messaggio = "Ciao $nome,\n\nil tuo account su GymFly è stato creato.\nEcco le tue credenziali temporanee:\n\nEmail: $email\nPassword: $password\n\nTi consigliamo di cambiare la password al primo accesso.\n\nSaluti,\nLo staff di GymFly";
        $headers = "From: no-reply@gymfly.com\r\nReply-To: support@gymfly.com\r\nContent-Type: text/plain; charset=utf-8";
        return @mail($email, $oggetto, $messaggio, $headers);
    }

    private function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void
    {
        $statusView = new VisualizzazioneViewSmarty();
        $statusView->mostraStatoOperazione($successo, $messaggio, $ritorno, $testoBottone);
    }
}
