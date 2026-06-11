<?php

namespace App\Entity\Repository;

interface DettaglioAllenamentoRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
