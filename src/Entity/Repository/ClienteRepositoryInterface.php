<?php

namespace App\Entity\Repository;

interface ClienteRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
