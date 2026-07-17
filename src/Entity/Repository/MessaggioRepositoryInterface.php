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

    // --- Posta in arrivo ---

    /**
     * Tutti i messaggi ricevuti da un utente (tabella RICEVE), dal più recente.
     *
     * @return Messaggio[]
     */
    public function findByDestinatario(Utente $destinatario): array;

}