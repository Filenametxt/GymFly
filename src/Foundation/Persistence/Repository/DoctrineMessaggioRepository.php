<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\MessaggioRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineMessaggioRepository extends EntityRepository implements MessaggioRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
