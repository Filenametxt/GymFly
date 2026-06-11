<?php

namespace App\Entity\Repository;

interface IscrizioneRepositoryInterface
{
    public function find(int $id);
    public function findAll();
}
