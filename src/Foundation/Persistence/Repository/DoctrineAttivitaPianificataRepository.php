<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\AttivitaPianificataRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineAttivitaPianificataRepository extends EntityRepository implements AttivitaPianificataRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
