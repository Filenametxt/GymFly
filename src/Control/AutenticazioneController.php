<?php
namespace App\Control;

use App\View\Interface\AutenticazioneView;
use App\View\AutenticazioneViewSmarty;
use App\Foundation\Session;
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

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormLogin();
            return;
        }
        $this->eseguiLogin();
    }

    private function eseguiLogin(): void
    {
        $loginData = $this->view->richiediCredenzialiLogin();
        $email = $loginData['email'] ?? '';
        $password = $loginData['password'] ?? '';
        if (empty($email) || empty($password)) {
            $this->view->mostraStatoOperazione(false, "Tutti i campi sono obbligatori per il login.", "login", "Torna al Login");
            return;
        }
        $utente = $this->utenteRepo->findByEmail($email);
        if ($utente === null || !$utente->verificaPassword($password)) {
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
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormRegistrazione();
            return;
        }
        $this->eseguiRegistrazione();
    }

    private function eseguiRegistrazione(): void
    {
        $dati = $this->view->richiediDatiRegistrazione();
        if (!$this->validaCampiObbligatori($dati)) {
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
            $this->entityManager->beginTransaction();
            $admin = new Amministratore(
                $dati['nome'], $dati['cognome'], $dati['email'], $dati['cf'],
                $dati['indirizzo'], Sesso::from($dati['sesso']), $dati['password'], null, $dati['telefono']
            );
            $palestra = new Palestra(
                $dati['nome_palestra'], $dati['indirizzo_palestra'], $dati['email_palestra'], $dati['telefono_palestra'], $admin
            );
            $this->amministratoreRepo->save($admin);
            $this->palestraRepo->save($palestra);
            $this->entityManager->commit();
            $this->view->mostraStatoOperazione(true, "Registrazione di amministratore e palestra effettuata con successo.");
        } catch (\Throwable $e) {
            $this->effettuaRollback();
            $prefix = ($e instanceof \InvalidArgumentException) ? "Dati non validi: " : "Errore: ";
            $this->view->mostraStatoOperazione(false, $prefix . $e->getMessage());
        }
    }

    private function effettuaRollback(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
    }
}