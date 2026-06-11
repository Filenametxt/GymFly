<?php

namespace App\Entity\Repository;

interface UtenteRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
