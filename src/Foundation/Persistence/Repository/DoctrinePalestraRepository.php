<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\PalestraRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrinePalestraRepository extends EntityRepository implements PalestraRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
