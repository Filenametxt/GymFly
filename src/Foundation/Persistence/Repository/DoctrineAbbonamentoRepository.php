<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\AbbonamentoRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineAbbonamentoRepository extends EntityRepository implements AbbonamentoRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
