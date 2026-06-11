<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\GruppoMuscolareRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineGruppoMuscolareRepository extends EntityRepository implements GruppoMuscolareRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
