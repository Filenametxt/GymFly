<?php

namespace App\Entity\Repository;

interface SessionePrivataRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
