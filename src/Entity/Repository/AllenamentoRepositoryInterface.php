<?php

namespace App\Entity\Repository;

use App\Entity\Allenamento;
use App\Entity\Scheda;

/**
 * Contratto dichiarato in Entity.
 * Zero import da Doctrine.
 */
interface AllenamentoRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Allenamento;
    public function save(Allenamento $allenamento): void;
    public function delete(Allenamento $allenamento): void;

    /** @return Allenamento[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    /**
     * Trova tutti gli allenamenti appartenenti a una scheda.
     * Caso d'uso: visualizzare il programma completo di un cliente.
     *
     * @return Allenamento[]
     */
    public function findByScheda(Scheda $scheda): array;

    /**
     * Verifica se esiste già un allenamento con lo stesso nome in una scheda.
     * Evita duplicati nella stessa scheda.
     */
    public function existsByNomeInScheda(string $nome, Scheda $scheda): bool;
}