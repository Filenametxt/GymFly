<?php

namespace App\Entity\Repository;

interface AttivitaRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
