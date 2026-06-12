<?php

namespace App\Entity\Repository;

use App\Entity\Attrezzatura;

interface AttrezzaturaRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?Attrezzatura;

    public function save(Attrezzatura $entity): void;

    public function delete(Attrezzatura $entity): void;

    /** @return Attrezzatura[] */
    public function findAll(): array;

    // --- Metodi di dominio ---

    /**
     * Cerca un'attrezzatura per nome esatto (case-insensitive).
     * Utile per evitare duplicati prima di un salvataggio.
     */
    public function findByNome(string $nome): ?Attrezzatura;

    /**
     * Verifica se esiste già un'attrezzatura con quel nome.
     * Più leggero di findByNome quando serve solo il bool.
     */
    public function existsByNome(string $nome): bool;

    /**
     * Ricerca parziale per nome — utile per autocomplete / ricerca live.
     *
     * @return Attrezzatura[]
     */
    public function findByNomeContaining(string $partial): array;
}