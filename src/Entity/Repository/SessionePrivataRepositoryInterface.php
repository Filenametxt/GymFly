<?php

namespace App\Entity\Repository;

use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\SessionePrivata;

interface SessionePrivataRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // findById assente: l'entità ha chiave primaria composta, non surrogata.
    // -------------------------------------------------------------------------

    /**
     * Ricerca per chiave primaria composta (allenatore, oraInizio, oraFine).
     */
    public function findByChiave(
        Allenatore         $allenatore,
        \DateTimeImmutable $oraInizio,
        \DateTimeImmutable $oraFine
    ): ?SessionePrivata;

    public function save(SessionePrivata $entity): void;

    public function delete(SessionePrivata $entity): void;

    /** @return SessionePrivata[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Query per allenatore
    // -------------------------------------------------------------------------

    /**
     * Tutte le sessioni di un allenatore, ordinate per data e ora inizio.
     * Caso d'uso: calendario sessioni dell'allenatore.
     *
     * @return SessionePrivata[]
     */
    public function findByAllenatore(Allenatore $allenatore): array;

    // -------------------------------------------------------------------------
    // Query per cliente
    // -------------------------------------------------------------------------

    /**
     * Tutte le sessioni di un cliente, ordinate per data e ora inizio.
     * Caso d'uso: storico sessioni nella vista cliente.
     *
     * @return SessionePrivata[]
     */
    public function findByCliente(Cliente $cliente): array;

    // -------------------------------------------------------------------------
    // Query per data
    // -------------------------------------------------------------------------

    /**
     * Tutte le sessioni in una data specifica.
     * Caso d'uso: vista giornaliera del calendario palestra.
     *
     * @return SessionePrivata[]
     */
    public function findByData(\DateTimeImmutable $data): array;

    // -------------------------------------------------------------------------
    // Controllo sovrapposizioni
    // -------------------------------------------------------------------------

    /**
     * Verifica se un allenatore ha già una sessione sovrapposta
     * nell'intervallo (oraInizio, oraFine) in una certa data.
     * Caso d'uso: validazione prima di creare/spostare una sessione.
     */
    public function existsSovrapposizioneAllenatore(
        Allenatore         $allenatore,
        \DateTimeImmutable $data,
        \DateTimeImmutable $oraInizio,
        \DateTimeImmutable $oraFine
    ): bool;

    /**
     * Verifica se un cliente ha già una sessione sovrapposta
     * nell'intervallo (oraInizio, oraFine) in una certa data.
     * Caso d'uso: validazione prima di creare/spostare una sessione.
     */
    public function existsSovrapposizioneCliente(
        Cliente            $cliente,
        \DateTimeImmutable $data,
        \DateTimeImmutable $oraInizio,
        \DateTimeImmutable $oraFine
    ): bool;
}