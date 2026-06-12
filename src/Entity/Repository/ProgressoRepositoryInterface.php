<?php

namespace App\Entity\Repository;

use App\Entity\Cliente;
use App\Entity\Esercizio;
use App\Entity\Progresso;

interface ProgressoRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base (find e delete lavorano sul tipo padre)
    // save è assente: ogni interfaccia figlia lo definisce con il tipo corretto
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Progresso;

    public function delete(Progresso $entity): void;

    /** @return Progresso[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Query per cliente
    // -------------------------------------------------------------------------

    /** @return Progresso[] */
    public function findByCliente(Cliente $cliente): array;

    /**
     * Curva di miglioramento di un cliente su un esercizio specifico,
     * ordinata per data crescente.
     *
     * @return Progresso[]
     */
    public function findByClienteAndEsercizio(Cliente $cliente, Esercizio $esercizio): array;

    // -------------------------------------------------------------------------
    // Query per esercizio
    // -------------------------------------------------------------------------

    /** @return Progresso[] */
    public function findByEsercizio(Esercizio $esercizio): array;

    // -------------------------------------------------------------------------
    // Query per intervallo di date
    // -------------------------------------------------------------------------

    /** @return Progresso[] */
    public function findByClienteInPeriodo(
        Cliente $cliente,
        \DateTimeImmutable $dal,
        \DateTimeImmutable $al
    ): array;

    // -------------------------------------------------------------------------
    // Ultimo progresso registrato
    // -------------------------------------------------------------------------

    public function findUltimoByClienteAndEsercizio(
        Cliente $cliente,
        Esercizio $esercizio
    ): ?Progresso;
}