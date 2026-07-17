<?php

namespace App\Entity\Repository;

use App\Entity\Palestra;
use App\Entity\Sala;

interface SalaRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Sala;

    public function save(Sala $entity): void;

    public function delete(Sala $entity): void;

    /** @return Sala[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Query per palestra
    // -------------------------------------------------------------------------

    /**
     * Tutte le sale di una palestra.
     * Caso d'uso: elenco sale nella dashboard amministratore.
     *
     * @return Sala[]
     */
    public function findByPalestra(Palestra $palestra): array;

    // -------------------------------------------------------------------------
    // Unicità
    // -------------------------------------------------------------------------

    /**
     * Verifica se esiste già una sala con lo stesso nome nella palestra.
     * Caso d'uso: validazione prima della creazione/modifica.
     */
    public function existsByNomeAndPalestra(string $nome, Palestra $palestra): bool;
}