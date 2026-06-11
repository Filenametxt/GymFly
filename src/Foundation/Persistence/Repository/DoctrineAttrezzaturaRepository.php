<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\AttrezzaturaRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineAttrezzaturaRepository extends EntityRepository implements AttrezzaturaRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
