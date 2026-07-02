<?php
namespace App\View\Interface;

interface AutenticazioneView 
{
    public function mostraFormLogin(): void;

    public function mostraFormRegistrazione(): void;

    /**
     * Richiede alla View di raccogliere i dati per il login (es. da $_POST).
     * @return array Associativo con 'email' e 'password'.
     */
    public function richiediCredenzialiLogin(): array;

    /**
     * Richiede alla View di raccogliere tutti i dati per la registrazione.
     * @return array Associativo con tutti i campi del form.
     */
    public function richiediDatiRegistrazione(): array;

    public function mostraStatoOperazione(bool $successo, string $messaggio): void;

    /**
     * Esegue un reindirizzamento HTTP verso la dashboard appropriata
     * in base al ruolo dell'utente.
     * @param string $ruolo Il ruolo dell'utente (es. 'cliente', 'amministratore').
     */
    public function reindirizzaDopoLogin(string $ruolo): void;
}