<?php

namespace App\Entity\Repository;

use App\Entity\Attivita;
use App\Entity\Allenatore;

/**
 * Contratto dichiarato in Entity.
 * Zero import da Doctrine.
 */
interface AttivitaRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Attivita;
    public function save(Attivita $attivita): void;
    public function delete(Attivita $attivita): void;

    /** @return Attivita[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    /**
     * Trova tutte le attività per cui un allenatore è abilitato.
     * Caso d'uso: mostrare cosa può condurre un allenatore.
     *
     * @return Attivita[]
     */
    public function findByAllenatore(Allenatore $allenatore): array;

    /**
     * Trova attività per nome (ricerca parziale).
     * Caso d'uso: ricerca attività nel catalogo.
     *
     * @return Attivita[]
     */
    public function findByNome(string $nome): array;

    /**
     * Verifica se esiste già un'attività con lo stesso nome.
     * Evita duplicati nel catalogo.
     */
    public function existsByNome(string $nome): bool;
}