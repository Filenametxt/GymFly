<?php
namespace App\View\Interface;

interface MessaggiView
{
    public function mostraBachecaMessaggi(array $dati): void;
    public function mostraErrore(string $messaggio): void;
    public function mostraConfermaInviato(string $messaggio): void;
}
