<?php
namespace App\View\Interface;

interface VisualizzazioneView
{
    public function mostraHome(): void;
    public function mostraDashboardAdmin(array $dati): void;
    public function mostraDashboardAllenatore(array $dati): void;
    public function mostraDashboardCliente(array $dati): void;
    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void;
    public function reindirizzaDopoLogin(string $ruolo): void;
}
