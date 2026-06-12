<?php

namespace App\Entity\Repository;

use App\Entity\Tipologia;

interface TipologiaRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Tipologia;

    public function save(Tipologia $entity): void;

    public function delete(Tipologia $entity): void;

    /** @return Tipologia[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Ricerca per nome
    // -------------------------------------------------------------------------

    /**
     * Ricerca per nome esatto (case-insensitive).
     * Caso d'uso: lookup prima di creare una tipologia per evitare duplicati.
     */
    public function findByNome(string $nomeTipologia): ?Tipologia;

    /**
     * Verifica se esiste già una tipologia con lo stesso nome.
     * Caso d'uso: validazione prima di save().
     */
    public function existsByNome(string $nomeTipologia): bool;
}