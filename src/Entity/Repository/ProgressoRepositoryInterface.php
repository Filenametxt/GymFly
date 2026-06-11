<?php

namespace App\Entity\Repository;

interface ProgressoRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
