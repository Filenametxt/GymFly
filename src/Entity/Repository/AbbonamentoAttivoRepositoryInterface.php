<?php

namespace App\Entity\Repository;

interface AbbonamentoAttivoRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
