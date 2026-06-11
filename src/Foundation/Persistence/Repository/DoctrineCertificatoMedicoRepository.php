<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\CertificatoMedicoRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineCertificatoMedicoRepository extends EntityRepository implements CertificatoMedicoRepositoryInterface
{
    // Implementazione concreta tramite Doctrine
}
