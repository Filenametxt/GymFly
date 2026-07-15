<?php
namespace App\View\Interface;

interface AmministratoreView 
{
    public function mostraFormCreaCliente(array $dati): void;

    public function mostraFormCreaAllenatore(array $dati): void;

    public function mostraFormCreaAttivita(array $dati): void;

    public function mostraFormAbilitaAttivita(array $dati): void;

    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void;
}
