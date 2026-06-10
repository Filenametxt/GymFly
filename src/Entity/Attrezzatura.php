<?php

namespace App\Entity;

class Attrezzatura
{
    private ?int $id = null;
    private string $nomeAttrezzatura;

    public function __construct(string $nomeAttrezzatura)
    {
        $this->nomeAttrezzatura = $nomeAttrezzatura;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomeAttrezzatura(): string
    {
        return $this->nomeAttrezzatura;
    }

    public function setNomeAttrezzatura(string $nomeAttrezzatura): self
    {
        $this->nomeAttrezzatura = $nomeAttrezzatura;
        return $this;
    }
}