<?php

namespace App\Entity\Repository;

//solo import da App\Entity — zero Doctrine
use App\Entity\Esercizio;
use App\Entity\GruppoMuscolare;
use App\Entity\Tipologia;
use App\Entity\Allenatore;

/**
 * Contratto dichiarato in Entity.
 * Control conosce SOLO questa interfaccia.
 * Non sa nulla di Doctrine, SQL, o di come i dati vengono recuperati.
 */
interface EsercizioRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?Esercizio;
    public function save(Esercizio $esercizio): void;
    public function delete(Esercizio $esercizio): void;

    /** @return Esercizio[] */
    public function findAll(): array;

    // --- Metodi specifici del dominio ---

    /** 
     * Restituisce tutti gli esercizi che utilizzano quel gruppo muscolare
     * @return Esercizio[] 
     */
    public function findByGruppoMuscolare(GruppoMuscolare $gruppo): array;

    /**
     * Restituisce tutti gli esercizi di una determinata tipologia
     * @return Esercizio[] */
    public function findByTipologia(Tipologia $tipologia): array;

    /**
     * Restituisce tutti gli esercizi creati da un determinato allenatore
     * @return Esercizio[] */
    public function findByCreatore(Allenatore $allenatore): array;

    /**
     * Controlla se esiste già un esercizio con quel nome
     */
    public function existsByNome(string $nome): bool;
}