<?php

namespace App\Entity\Repository;

use App\Entity\Abbonamento;

interface AbbonamentoRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Abbonamento;
    public function save(Abbonamento $abbonamento): void;
    public function delete(Abbonamento $abbonamento): void;

    /** @return Abbonamento[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    /**
     * Trova tutti gli abbonamenti di una certa tipologia.
     * Es. "mensile", "annuale", "trimestrale"
     *
     * @return Abbonamento[]
     */
    public function findByTipologia(string $tipologia): array;

    /**
     * Trova tutti gli abbonamenti di una certa categoria.
     * Es. "standard", "premium", "student"
     *
     * @return Abbonamento[]
     */
    public function findByCategoria(string $categoria): array;

    /**
     * Verifica se esiste già un abbonamento con la stessa tipologia e categoria.
     */
    public function existsByTipologiaAndCategoria(string $tipologia, string $categoria): bool;
}