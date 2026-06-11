<?php

namespace App\Entity\Repository;

use App\Entity\Palestra;
use App\Entity\Amministratore;

interface PalestraRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?Palestra;

    public function save(Palestra $entity): void;

    public function delete(Palestra $entity): void;

    /** @return Palestra[] */
    public function findAll(): array;

    // --- Lookup anagrafico ---

    /**
     * Cerca una palestra per email (univoca nel dominio).
     */
    public function findByEmail(string $email): ?Palestra;

    /**
     * Ricerca per nome — utile per ricerca utente o pannello admin.
     *
     * @return Palestra[]
     */
    public function findByNomeContaining(string $partial): array;

    /**
     * Verifica se esiste già una palestra registrata con quell'email.
     * Guard prima del salvataggio per evitare duplicati.
     */
    public function existsByEmail(string $email): bool;

    /**
     * Verifica se esiste già una palestra con quel nome in quell'indirizzo.
     * Evita duplicati geografici.
     */
    public function existsByNomeAndIndirizzo(string $nome, string $indirizzo): bool;

}