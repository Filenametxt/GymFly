<?php

namespace App\Entity\Repository;

interface ProgressoRipetizioniRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
