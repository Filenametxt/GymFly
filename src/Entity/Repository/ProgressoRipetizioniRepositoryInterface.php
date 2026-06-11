<?php

namespace App\Entity\Repository;

use App\Entity\ProgressoRipetizioni;

interface ProgressoRipetizioniRepositoryInterface extends ProgressoRepositoryInterface
{
    public function save(ProgressoRipetizioni $entity): void;
}