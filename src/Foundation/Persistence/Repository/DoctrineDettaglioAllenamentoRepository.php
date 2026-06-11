<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\DettaglioAllenamentoRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineDettaglioAllenamentoRepository extends EntityRepository implements DettaglioAllenamentoRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
