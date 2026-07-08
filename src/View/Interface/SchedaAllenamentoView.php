<?php
namespace App\View\Interface;

interface SchedaAllenamentoView
{
    /**
     * Mostra la dashboard o l'elenco delle schede (o i moduli).
     */
    public function mostraTemplate(string $tplName, array $dati = []): void;

    /**
     * Mostra la pagina di stato operazione (successo o errore).
     */
    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null): void;
}
