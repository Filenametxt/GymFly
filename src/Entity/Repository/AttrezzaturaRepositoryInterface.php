<?php

namespace App\Entity\Repository;

interface AttrezzaturaRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
