<?php
namespace App\View\Interface;

interface AbbonamentiView
{
    public function mostraGestioneAbbonamento(array $dati): void;
    public function mostraErrore(string $messaggio): void;
    public function mostraConferma(string $messaggio, string $ritorno): void;
}
