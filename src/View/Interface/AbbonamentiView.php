<?php
namespace App\View\Interface;

interface AbbonamentiView
{
    public function mostraGestioneAbbonamento(array $dati): void;
    public function redirect(string $url): void;
}
