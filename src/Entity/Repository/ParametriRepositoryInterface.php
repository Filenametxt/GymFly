<?php

namespace App\Entity\Repository;

interface ParametriRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
