<?php

namespace App\Entity\Repository;

interface SalaRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
