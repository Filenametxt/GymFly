<?php

namespace App\Entity\Repository;

use App\Entity\AttivitaPianificata;
use App\Entity\Cliente;
use App\Entity\CodaAttesa;

interface CodaAttesaRepositoryInterface
{
    public function findById(int $id): ?CodaAttesa;

    public function save(CodaAttesa $entity): void;

    public function delete(CodaAttesa $entity): void;

    /**
     * @return CodaAttesa[]
     */
    public function findByAttivitaPianificata(AttivitaPianificata $attivita): array;

    /**
     * @return CodaAttesa[]
     */
    public function findByCliente(Cliente $cliente): array;

    public function findOneByClienteAndAttivita(Cliente $cliente, AttivitaPianificata $attivita): ?CodaAttesa;

    public function findPrimoInCoda(AttivitaPianificata $attivita): ?CodaAttesa;

    public function existsInCoda(Cliente $cliente, AttivitaPianificata $attivita): bool;

    public function countByAttivitaPianificata(AttivitaPianificata $attivita): int;
}
