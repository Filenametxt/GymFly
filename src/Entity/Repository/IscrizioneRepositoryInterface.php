<?php

namespace App\Entity\Repository;

use App\Entity\Iscrizione;
use App\Entity\Cliente;

interface IscrizioneRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?Iscrizione;

    public function save(Iscrizione $entity): void;

    public function delete(Iscrizione $entity): void;

    /** @return Iscrizione[] */
    public function findAll(): array;

    // --- Metodi di dominio ---

    /**
     * Restituisce l'iscrizione associata a un cliente.
     * La relazione è 1-1: un cliente ha al più una iscrizione.
     */
    public function findByCliente(Cliente $cliente): ?Iscrizione;

    /**
     * Verifica se un cliente ha un'iscrizione attualmente attiva.
     */
    public function clienteHaIscrizioneAttiva(Cliente $cliente): bool;

    /**
     * Tutte le iscrizioni scadute (dataFine < oggi).
     * Utile per reportistica e pulizia periodica.
     *
     * @return Iscrizione[]
     */
    public function findScadute(): array;

    /**
     * Iscrizioni in scadenza entro $giorni giorni da oggi.
     * Utile per notifiche di rinnovo preventive.
     *
     * @return Iscrizione[]
     */
    public function findInScadenzaEntro(int $giorni): array;

    /**
     * Iscrizioni ancora attive (dataFine >= oggi).
     *
     * @return Iscrizione[]
     */
    public function findAttive(): array;
}