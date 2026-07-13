<?php
namespace App\Control;

use App\View\Interface\AutenticazioneView;
use App\View\AutenticazioneViewSmarty;
use App\Foundation\Session;
use App\Enum\Sesso;
use App\Entity\Amministratore;
use App\Entity\Utente;
use App\Entity\Palestra;
use Doctrine\ORM\EntityManagerInterface;

class AutenticazioneController 
{
    private AutenticazioneView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->view = new AutenticazioneViewSmarty();
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormLogin();
            return;
        }

        // Richiede i dati di login alla View
        $loginData = $this->view->richiediCredenzialiLogin();
        $email = $loginData['email'] ?? '';
        $password = $loginData['password'] ?? '';
        if (empty($email) || empty($password)) {
            $this->view->mostraStatoOperazione(false, "Tutti i campi sono obbligatori per il login.");
            return;
        }

        $utente = $this->entityManager->getRepository(Utente::class)->findOneBy(['email' => $email]);

        if ($utente === null || !$utente->verificaPassword($password)) {
            $this->view->mostraStatoOperazione(false, "Credenziali errate.");
            return;
        }

        $this->session->setUtenteLoggato($utente);
        $ruolo = $utente->getRuolo();
        $this->view->reindirizzaDopoLogin($ruolo);
    }

    public function logout(): void 
    {
        $this->session->logout();
        $this->view->mostraFormLogin();
    }

    public function registraAmministratore(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormRegistrazione();
            return;
        }

        // Richiede i dati di registrazione alla View
        $dati = $this->view->richiediDatiRegistrazione();

        // Validazione dei campi obbligatori
        $campiObbligatori = [
            'nome', 'cognome', 'email', 'cf', 'password', 'indirizzo', 'sesso', // Dati Amministratore
            'nome_palestra', 'indirizzo_palestra', 'email_palestra', 'telefono_palestra' // Dati Palestra
        ];

        foreach ($campiObbligatori as $campo) {
            if (empty($dati[$campo])) {
                $this->view->mostraStatoOperazione(false, "Tutti i campi sono obbligatori per completare la registrazione.");
                return;
            }
        }

        $existingUser = $this->entityManager->getRepository(Utente::class)->findOneBy(['email' => $dati['email']]);
        if ($existingUser) {
            $this->view->mostraStatoOperazione(false, "L'email dell'amministratore è già registrata.");
            return;
        }

        try {
            $this->entityManager->beginTransaction();

            // Conversione dei dati grezzi nei tipi richiesti dall'entità
            $sesso = Sesso::from($dati['sesso']);

            $nuovoAmministratore = new Amministratore(
                $dati['nome'],
                $dati['cognome'],
                $dati['email'],
                $dati['cf'],
                $dati['indirizzo'],
                $sesso,
                $dati['password'],
                null, // profilePicture
                $dati['telefono']
            );

            // 2. Crea la Palestra, passando l'amministratore appena creato
            $nuovaPalestra = new Palestra(
                $dati['nome_palestra'],
                $dati['indirizzo_palestra'],
                $dati['email_palestra'],
                $dati['telefono_palestra'],
                $nuovoAmministratore // Associa l'amministratore
            );

            $this->entityManager->persist($nuovoAmministratore);
            $this->entityManager->persist($nuovaPalestra);
            $this->entityManager->flush(); // Salva tutto in una singola transazione
            $this->entityManager->commit();

            $this->view->mostraStatoOperazione(true, "Registrazione di amministratore e palestra effettuata con successo.");
        } catch (\InvalidArgumentException $e) {
            // Cattura errori di validazione dalle entità (es. CF malformato)
            $this->view->mostraStatoOperazione(false, "Dati non validi per la registrazione: " . $e->getMessage());
        } catch (\Throwable $e) {
            // Cattura altri errori (es. data non valida)
            $this->view->mostraStatoOperazione(false, "Si è verificato un errore durante la registrazione: " . $e->getMessage());
        }
    }

}