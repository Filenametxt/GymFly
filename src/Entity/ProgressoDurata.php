<?php

namespace App\Entity;

class ProgressoDurata extends Progresso
{
    private float $nuovaDurata;

    public function __construct(\DateTimeImmutable $data, Cliente $cliente, Esercizio $esercizio, float $nuovaDurata)
    {
        parent::__construct($data, $cliente, $esercizio);
        $this->nuovaDurata = $nuovaDurata;
    }

    public function getNuovaDurata(): float
    {
        return $this->nuovaDurata;
    }
    public function setNuovaDurata(float $nuovaDurata): self
    {
        $this->nuovaDurata = $nuovaDurata;
        return $this;
    }
}
