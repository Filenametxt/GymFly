<?php

namespace App\Entity\Repository;

use App\Entity\Amministratore;
use App\Entity\Palestra;

/**
 * @method void save(\App\Entity\Utente $entity)
 * @method void delete(\App\Entity\Utente $entity)
 */
interface AmministratoreRepositoryInterface extends UtenteRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD tipizzato — override con tipo concreto
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Amministratore;

    // -------------------------------------------------------------------------
    // Lookup anagrafico
    // -------------------------------------------------------------------------

    public function findByEmail(string $email): ?Amministratore;

    // -------------------------------------------------------------------------
    // Filtro per palestra
    // -------------------------------------------------------------------------

    /**
     * Trova l'amministratore responsabile di una palestra.
     * Caso d'uso: identificare chi gestisce una palestra.
     */
    public function findByPalestra(Palestra $palestra): ?Amministratore;
}