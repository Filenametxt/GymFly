<?php

namespace App\Entity\Repository;

use App\Entity\GruppoMuscolare;
use App\Entity\Esercizio;

interface GruppoMuscolareRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?GruppoMuscolare;

    public function save(GruppoMuscolare $entity): void;

    public function delete(GruppoMuscolare $entity): void;

    /** @return GruppoMuscolare[] */
    public function findAll(): array;

    // --- Metodi di dominio ---

    /**
     * Cerca un gruppo muscolare per nome esatto (case-insensitive).
     * Utile per evitare duplicati prima di un salvataggio.
     */
    public function findByNome(string $nome): ?GruppoMuscolare;

    /**
     * Verifica se esiste già un gruppo muscolare con quel nome.
     * Più leggero di findByNome quando serve solo il bool.
     */
    public function existsByNome(string $nome): bool;

    /**
     * Ricerca parziale per nome — utile per autocomplete.
     *
     * @return GruppoMuscolare[]
     */
    public function findByNomeContaining(string $partial): array;

    /**
     * Tutti i gruppi muscolari allenati da un dato esercizio.
     * Attraversa la N-N ALLENA (Esercizio owner, GruppoMuscolare inverso).
     *
     * @return GruppoMuscolare[]
     */
    public function findByEsercizio(Esercizio $esercizio): array;

    /**
     * Gruppi muscolari che non hanno ancora alcun esercizio associato.
     * Utile per rilevare gruppi "orfani" in fase di gestione catalogo.
     *
     * @return GruppoMuscolare[]
     */
    public function findSenzaEsercizi(): array;
}