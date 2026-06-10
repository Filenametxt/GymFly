<?php

namespace App\Entity;

class ProgressoRipetizioni extends Progresso
{
    private float $nuovoNRipetizioni;

    public function __construct(\DateTimeImmutable $data, Cliente $cliente, Esercizio $esercizio, float $nuovoNRipetizioni)
    {
        parent::__construct($data, $cliente, $esercizio);
        $this->nuovoNRipetizioni = $nuovoNRipetizioni;
    }

    public function getNuovoNRipetizioni(): float
    {
        return $this->nuovoNRipetizioni;
    }
    public function setNuovoNRipetizioni(float $nuovoNRipetizioni): self
    {
        $this->nuovoNRipetizioni = $nuovoNRipetizioni;
        return $this;
    }
}