<?php

namespace App\Entity;

abstract class Abbonamento
{
    protected ?int $id = null;
    protected string $tipologia;
    protected string $categoria;

    public function __construct(string $tipologia, string $categoria)
    {
        $this->tipologia = $tipologia;
        $this->categoria = $categoria;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getTipologia(): string
    {
        return $this->tipologia;
    }
    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function setTipologia(string $tipologia): self
    {
        $this->tipologia = $tipologia;
        return $this;
    }
    public function setCategoria(string $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    // Forza le sottoclassi a definire se e come calcolare la data di fine temporale
    abstract public function calcolaDataFine(\DateTimeImmutable $dataInizio): ?\DateTimeImmutable;

    // Pattern Strategy: la sottoclasse valuta se il contesto (AbbonamentoAttivo) è scaduto
    abstract public function isScaduto(AbbonamentoAttivo $context): bool;

    // Genera la stringa descrittiva basandosi sullo stato del contesto
    abstract public function descrizioneScadenza(AbbonamentoAttivo $context): string;
}