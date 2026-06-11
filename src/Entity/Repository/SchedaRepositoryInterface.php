<?php

namespace App\Entity\Repository;

interface SchedaRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
