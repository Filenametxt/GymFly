<?php

namespace App\Entity\Repository;

interface ProgressoCaricoRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
