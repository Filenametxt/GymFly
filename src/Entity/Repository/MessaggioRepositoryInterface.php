<?php

namespace App\Entity\Repository;

use App\Entity\Messaggio;
use App\Entity\Utente;

interface MessaggioRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?Messaggio;

    public function save(Messaggio $entity): void;

    public function delete(Messaggio $entity): void;

    /** @return Messaggio[] */
    public function findAll(): array;

    // --- Posta in uscita ---

    /**
     * Tutti i messaggi inviati da un utente, dal più recente.
     *
     * @return Messaggio[]
     */
    public function findByMittente(Utente $mittente): array;

    /**
     * Numero di messaggi inviati da un utente.
     * Utile per badge / statistiche senza caricare le entità.
     */
    public function countByMittente(Utente $mittente): int;

    // --- Posta in arrivo ---

    /**
     * Tutti i messaggi ricevuti da un utente (tabella RICEVE), dal più recente.
     *
     * @return Messaggio[]
     */
    public function findByDestinatario(Utente $destinatario): array;

    /**
     * Numero di messaggi ricevuti da un utente.
     */
    public function countByDestinatario(Utente $destinatario): int;

    // --- Ricerca contenuto ---

    /**
     * Ricerca full-text sull'oggetto del messaggio.
     * Utile per la barra di ricerca della casella.
     *
     * @return Messaggio[]
     */
    public function findByOggettoContaining(string $partial): array;

    /**
     * Messaggi inviati da un mittente verso uno specifico destinatario.
     * Utile per visualizzare la conversazione tra due utenti.
     *
     * @return Messaggio[]
     */
    public function findConversazione(Utente $mittente, Utente $destinatario): array;
}