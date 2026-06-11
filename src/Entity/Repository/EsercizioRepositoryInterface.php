<?php

namespace App\Entity\Repository;

interface EsercizioRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
