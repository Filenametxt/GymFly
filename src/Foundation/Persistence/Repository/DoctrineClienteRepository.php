<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\ClienteRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineClienteRepository extends EntityRepository implements ClienteRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
