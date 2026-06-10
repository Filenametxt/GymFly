<?php

namespace App\Entity;

class Allenamento
{
    private ?int $id = null;
    private string $nome;
    private ?string $descrizione = null;
    private ?Scheda $scheda = null;


    //Si riferisce alla relazione con Dettagli, biunivocità
    private array $dettagli = [];

    public function __construct(string $nome, ?string $descrizione = null)
    {
        $this->nome = $nome;
        $this->descrizione = $descrizione;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNome(): string
    {
        return $this->nome;
    }
    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function getDescrizione(): ?string
    {
        return $this->descrizione;
    }
    public function setDescrizione(?string $descrizione): self
    {
        $this->descrizione = $descrizione;
        return $this;
    }

    public function getScheda(): ?Scheda
    {
        return $this->scheda;
    }
    public function setScheda(?Scheda $scheda): self
    {
        $this->scheda = $scheda;
        return $this;
    }

    public function getDettagli(): array
    {
        return $this->dettagli;
    }

    public function addDettaglio(DettaglioAllenamento $dettaglio): self
    {
        if (!in_array($dettaglio, $this->dettagli, true)) {
            $this->dettagli[] = $dettaglio;
            $dettaglio->setAllenamento($this);
        }
        return $this;
    }
    
}