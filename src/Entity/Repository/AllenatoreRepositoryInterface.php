<?php

namespace App\Entity\Repository;

use App\Entity\Allenatore;
use App\Entity\Attivita;
use App\Entity\Palestra;

/**
 * @method void save(\App\Entity\Utente $entity)
 * @method void delete(\App\Entity\Utente $entity)
 */
interface AllenatoreRepositoryInterface extends UtenteRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD tipizzato — override con tipo concreto
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Allenatore;

    // -------------------------------------------------------------------------
    // Lookup anagrafico
    // -------------------------------------------------------------------------

    public function findByEmail(string $email): ?Allenatore;

    public function findByCF(string $CF): ?Allenatore;

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
     * Caso d'uso: assegnare un allenatore a un'attività.
     *
     * @return Allenatore[]
     */
    public function findAbilitatiPerAttivita(Attivita $attivita): array;
}