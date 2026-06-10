<?php

namespace App\Entity;

class Tipologia
{
    private ?int $id = null;
    private string $nomeTipologia;

    public function __construct(string $nomeTipologia)
    {
        $this->nomeTipologia = $nomeTipologia;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNomeTipologia(): string
    {
        return $this->nomeTipologia;
    }

    public function setNomeTipologia(string $nomeTipologia): self
    {
        $this->nomeTipologia = $nomeTipologia;
        return $this;
    }
}