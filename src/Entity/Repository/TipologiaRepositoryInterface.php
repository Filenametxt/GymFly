<?php

namespace App\Entity\Repository;

interface TipologiaRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
