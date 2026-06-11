<?php

namespace App\Entity\Repository;

use App\Entity\Amministratore;
use App\Entity\Palestra;

/**
 * Contratto dichiarato in Entity.
 * Zero import da Doctrine.
 */
interface AmministratoreRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Amministratore;
    public function save(Amministratore $amministratore): void;
    public function delete(Amministratore $amministratore): void;

    /** @return Amministratore[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    /**
     * Trova un amministratore dalla sua email.
     * Caso d'uso: login amministratore.
     */
    public function findByEmail(string $email): ?Amministratore;

    /**
     * Trova l'amministratore responsabile di una palestra.
     * Caso d'uso: identificare chi gestisce una palestra.
     */
    public function findByPalestra(Palestra $palestra): ?Amministratore;
}