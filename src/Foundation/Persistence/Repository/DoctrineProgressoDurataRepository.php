<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\ProgressoDurataRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineProgressoDurataRepository extends EntityRepository implements ProgressoDurataRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
