<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\UtenteRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineUtenteRepository extends EntityRepository implements UtenteRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
