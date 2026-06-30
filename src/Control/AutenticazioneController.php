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
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->view->mostraErroreLogin("Tutti i campi sono obbligatori.");
            return;
        }

        $cliente = $this->clienteRepo->findByEmail($email);

        if ($cliente === null || !$cliente->verificaPassword($password)) {
            $this->view->mostraErroreLogin("Credenziali errate.");
            return;
        }

        $this->session->setUtenteLoggato($cliente);
        $this->view->mostraSuccessoLogin();
    }

    public function logout(): void 
    {
        $this->session->logout();
        $this->view->mostraFormLogin();
    }

    public function registraCliente(): void 
    {
        // Raccolta completa dei dati dal form di registrazione
        $nome = $_POST['nome'] ?? null;
        $cognome = $_POST['cognome'] ?? null;
        $email = $_POST['email'] ?? null;
        $cf = $_POST['cf'] ?? null;
        $password = $_POST['password'] ?? null;
        $indirizzo = $_POST['indirizzo'] ?? null; // Indirizzo di residenza
        $metodoPagamento = $_POST['metodo_pagamento'] ?? null;
        $dataNascitaStr = $_POST['data_nascita'] ?? null; // es. "1990-01-15"
        $luogoNascita = $_POST['luogo_nascita'] ?? null;
        $sessoStr = $_POST['sesso'] ?? null; // es. "MALE" o "FEMALE"
        $indirizzoDomicilio = $_POST['indirizzo_domicilio'] ?? null;
        $telefono = $_POST['telefono'] ?? null;

        // Validazione dei campi obbligatori
        if (empty($nome) || empty($cognome) || empty($email) || empty($cf) || empty($password) || empty($indirizzo) || empty($dataNascitaStr) || empty($luogoNascita) || empty($sessoStr) || empty($metodoPagamento)) {
            $this->view->mostraErroreRegistrazione("Dati obbligatori mancanti.");
            return;
        }

        if ($this->clienteRepo->findByEmail($email) !== null) {
            $this->view->mostraErroreRegistrazione("Email già registrata.");
            return;
        }

        try {
            // Conversione dei dati grezzi nei tipi richiesti dall'entità
            $dataDiNascita = new \DateTimeImmutable($dataNascitaStr);
            $sesso = Sesso::from($sessoStr);

            // Creazione dell'entità Cliente con il costruttore corretto
            $nuovoCliente = new Cliente(
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
                $password,
                null, // profilePicture
                $telefono
            );

            $this->clienteRepo->save($nuovoCliente);
            $this->view->mostraSuccessoRegistrazione();

        } catch (\InvalidArgumentException $e) {
            // Cattura errori di validazione dalle entità (es. CF malformato)
            $this->view->mostraErroreRegistrazione("Dati non validi: " . $e->getMessage());
        } catch (\Throwable $e) {
            // Cattura altri errori (es. data non valida)
            $this->view->mostraErroreRegistrazione("Si è verificato un errore durante la registrazione: " . $e->getMessage());
        }
    }

    public function rimuoviUtente(): void 
    {
        // La logica di autorizzazione (es. controllo del ruolo) andrebbe implementata qui.
        // Dato che Session non fornisce il ruolo, per ora procediamo senza questo controllo.

        $idDaRimuovere = isset($_POST['id_utente']) ? (int)$_POST['id_utente'] : 0;
        $utente = $this->clienteRepo->findById($idDaRimuovere);

        if (!$utente) {
            $this->view->mostraErroreRimozione("Utente non trovato.");
            return;
        }

        $this->clienteRepo->delete($utente);
        $this->view->mostraConfermaRimozione();
    }
}