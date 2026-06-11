<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\IscrizioneRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineIscrizioneRepository extends EntityRepository implements IscrizioneRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
