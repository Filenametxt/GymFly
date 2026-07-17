<?php
namespace App\View\Interface;

interface MessaggiView
{
    public function mostraBachecaMessaggi(array $dati): void;
    public function mostraErrore(string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void;
    public function mostraConfermaInviato(string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void;
}
