<?php

namespace App\Entity\Repository;

interface PalestraRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
