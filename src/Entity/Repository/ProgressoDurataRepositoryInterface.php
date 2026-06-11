<?php

namespace App\Entity\Repository;

interface ProgressoDurataRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
