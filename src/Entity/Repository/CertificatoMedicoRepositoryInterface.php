<?php

namespace App\Entity\Repository;

interface CertificatoMedicoRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
