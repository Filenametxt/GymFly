<?php

namespace App\Entity\Repository;

use App\Entity\ProgressoDurata;

interface ProgressoDurataRepositoryInterface extends ProgressoRepositoryInterface
{
    public function save(ProgressoDurata $entity): void;
}