<?php

namespace App\Entity\Repository;

interface AllenatoreRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
