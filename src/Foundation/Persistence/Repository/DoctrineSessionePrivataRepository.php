<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\SessionePrivataRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineSessionePrivataRepository extends EntityRepository implements SessionePrivataRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
