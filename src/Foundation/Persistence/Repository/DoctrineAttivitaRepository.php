<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\AttivitaRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineAttivitaRepository extends EntityRepository implements AttivitaRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
