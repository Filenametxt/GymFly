<?php
namespace App\View\Interface;

interface ReportView
{
    public function mostraReport(array $dati): void;
    public function mostraErrore(string $messaggio): void;
}
