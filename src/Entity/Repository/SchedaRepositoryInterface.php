<?php

namespace App\Entity\Repository;

use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\Scheda;
use App\Entity\Palestra;

interface SchedaRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Scheda;

    public function save(Scheda $entity): void;

    public function delete(Scheda $entity): void;

    /** @return Scheda[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Query per cliente
    // -------------------------------------------------------------------------

    /**
     * Tutte le schede assegnate a un cliente, ordinate per data inizio
     * discendente (la più recente per prima).
     * Caso d'uso: storico schede nella vista cliente.
     *
     * @return Scheda[]
     */
    public function findByCliente(Cliente $cliente): array;

    /**
     * La scheda attualmente attiva per un cliente (data_inizio <= oggi <= data_fine).
     * Restituisce null se il cliente non ha schede attive.
     * Caso d'uso: caricamento scheda corrente nella dashboard cliente.
     */
    public function findAttivaByCliente(Cliente $cliente): ?Scheda;

    /**
     * Schede scadute di un cliente (data_fine < oggi).
     * Caso d'uso: archivio storico allenamenti.
     *
     * @return Scheda[]
     */
    public function findScaduteByCliente(Cliente $cliente): array;

    // -------------------------------------------------------------------------
    // Query per allenatore
    // -------------------------------------------------------------------------

    /**
     * Tutte le schede create da un allenatore.
     * Caso d'uso: pannello allenatore — riepilogo schede assegnate.
     *
     * @return Scheda[]
     */
    public function findByAllenatore(Allenatore $allenatore): array;

    /**
     * Schede attive gestite da un allenatore (data_inizio <= oggi <= data_fine).
     * Caso d'uso: monitoraggio clienti attivi dell'allenatore.
     *
     * @return Scheda[]
     */
    public function findAttiveByAllenatore(Allenatore $allenatore): array;

    // -------------------------------------------------------------------------
    // Query per scadenza imminente
    // -------------------------------------------------------------------------

    /**
     * Schede in scadenza entro i prossimi $giorni giorni.
     * Caso d'uso: notifiche/reminder per allenatore o cliente.
     *
     * @return Scheda[]
     */
    public function findInScadenza(int $giorni): array;

    /**
     * Tutte le schede dei clienti appartenenti a una specifica palestra.
     *
     * @return Scheda[]
     */
    public function findByPalestra(Palestra $palestra): array;

    /**
     * Altre schede attive o storiche dei clienti della palestra escludendo la scheda corrente.
     *
     * @return Scheda[]
     */
    public function findAltreByPalestra(Palestra $palestra, int $escludiSchedaId): array;
}
