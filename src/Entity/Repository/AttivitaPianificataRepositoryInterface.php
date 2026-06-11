<?php

namespace App\Entity\Repository;

interface AttivitaPianificataRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
