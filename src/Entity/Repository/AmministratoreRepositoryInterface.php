<?php

namespace App\Entity\Repository;

interface AmministratoreRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
