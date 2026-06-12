<?php

namespace App\Entity\Repository;

use App\Entity\Utente;

interface UtenteRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Utente;

    public function save(Utente $entity): void;

    public function delete(Utente $entity): void;

    /** @return Utente[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Lookup anagrafico — comuni a tutti i tipi di utente
    // -------------------------------------------------------------------------

    /**
     * Ricerca per email.
     * Caso d'uso: autenticazione generica, indipendente dal ruolo.
     */
    public function findByEmail(string $email): ?Utente;

    /**
     * Verifica unicità email prima della registrazione.
     */
    public function existsByEmail(string $email): bool;

    /**
     * Verifica unicità codice fiscale prima della registrazione.
     */
    public function existsByCF(string $CF): bool;
}