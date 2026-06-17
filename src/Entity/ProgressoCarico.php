<?php

namespace App\Entity;

class ProgressoCarico extends Progresso
{
    private float $nuovoCarico;

    public function __construct(\DateTimeImmutable $data, Cliente $cliente, Esercizio $esercizio, float $nuovoCarico)
    {
        parent::__construct($data, $cliente, $esercizio);
        $this->setNuovoCarico($nuovoCarico);
    }

    public function getNuovoCarico(): float
    {
        return $this->nuovoCarico;
    }
    public function setNuovoCarico(float $nuovoCarico): self
    {

        if ($nuovoCarico <=0) {
            throw new \InvalidArgumentException("Il carico non può essere negativo.");
        }

        $this->nuovoCarico = $nuovoCarico;
        return $this;
    }

    }
