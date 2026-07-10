<?php
namespace App\View\Interface;

interface EserciziView
{
    /**
     * Mostra il form per la creazione, copia o modifica di un esercizio.
     *
     * @param array $dati
     */
    public function mostraFormEsercizio(array $dati): void;

    /**
     * Mostra la pagina di stato operazione (successo o errore).
     *
     * @param bool $successo
     * @param string $messaggio
     * @param string|null $ritorno
     */
    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null): void;
}
