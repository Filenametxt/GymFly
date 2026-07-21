<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Allenamento
{
    private ?int $id = null;
    private string $nome;
    private ?string $descrizione = null;
    private ?Scheda $scheda = null;


    //Si riferisce alla relazione con Dettagli, biunivocità
    /** @var Collection<int, DettaglioAllenamento> */
    private Collection $dettagli;

    public function __construct(string $nome, ?string $descrizione = null)
    {
        $this->dettagli = new ArrayCollection();
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

    /**
     * @return Collection<int, DettaglioAllenamento>
     */
    public function getDettagli(): Collection
    {
        return $this->dettagli;
    }

    /**
     * @return DettaglioAllenamento[]
     */
    public function getDettagliOrdinati(): array
    {
        $arr = $this->dettagli->toArray();
        usort($arr, function($a, $b) {
            return ($a->getId() ?? 0) <=> ($b->getId() ?? 0);
        });
        return $arr;
    }

    public function addDettaglio(DettaglioAllenamento $dettaglio): self
    {
        if (!$this->dettagli->contains($dettaglio)) {
            $this->dettagli->add($dettaglio);
            $dettaglio->setAllenamento($this);
        }
        return $this;
    }
    
}