<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\SchedaRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineSchedaRepository extends EntityRepository implements SchedaRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
