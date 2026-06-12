<?php

namespace App\Entity\Repository;

use App\Entity\DettaglioAllenamento;
use App\Entity\Allenamento;
use App\Entity\Esercizio;

interface DettaglioAllenamentoRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?DettaglioAllenamento;

    public function save(DettaglioAllenamento $entity): void;

    public function delete(DettaglioAllenamento $entity): void;

    /** @return DettaglioAllenamento[] */
    public function findAll(): array;

    // --- Metodi di dominio ---

    /**
     * Tutti i dettagli che compongono un allenamento, ordinati per id
     * (ordine di inserimento, che corrisponde all'ordine degli esercizi).
     *
     * @return DettaglioAllenamento[]
     */
    public function findByAllenamento(Allenamento $allenamento): array;

    /**
     * Tutti i dettagli che coinvolgono un dato esercizio.
     * Utile per capire in quanti allenamenti compare un esercizio
     * (es. prima di eliminarlo).
     *
     * @return DettaglioAllenamento[]
     */
    public function findByEsercizio(Esercizio $esercizio): array;

    /**
     * Trova il dettaglio specifico di un esercizio all'interno di un
     * allenamento. Utile per aggiornare serie/ripetizioni/carico senza
     * ricaricare l'intera collection.
     */
    public function findByAllenamentoAndEsercizio(
        Allenamento $allenamento,
        Esercizio   $esercizio,
    ): ?DettaglioAllenamento;

    /**
     * Verifica se un esercizio è già presente in un allenamento.
     * Usato prima di aggiungere un dettaglio per evitare duplicati.
     */
    public function existsInAllenamento(
        Allenamento $allenamento,
        Esercizio   $esercizio,
    ): bool;

    /**
     * Numero di esercizi (dettagli) presenti in un allenamento.
     */
    public function countByAllenamento(Allenamento $allenamento): int;
}