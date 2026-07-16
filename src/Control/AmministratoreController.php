<?php
namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
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
use App\Foundation\Session;
use App\Enum\Sesso;
use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\Attivita;
use App\Entity\Palestra;
use Doctrine\ORM\EntityManagerInterface;

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
    }

    // =========================================================================
    // 1. CREAZIONE CLIENTE
    // =========================================================================

    public function creaCliente(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "dashboard-admin", "Torna alla Dashboard");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCreaCliente([]);
            return;
        }
        $this->eseguiCreazioneCliente($palestra);
    }

    private function eseguiCreazioneCliente(Palestra $palestra): void
    {
        $dati = $this->estraiDatiClientePost();
        if ($dati['nome'] === '' || $dati['cognome'] === '' || $dati['email'] === '' || $dati['cf'] === '' || $dati['indirizzo'] === '' || $dati['sessoVal'] === '' || $dati['dataNascitaStr'] === '' || $dati['luogoNascita'] === '' || $dati['metodoPagamento'] === '') {
            $this->view->mostraStatoOperazione(false, "Campi obbligatori mancanti.", "clienti", "Torna a Gestione Clienti");
            return;
        }
        if ($this->utenteRepo->findByEmail($dati['email'])) {
            $this->view->mostraStatoOperazione(false, "Email già associata ad un altro utente.", "clienti", "Torna a Gestione Clienti");
            return;
        }
        $this->salvaEInviaMailCliente($palestra, $dati);
    }

    private function estraiDatiClientePost(): array
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

    private function salvaEInviaMailCliente(Palestra $palestra, array $dati): void
    {
        try {
            $dataDiNascita = new \DateTimeImmutable($dati['dataNascitaStr']);
            if ($dataDiNascita > new \DateTimeImmutable()) {
                $this->view->mostraStatoOperazione(false, "La data di nascita non può essere futura.", "clienti", "Torna a Gestione Clienti");
                return;
            }
            $tempPassword = $this->generaPasswordTemporanea();
            $cliente = new Cliente($dati['nome'], $dati['cognome'], $dati['email'], $dati['cf'], $dati['indirizzo'], Sesso::from($dati['sessoVal']), $dataDiNascita, $dati['luogoNascita'], $dati['indirizzoDomicilio'], $dati['metodoPagamento'], $tempPassword, null, $dati['telefono']);
            $cliente->setPalestra($palestra);
            $this->clienteRepo->save($cliente);
            $invioOk = $this->inviaMailPasswordTemporanea($dati['email'], $dati['nome'], $tempPassword);
            $msg = "Cliente registrato con successo. " . ($invioOk ? "Le credenziali sono state inviate via email." : "Nota: SMTP locale non configurato. Password temporanea: " . $tempPassword);
            $this->view->mostraStatoOperazione(true, $msg, "clienti", "Torna a Gestione Clienti");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore durante la creazione: " . $e->getMessage(), "clienti", "Torna a Gestione Clienti");
        }
    }

    // =========================================================================
    // 2. CREAZIONE ALLENATORE
    // =========================================================================

    public function creaAllenatore(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "dashboard-admin", "Torna alla Dashboard");
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
        $nome = !empty($_POST['nome']) ? trim($_POST['nome']) : '';
        $cognome = !empty($_POST['cognome']) ? trim($_POST['cognome']) : '';
        $email = !empty($_POST['email']) ? trim($_POST['email']) : '';
        $cf = !empty($_POST['cf']) ? trim($_POST['cf']) : '';
        $indirizzo = !empty($_POST['indirizzo']) ? trim($_POST['indirizzo']) : '';
        $sessoVal = !empty($_POST['sesso']) ? trim($_POST['sesso']) : '';
        $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;

        if ($nome === '' || $cognome === '' || $email === '' || $cf === '' || $indirizzo === '' || $sessoVal === '') {
            $this->view->mostraStatoOperazione(false, "Campi obbligatori mancanti.", "allenatori", "Torna a Gestione Allenatori");
            return;
        }
        if ($this->utenteRepo->findByEmail($email)) {
            $this->view->mostraStatoOperazione(false, "Email già associata ad un altro utente.", "allenatori", "Torna a Gestione Allenatori");
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
            $this->view->mostraStatoOperazione(true, $msg, "allenatori", "Torna a Gestione Allenatori");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore durante la creazione: " . $e->getMessage(), "allenatori", "Torna a Gestione Allenatori");
        }
    }

    // =========================================================================
    // 3. CREAZIONE ATTIVITA
    // =========================================================================

    public function creaAttivita(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "dashboard-admin", "Torna alla Dashboard");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCreaAttivita([]);
            return;
        }
        $this->eseguiCreazioneAttivita();
    }

    private function eseguiCreazioneAttivita(): void
    {
        $nome = !empty($_POST['nome']) ? trim($_POST['nome']) : '';
        $descrizione = !empty($_POST['descrizione']) ? trim($_POST['descrizione']) : '';
        $maxPartecipanti = isset($_POST['max_partecipanti']) ? (int)$_POST['max_partecipanti'] : 0;

        if ($nome === '' || $descrizione === '' || $maxPartecipanti <= 0) {
            $this->view->mostraStatoOperazione(false, "Tutti i campi sono obbligatori e partecipanti > 0.", "crea-attivita", "Torna all'Attività");
            return;
        }
        if ($this->attivitaRepo->existsByNome($nome)) {
            $this->view->mostraStatoOperazione(false, "Attività già esistente.", "crea-attivita", "Torna all'Attività");
            return;
        }
        try {
            $attivita = new Attivita($nome, $descrizione, $maxPartecipanti);
            $this->attivitaRepo->save($attivita);
            $this->view->mostraStatoOperazione(true, "Attività '" . $nome . "' creata con successo.", "crea-attivita", "Torna all'Attività");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore di validazione: " . $e->getMessage(), "crea-attivita", "Torna all'Attività");
        }
    }

    // =========================================================================
    // 4. ABILITAZIONE ATTIVITA ALLENATORE
    // =========================================================================

    public function abilitaAttivitaAllenatore(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "dashboard-admin", "Torna alla Dashboard");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormAbilitaAttivita([
                'allenatori' => $this->allenatoreRepo->findByPalestra($palestra),
                'attivita' => $this->attivitaRepo->findAll()
            ]);
            return;
        }
        $this->eseguiAbilitazioneAllenatore($palestra);
    }

    private function eseguiAbilitazioneAllenatore(Palestra $palestra): void
    {
        $idAllenatore = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;
        $idAttivita = isset($_POST['id_attivita']) ? (int)$_POST['id_attivita'] : 0;
        $azione = isset($_POST['azione']) ? $_POST['azione'] : 'abilita';
        $allenatore = $this->allenatoreRepo->findById($idAllenatore);
        $attivita = $this->attivitaRepo->findById($idAttivita);

        if (!$allenatore || !$attivita) {
            $this->view->mostraStatoOperazione(false, "Allenatore o Attività non validi.", "allenatori", "Torna a Gestione Allenatori");
            return;
        }
        if ($allenatore->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "L'allenatore non appartiene al tuo centro sportivo.", "allenatori", "Torna a Gestione Allenatori");
            return;
        }
        $this->salvaAbilitazioneAllenatore($allenatore, $attivita, $azione);
    }

    private function salvaAbilitazioneAllenatore(Allenatore $allenatore, Attivita $attivita, string $azione): void
    {
        try {
            if ($azione === 'abilita') {
                $allenatore->addAbilitazione($attivita);
                $msg = "Allenatore abilitato con successo all'attività " . $attivita->getNome() . ".";
            } else {
                $allenatore->removeAbilitazione($attivita);
                $msg = "Abilitazione all'attività " . $attivita->getNome() . " rimossa con successo.";
            }
            $this->entityManager->flush();
            $this->view->mostraStatoOperazione(true, $msg, "allenatori", "Torna a Gestione Allenatori");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), "allenatori", "Torna a Gestione Allenatori");
        }
    }

    // =========================================================================
    // 5. RIMOZIONE CLIENTE
    // =========================================================================

    public function rimuoviCliente(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "dashboard-admin", "Torna alla Dashboard");
            return;
        }
        $idCliente = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $cliente = $this->clienteRepo->findById($idCliente);
        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato.", "clienti", "Torna a Gestione Clienti");
            return;
        }
        if ($cliente->getPalestra() === null || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Il cliente non appartiene alla tua palestra.", "clienti", "Torna a Gestione Clienti");
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
            
            $this->scollegaEntitaUnoAUno($cliente, $cert, $abb, $isc);
            $this->clienteRepo->delete($cliente);
            $this->rimuoviEntitaOrfane($cert, $abb, $isc);
            
            $this->view->mostraStatoOperazione(true, "Rimozione del cliente " . $nomeCompleto . " avvenuta con successo.", "clienti", "Torna a Gestione Clienti");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile eliminare il cliente: " . $e->getMessage(), "clienti", "Torna a Gestione Clienti");
        }
    }

    private function rimuoviDipendenzeCliente(Cliente $cliente): void
    {
        foreach ($cliente->getAttivitaPianificate() as $attivita) {
            $cliente->cancellaIscrizioneAttivita($attivita);
            $attivita->setPrenotati(max(0, $attivita->getPrenotati() - 1));
            $this->scorriCodaAttivita($attivita);
        }
        $this->pulisciListeSecondarieCliente($cliente);
    }

    private function pulisciListeSecondarieCliente(Cliente $cliente): void
    {
        foreach ($this->codaAttesaRepo->findByCliente($cliente) as $c) {
            $this->codaAttesaRepo->delete($c);
        }
        foreach ($this->sessionePrivataRepo->findByCliente($cliente) as $s) {
            $this->sessionePrivataRepo->delete($s);
        }
        foreach ($this->parametriRepo->findByCliente($cliente) as $p) {
            $this->parametriRepo->delete($p);
        }
        foreach ($this->schedaRepo->findByCliente($cliente) as $s) {
            $cliente->setScheda(null);
            $this->schedaRepo->delete($s);
        }
    }

    private function scorriCodaAttivita($attivita): void
    {
        $codaPrimo = $this->codaAttesaRepo->findPrimoInCoda($attivita);
        if ($codaPrimo) {
            $clienteScelto = $codaPrimo->getCliente();
            $clienteScelto->iscriviAAttivita($attivita);
            $attivita->setPrenotati($attivita->getPrenotati() + 1);
            $this->codaAttesaRepo->delete($codaPrimo);

            $mittente = $attivita->getAllenatore();
            $oggettoMsg = "Iscrizione automatica all'attività";
            $contenutoMsg = "Ciao " . $clienteScelto->getNome() . ",\n\nti informiamo che si è liberato un posto e sei stato iscritto automaticamente all'attività: " . $attivita->getAttivita()->getNome() . " in data " . $attivita->getGiorno()->format('d/m/Y') . " alle ore " . $attivita->getOrario() . ":00.\n\nSaluti,\nLo staff di GymFly";
            
            $messaggio = new \App\Entity\Messaggio($mittente, $oggettoMsg, $contenutoMsg);
            $messaggio->aggiungiDestinatario($clienteScelto);
            $this->entityManager->persist($messaggio);

            $headers = "From: no-reply@gymfly.com\r\nReply-To: support@gymfly.com\r\nContent-Type: text/plain; charset=utf-8";
            @mail($clienteScelto->getEmail(), $oggettoMsg, $contenutoMsg, $headers);
        }
    }

    private function scollegaEntitaUnoAUno(Cliente $cliente, $cert, $abb, $isc): void
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

    private function rimuoviEntitaOrfane($cert, $abb, $isc): void
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

    public function rimuoviAllenatore(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "dashboard-admin", "Torna alla Dashboard");
            return;
        }
        $idAllenatore = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $allenatore = $this->allenatoreRepo->findById($idAllenatore);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.", "allenatori", "Torna a Gestione Allenatori");
            return;
        }
        if ($allenatore->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "L'allenatore indicato non appartiene alla tua palestra.", "allenatori", "Torna a Gestione Allenatori");
            return;
        }
        $this->eseguiRimozioneAllenatore($allenatore);
    }

    private function eseguiRimozioneAllenatore(Allenatore $allenatore): void
    {
        try {
            $nomeCompleto = $allenatore->getNome() . " " . $allenatore->getCognome();
            $this->allenatoreRepo->delete($allenatore);
            $this->view->mostraStatoOperazione(true, "Rimozione dell'allenatore " . $nomeCompleto . " avvenuta con successo.", "allenatori", "Torna a Gestione Allenatori");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile eliminare l'allenatore: " . $e->getMessage(), "allenatori", "Torna a Gestione Allenatori");
        }
    }

    // =========================================================================
    // 7. RIMOZIONE ATTIVITA
    // =========================================================================

    public function rimuoviAttivita(): void
    {
        $palestra = $this->recuperaPalestraAdmin();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "dashboard-admin", "Torna alla Dashboard");
            return;
        }
        $idAttivita = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $attivita = $this->attivitaRepo->findById($idAttivita);
        if (!$attivita) {
            $this->view->mostraStatoOperazione(false, "Attività non trovata.", "crea-attivita", "Torna all'Attività");
            return;
        }
        $this->eseguiRimozioneAttivita($attivita);
    }

    private function eseguiRimozioneAttivita(Attivita $attivita): void
    {
        try {
            $nomeAttivita = $attivita->getNome();
            $this->attivitaRepo->delete($attivita);
            $this->view->mostraStatoOperazione(true, "Attività '" . $nomeAttivita . "' rimossa con successo dal catalogo.", "crea-attivita", "Torna all'Attività");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile rimuovere l'attività: " . $e->getMessage(), "crea-attivita", "Torna all'Attività");
        }
    }

    // =========================================================================
    // HELPER PRIVATI GENERALI
    // =========================================================================

    private function recuperaPalestraAdmin(): ?Palestra
    {
        $idAdmin = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAdmin || $ruolo !== 'amministratore') {
            return null;
        }
        $admin = $this->utenteRepo->findById($idAdmin);
        if (!$admin) {
            return null;
        }
        return $this->palestraRepo->findByAmministratore($admin);
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
}
