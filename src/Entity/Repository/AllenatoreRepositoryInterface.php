<?php

namespace App\Entity\Repository;

use App\Entity\Allenatore;
use App\Entity\Palestra;
use App\Entity\Attivita;

/**
 * Contratto dichiarato in Entity.
 * Zero import da Doctrine.
 */
interface AllenatoreRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Allenatore;
    public function save(Allenatore $allenatore): void;
    public function delete(Allenatore $allenatore): void;

    /** @return Allenatore[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    /**
     * Trova un allenatore dalla sua email.
     * Caso d'uso: login.
     */
    public function findByEmail(string $email): ?Allenatore;

    /**
     * Trova tutti gli allenatori di una palestra.
     * Caso d'uso: gestione staff palestra.
     *
     * @return Allenatore[]
     */
    public function findByPalestra(Palestra $palestra): array;

    /**
     * Trova tutti gli allenatori abilitati per una certa attività.
     * Caso d'uso: assegnare un allenatore a un'attività pianificata.
     *
     * @return Allenatore[]
     */
    public function findAbilitatiPerAttivita(Attivita $attivita): array;
}