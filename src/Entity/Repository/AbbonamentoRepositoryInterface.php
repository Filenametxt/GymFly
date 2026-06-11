<?php

namespace App\Entity\Repository;

interface AbbonamentoRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
