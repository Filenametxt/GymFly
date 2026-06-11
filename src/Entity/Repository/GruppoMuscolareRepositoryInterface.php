<?php

namespace App\Entity\Repository;

interface GruppoMuscolareRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
