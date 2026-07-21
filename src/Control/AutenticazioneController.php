<?php
namespace App\Control;

use App\View\Interface\AutenticazioneView;
use App\View\AutenticazioneViewSmarty;
use App\Foundation\Session;
use App\Foundation\Utility\HTTPMethods;
use App\Enum\Sesso;
use App\Entity\Amministratore;
use App\Entity\Palestra;
use App\Entity\Repository\UtenteRepositoryInterface;
use App\Entity\Repository\AmministratoreRepositoryInterface;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;
use App\Foundation\Persistence\Repository\DoctrineAmministratoreRepository;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use Doctrine\ORM\EntityManagerInterface;

class AutenticazioneController 
{
    private AutenticazioneView $view;
    private UtenteRepositoryInterface $utenteRepo;
    private AmministratoreRepositoryInterface $amministratoreRepo;
    private PalestraRepositoryInterface $palestraRepo;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->view = new AutenticazioneViewSmarty();
        $this->utenteRepo = new DoctrineUtenteRepository($this->entityManager);
        $this->amministratoreRepo = new DoctrineAmministratoreRepository($this->entityManager);
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
    }

    // =========================================================================
    // 1. LOGIN (/login)
    // =========================================================================

    public function login(): void   //gestisce la richiesta di login
    {
        if (HTTPMethods::method() === 'GET') {    //mostra il form di login se la richiesta è GET
            $this->view->mostraFormLogin();
            return;
        }
        $this->eseguiLogin();
    }

    private function eseguiLogin(): void     //esegue il login dell'utente, controllando le credenziali e impostando la sessione
    {
        $loginData = $this->view->richiediCredenzialiLogin();   //richiede le credenziali di login dalla view
        $email = $loginData['email'] ?? '';
        $password = $loginData['password'] ?? '';
        if (empty($email) || empty($password)) {
            $this->view->mostraStatoOperazione(false, "Tutti i campi sono obbligatori per il login.", "login", "Torna al Login");
            return;
        }
        $utente = $this->utenteRepo->findByEmail($email);
        if ($utente === null || !$utente->verificaPassword($password)) {   //verifica se la password in chiaro corrisponde all'hash memorizzato nel database
            $this->view->mostraStatoOperazione(false, "Credenziali errate.", "login", "Torna al Login");
            return;
        }
        $this->session->setUtenteLoggato($utente);
        $this->view->reindirizzaDopoLogin($utente->getRuolo());
    }

    // =========================================================================
    // 2. LOGOUT (/logout)
    // =========================================================================

    public function logout(): void 
    {
        $this->session->logout();
        $this->view->mostraFormLogin();
    }

    // =========================================================================
    // 3. REGISTRAZIONE AMMINISTRATORE (/registrazione)
    // =========================================================================

    public function registraAmministratore(): void
    {
        if (HTTPMethods::method() === 'GET') {
            $this->view->mostraFormRegistrazione();
            return;
        }
        $this->eseguiRegistrazione();
    }

    private function eseguiRegistrazione(): void
    {
        $dati = $this->view->richiediDatiRegistrazione();
        if (!$this->validaCampiObbligatori($dati)) {        //controlla se tutti i campi obbligatori sono stati compilati
            $this->view->mostraStatoOperazione(false, "Tutti i campi sono obbligatori per completare la registrazione.");
            return;
        }
        if ($this->utenteRepo->findByEmail($dati['email'] ?? '')) {
            $this->view->mostraStatoOperazione(false, "L'email dell'amministratore è già registrata.");
            return;
        }
        $this->salvaAmministratoreEPalestra($dati);
    }

    private function validaCampiObbligatori(array $dati): bool
    {
        $campi = [
            'nome', 'cognome', 'email', 'cf', 'password', 'indirizzo', 'sesso',
            'nome_palestra', 'indirizzo_palestra', 'email_palestra', 'telefono_palestra'
        ];
        foreach ($campi as $campo) {
            if (empty($dati[$campo])) {
                return false;
            }
        }
        return true;
    }

    private function salvaAmministratoreEPalestra(array $dati): void
    {
        try {
            $this->entityManager->beginTransaction();   //inizia una transazione per salvare sia l'amministratore che la palestra
            $admin = new Amministratore(
                $dati['nome'], $dati['cognome'], $dati['email'], $dati['cf'],
                $dati['indirizzo'], Sesso::from($dati['sesso']), $dati['password'], null, $dati['telefono']
            );
            $palestra = new Palestra(
                $dati['nome_palestra'], $dati['indirizzo_palestra'], $dati['email_palestra'], $dati['telefono_palestra'], $admin
            );
            $this->amministratoreRepo->save($admin);
            $this->palestraRepo->save($palestra);
            $this->entityManager->commit();    //committa la transazione se tutto è andato a buon fine
            $this->view->mostraStatoOperazione(true, "Registrazione di amministratore e palestra effettuata con successo.");
        } catch (\Throwable $e) {
            $this->effettuaRollback();
            $prefix = ($e instanceof \InvalidArgumentException) ? "Dati non validi: " : "Errore: ";   //aggiunge un prefisso al messaggio di errore in base al tipo di eccezione
            $this->view->mostraStatoOperazione(false, $prefix . $e->getMessage());
        }
    }

    private function effettuaRollback(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {    //controlla se la transazione è attiva prima di effettuare il rollback
            $this->entityManager->rollback();
        }
    }
}