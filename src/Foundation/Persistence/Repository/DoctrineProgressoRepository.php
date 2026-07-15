<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Progresso;

class DoctrineProgressoRepository extends AbstractDoctrineProgressoRepository
{
    protected function getEntityClass(): string
    {
        return Progresso::class;
    }
}
