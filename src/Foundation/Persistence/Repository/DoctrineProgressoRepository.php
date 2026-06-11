<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\ProgressoRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineProgressoRepository extends EntityRepository implements ProgressoRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
