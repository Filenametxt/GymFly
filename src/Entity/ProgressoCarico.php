<?php

namespace App\Entity;

class ProgressoCarico extends Progresso
{
    private float $nuovoCarico;

    public function __construct(\DateTimeImmutable $data, Cliente $cliente, Esercizio $esercizio, float $nuovoCarico)
    {
        parent::__construct($data, $cliente, $esercizio);
        $this->nuovoCarico = $nuovoCarico;
    }

    public function getNuovoCarico(): float
    {
        return $this->nuovoCarico;
    }
    public function setNuovoCarico(float $nuovoCarico): self
    {
        $this->nuovoCarico = $nuovoCarico;
        return $this;
    }
}