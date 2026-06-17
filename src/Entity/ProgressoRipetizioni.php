<?php

namespace App\Entity;

class ProgressoRipetizioni extends Progresso
{
    private float $nuovoNRipetizioni;

    public function __construct(\DateTimeImmutable $data, Cliente $cliente, Esercizio $esercizio, float $nuovoNRipetizioni)
    {
        parent::__construct($data, $cliente, $esercizio);
        $this->setNuovoNRipetizioni($nuovoNRipetizioni);
    }

    public function getNuovoNRipetizioni(): float
    {
        return $this->nuovoNRipetizioni;
    }
    public function setNuovoNRipetizioni(float $nuovoNRipetizioni): self
    {
        if ($nuovoNRipetizioni <= 0) {
            throw new \InvalidArgumentException("Il numero delle ripetizioni non può essere negativo.");
        }

        $this->nuovoNRipetizioni = $nuovoNRipetizioni;
        return $this;
    }
    
}