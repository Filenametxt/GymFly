<?php

namespace App\Entity\Repository;

use App\Entity\Cliente;
use App\Entity\Palestra;
use App\Entity\AttivitaPianificata;

interface ClienteRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?Cliente;

    public function save(Cliente $entity): void;

    public function delete(Cliente $entity): void;

    /** @return Cliente[] */
    public function findAll(): array;

    // --- Lookup anagrafico ---

    public function findByEmail(string $email): ?Cliente;

    public function findByCF(string $CF): ?Cliente;

    public function existsByEmail(string $email): bool;

    public function existsByCF(string $CF): bool;

    // --- Filtro per palestra ---

    /**
     * Tutti i clienti iscritti a una palestra.
     *
     * @return Cliente[]
     */
    public function findByPalestra(Palestra $palestra): array;

    // --- Stato abbonamento ---

    /**
     * Clienti con abbonamento attivo (AbbonamentoAttivo non scaduto).
     *
     * @return Cliente[]
     */
    public function findConAbbonamentoAttivo(): array;

    /**
     * Clienti senza abbonamento oppure con abbonamento scaduto.
     *
     * @return Cliente[]
     */
    public function findSenzaAbbonamentoAttivo(): array;

    // --- Stato certificato medico ---

    /**
     * Clienti il cui certificato medico è scaduto o assente.
     * Utile per notifiche e blocco prenotazioni.
     *
     * @return Cliente[]
     */
    public function findConCertificatoScadutoOAssente(): array;

    /**
     * Clienti il cui certificato scade entro $giorni giorni.
     * Utile per notifiche preventive.
     *
     * @return Cliente[]
     */
    public function findConCertificatoInScadenzaEntro(int $giorni): array;

    // --- Attività pianificate ---

    /**
     * Clienti iscritti a una specifica attività pianificata.
     *
     * @return Cliente[]
     */
    public function findByAttivitaPianificata(AttivitaPianificata $attivita): array;

    /**
     * Verifica se un cliente è iscritto a una specifica attività pianificata.
     */
    public function isIscrittoAAttivita(Cliente $cliente, AttivitaPianificata $attivita): bool;
}