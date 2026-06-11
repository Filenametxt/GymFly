<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\SalaRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineSalaRepository extends EntityRepository implements SalaRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
