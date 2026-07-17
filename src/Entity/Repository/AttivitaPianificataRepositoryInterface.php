<?php

namespace App\Entity\Repository;

use App\Entity\AttivitaPianificata;
use App\Entity\Attivita;
use App\Entity\Allenatore;
use App\Entity\Sala;
use App\Entity\Cliente;

/**
 * Contratto dichiarato in Entity.
 * Zero import da Doctrine.
 */
interface AttivitaPianificataRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?AttivitaPianificata;
    public function save(AttivitaPianificata $attivita): void;
    public function delete(AttivitaPianificata $attivita): void;

    /** @return AttivitaPianificata[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    /**
     * Trova tutte le attività pianificate in una data specifica.
     * Caso d'uso: visualizzare il calendario giornaliero.
     *
     * @return AttivitaPianificata[]
     */
    public function findByGiorno(\DateTimeImmutable $giorno): array;

    /**
     * Trova tutte le attività pianificate di un allenatore.
     * Caso d'uso: agenda dell'allenatore.
     *
     * @return AttivitaPianificata[]
     */
    public function findByAllenatore(Allenatore $allenatore): array;

    /**
     * Trova tutte le attività pianificate in una sala.
     * Caso d'uso: verificare disponibilità sala.
     *
     * @return AttivitaPianificata[]
     */
    public function findBySala(Sala $sala): array;

    /**
     * Trova tutte le attività pianificate a cui un cliente è iscritto.
     * Caso d'uso: visualizzare il calendario del cliente.
     *
     * @return AttivitaPianificata[]
     */
    public function findByCliente(Cliente $cliente): array;

    /**
     * Cerca un'attività pianificata per giorno, orario e sala.
     */
    public function findOneByGiornoOrarioAndSala(\DateTimeImmutable $giorno, int $orario, Sala $sala): ?AttivitaPianificata;
}