<?php
namespace App\View\Interface;

interface VisualizzazioneUtentiView 
{
    public function mostraListaClienti(array $clientiData): void;
    public function mostraListaAllenatori(array $allenatoriData): void;
}