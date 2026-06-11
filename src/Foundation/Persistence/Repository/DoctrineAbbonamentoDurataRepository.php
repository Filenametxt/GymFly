<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\AbbonamentoDurataRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineAbbonamentoDurataRepository extends EntityRepository implements AbbonamentoDurataRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
