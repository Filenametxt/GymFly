<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\AllenatoreRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineAllenatoreRepository extends EntityRepository implements AllenatoreRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
