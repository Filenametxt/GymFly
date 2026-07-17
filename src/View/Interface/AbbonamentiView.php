<?php
namespace App\View\Interface;

interface AbbonamentiView
{
    public function mostraGestioneAbbonamento(array $dati): void;
    public function mostraErrore(string $messaggio, ?string $ritorno = 'login', ?string $testoBottone = 'Torna al login'): void;
    public function mostraConferma(string $messaggio, string $ritorno, ?string $testoBottone = null): void;
}
