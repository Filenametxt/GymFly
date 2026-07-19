<?php
namespace App\View\Interface;

interface AttivitaPianificataView
{
    public function mostraCalendario(array $dati): void;
    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void;
}
