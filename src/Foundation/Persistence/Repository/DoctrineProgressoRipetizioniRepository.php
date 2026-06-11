<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\ProgressoRipetizioniRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineProgressoRipetizioniRepository extends EntityRepository implements ProgressoRipetizioniRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
