<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\TipologiaRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineTipologiaRepository extends EntityRepository implements TipologiaRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
