<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Utente;

class DoctrineUtenteRepository extends AbstractDoctrineUtenteRepository
{
    protected function getEntityClass(): string
    {
        return Utente::class;
    }
}
