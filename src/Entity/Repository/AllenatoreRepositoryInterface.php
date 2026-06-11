<?php

namespace App\Entity\Repository;

use App\Entity\Allenatore;
use App\Entity\Attivita;
use App\Entity\Palestra;

interface AllenatoreRepositoryInterface extends UtenteRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD tipizzato — override con tipo concreto
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Allenatore;

    public function save(Allenatore $entity): void;

    public function delete(Allenatore $entity): void;

    /** @return Allenatore[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Lookup anagrafico
    // -------------------------------------------------------------------------

    public function findByEmail(string $email): ?Allenatore;

    // -------------------------------------------------------------------------
    // Filtro per palestra
    // -------------------------------------------------------------------------

    /**
     * Tutti gli allenatori di una palestra.
     * Caso d'uso: gestione staff palestra.
     *
     * @return Allenatore[]
     */
    public function findByPalestra(Palestra $palestra): array;

    // -------------------------------------------------------------------------
    // Abilitazioni
    // -------------------------------------------------------------------------

    /**
     * Allenatori abilitati per una certa attività.
     * Caso d'uso: assegnare un allenatore a un'attività pianificata.
     *
     * @return Allenatore[]
     */
    public function findAbilitatiPerAttivita(Attivita $attivita): array;
}