<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\ParametriRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineParametriRepository extends EntityRepository implements ParametriRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
