<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\AbbonamentoAttivoRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineAbbonamentoAttivoRepository extends EntityRepository implements AbbonamentoAttivoRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
