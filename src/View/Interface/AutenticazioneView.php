<?php
namespace App\View\Interface;

interface AutenticazioneView 
{
    public function mostraFormLogin(): void;
    public function mostraFormRegistrazione(): void;
    public function mostraSuccessoLogin(): void;
    public function mostraErroreLogin(string $messaggio): void;
    public function mostraSuccessoRegistrazione(): void;
    public function mostraErroreRegistrazione(string $messaggio): void;
    public function mostraConfermaRimozione(): void;
    public function mostraErroreRimozione(string $messaggio): void;
}