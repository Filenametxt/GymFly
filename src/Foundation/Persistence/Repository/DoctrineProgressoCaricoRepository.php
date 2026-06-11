<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\ProgressoCaricoRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineProgressoCaricoRepository extends EntityRepository implements ProgressoCaricoRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
