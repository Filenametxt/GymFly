<?php

namespace App\Entity;

class ProgressoDurata extends Progresso
{
    private float $nuovaDurata;

    public function __construct(\DateTimeImmutable $data, Cliente $cliente, Esercizio $esercizio, float $nuovaDurata)
    {
        parent::__construct($data, $cliente, $esercizio);
        $this->setNuovaDurata($nuovaDurata);
    }

    public function getNuovaDurata(): float
    {
        return $this->nuovaDurata;
    }
    public function setNuovaDurata(float $nuovaDurata): self
    {
        if ($nuovaDurata <= 0 ) {
            throw new \InvalidArgumentException("La durata non può essere negativa.");
        }

        $this->nuovaDurata = $nuovaDurata;
        return $this;
    }
}
