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

    /** @return Esercizio[] */
    public function findByGruppoMuscolare(GruppoMuscolare $gruppo): array;

    /** @return Esercizio[] */
    public function findByTipologia(Tipologia $tipologia): array;

    /** @return Esercizio[] */
    public function findByCreatore(Allenatore $allenatore): array;

    /** @return Esercizio[] esercizi importati da API esterna, senza creatore */
    public function findSenzaCreatore(): array;

    public function existsByNome(string $nome): bool;
}