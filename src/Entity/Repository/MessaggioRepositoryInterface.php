<?php

namespace App\Entity\Repository;

interface MessaggioRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
