<?php

namespace App\Entity\Repository;

use App\Entity\Parametri;
use App\Entity\Cliente;

interface ParametriRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?Parametri;

    public function save(Parametri $entity): void;

    public function delete(Parametri $entity): void;

    /** @return Parametri[] */
    public function findAll(): array;

    // --- Filtro per cliente ---

    /**
     * Tutte le misurazioni di un cliente, dalla più recente.
     * Costituisce lo storico completo delle misure corporee.
     *
     * @return Parametri[]
     */
    public function findByCliente(Cliente $cliente): array;

    /**
     * L'ultima misurazione registrata per un cliente.
     * Usata per mostrare i parametri correnti nella dashboard.
     */
    public function findUltimaByCliente(Cliente $cliente): ?Parametri;

    /**
     * La prima misurazione registrata per un cliente.
     * Usata come baseline per calcolare i progressi nel tempo.
     */
    public function findPrimaByCliente(Cliente $cliente): ?Parametri;

    /**
     * Misurazioni di un cliente in un intervallo di date.
     * Utile per grafici di andamento su un periodo specifico.
     *
     * @return Parametri[]
     */
    public function findByClienteInPeriodo(
        Cliente              $cliente,
        \DateTimeImmutable   $dal,
        \DateTimeImmutable   $al,
    ): array;

    /**
     * Verifica se un cliente ha già una misurazione registrata
     * per una data specifica. Evita duplicati sullo stesso giorno.
     */
    public function existsByClienteAndData(
        Cliente            $cliente,
        \DateTimeImmutable $data,
    ): bool;

    /**
     * Numero totale di misurazioni registrate per un cliente.
     */
    public function countByCliente(Cliente $cliente): int;


    /**
     * Salva le nuove misure dell'utente (I parametri) 
     */
    public function salvaMisure(Parametri $parametri): void;

}