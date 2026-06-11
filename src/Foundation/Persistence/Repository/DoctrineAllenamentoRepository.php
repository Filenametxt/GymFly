<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\AllenamentoRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineAllenamentoRepository extends EntityRepository implements AllenamentoRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
