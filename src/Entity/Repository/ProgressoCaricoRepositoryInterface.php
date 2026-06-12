<?php

namespace App\Entity\Repository;

use App\Entity\ProgressoCarico;

interface ProgressoCaricoRepositoryInterface extends ProgressoRepositoryInterface
{
    public function save(ProgressoCarico $entity): void;
}