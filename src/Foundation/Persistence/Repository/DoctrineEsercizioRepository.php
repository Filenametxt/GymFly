<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\EsercizioRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineEsercizioRepository extends EntityRepository implements EsercizioRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
