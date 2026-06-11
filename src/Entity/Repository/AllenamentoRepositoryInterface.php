<?php

namespace App\Entity\Repository;

interface AllenamentoRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
