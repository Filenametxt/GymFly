<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\AmministratoreRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineAmministratoreRepository extends EntityRepository implements AmministratoreRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
