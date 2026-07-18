<?php
namespace App\View\Interface;

interface AmministratoreView 
{
    public function mostraFormCreaCliente(array $dati): void;

    public function mostraFormCreaAllenatore(array $dati): void;

    public function mostraFormCreaAttivita(array $dati): void;
}
