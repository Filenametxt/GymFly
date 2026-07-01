<?php
namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\View\Interface\AutenticazioneView;
use App\Foundation\Session;
use App\Enum\Sesso;
use App\Entity\Cliente;

class AutenticazioneController 
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepo,
        private AutenticazioneView $view,
        private Session $session
    ) {}

    public function login(): void
    {
        // Richiede i dati di login alla View
        $loginData = $this->view->richiediCredenzialiLogin();
        $email = $loginData['email'] ?? '';
        $password = $loginData['password'] ?? '';
        if (empty($email) || empty($password)) {
            $this->view->mostraStatoOperazione(false, "Tutti i campi sono obbligatori per il login.");
            return;
        }

        $cliente = $this->clienteRepo->findByEmail($email);

        if ($cliente === null || !$cliente->verificaPassword($password)) {
            $this->view->mostraStatoOperazione(false, "Credenziali errate.");
            return;
        }

        $this->session->setUtenteLoggato($cliente); // Assume che setUtenteLoggato gestisca il ruolo
        $this->view->mostraStatoOperazione(true, "Login effettuato con successo.");
    }

    public function logout(): void 
    {
        $this->session->logout();
        $this->view->mostraFormLogin();
    }

    public function registraCliente(): void
    {
        // Richiede i dati di registrazione alla View
        $dati = $this->view->richiediDatiRegistrazione();

        // Validazione dei campi obbligatori
        if (empty($dati['nome']) || empty($dati['cognome']) || empty($dati['email']) || empty($dati['cf']) || empty($dati['password']) || empty($dati['indirizzo']) || empty($dati['data_nascita']) || empty($dati['luogo_nascita']) || empty($dati['sesso']) || empty($dati['metodo_pagamento'])) {
            $this->view->mostraStatoOperazione(false, "Dati obbligatori mancanti per la registrazione.");
            return;
        }

        if ($this->clienteRepo->findByEmail($dati['email']) !== null) {
            $this->view->mostraStatoOperazione(false, "Email già registrata.");
            return;
        }

        try {
            // Conversione dei dati grezzi nei tipi richiesti dall'entità
            $dataDiNascita = new \DateTimeImmutable($dati['data_nascita']);
            $sesso = Sesso::from($dati['sesso']);

            // Creazione dell'entità Cliente con il costruttore corretto
            $nuovoCliente = new Cliente(
                $dati['nome'],
                $dati['cognome'],
                $dati['email'],
                $dati['cf'],
                $dati['indirizzo'],
                $sesso,
                $dataDiNascita,
                $dati['luogo_nascita'],
                $dati['indirizzo_domicilio'],
                $dati['metodo_pagamento'],
                $dati['password'],
                null, // profilePicture
                $dati['telefono']
            );

            $this->clienteRepo->save($nuovoCliente);
            $this->view->mostraStatoOperazione(true, "Registrazione effettuata con successo."); // Correzione: era una chiamata a un metodo obsoleto

        } catch (\InvalidArgumentException $e) {
            // Cattura errori di validazione dalle entità (es. CF malformato)
            $this->view->mostraStatoOperazione(false, "Dati non validi per la registrazione: " . $e->getMessage());
        } catch (\Throwable $e) {
            // Cattura altri errori (es. data non valida)
            $this->view->mostraStatoOperazione(false, "Si è verificato un errore durante la registrazione: " . $e->getMessage());
        }
    }

    public function rimuoviUtente(): void
    {
        // La logica di autorizzazione (es. controllo del ruolo) andrebbe implementata qui.
        // Dato che Session non fornisce il ruolo, per ora procediamo senza questo controllo.
        $idDaRimuovere = $this->view->richiediIdUtenteDaRimuovere();
        $utente = $this->clienteRepo->findById($idDaRimuovere);

        if (!$utente) {
            $this->view->mostraStatoOperazione(false, "Utente non trovato per la rimozione.");
            return;
        }

        $this->clienteRepo->delete($utente);
        $this->view->mostraStatoOperazione(true, "Utente rimosso con successo.");
    }
}