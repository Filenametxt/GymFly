<?php

namespace App\Entity\Repository;

interface AbbonamentoDurataRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
