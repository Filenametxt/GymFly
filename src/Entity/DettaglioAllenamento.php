<?php

namespace App\Entity;

class DettaglioAllenamento
{
    private ?int $id = null;
    private int $serie;
    private int $ripetizioni;
    private float $carico;

    // N-1 con Esercizio — NO biunivocità, DettaglioAllenamento conosce Esercizio
    private Esercizio $esercizio;

    // N-1 con Allenamento — biunivoca (Allenamento ha array di dettagli)
    private Allenamento $allenamento;

    public function __construct(
        Esercizio $esercizio,
        Allenamento $allenamento,
        int $serie,
        int $ripetizioni,
        float $carico,
    ) {
        $this->esercizio = $esercizio;
        $this->allenamento = $allenamento;
        $this->serie = $serie;
        $this->ripetizioni = $ripetizioni;
        $this->carico = $carico;
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEsercizio(): Esercizio
    {
        return $this->esercizio;
    }
    public function getAllenamento(): Allenamento
    {
        return $this->allenamento;
    }
    public function getSerie(): int
    {
        return $this->serie;
    }
    public function getRipetizioni(): int
    {
        return $this->ripetizioni;
    }
    public function getCarico(): float
    {
        return $this->carico;
    }  // BUGFIX: era int

    // -------------------------------------------------------------------------
    // Setter
    // -------------------------------------------------------------------------

    public function setEsercizio(Esercizio $esercizio): self
    {
        $this->esercizio = $esercizio;
        return $this;
    }

    public function setAllenamento(Allenamento $allenamento): self
    {
        $this->allenamento = $allenamento;
        return $this;
    }

    public function setSerie(int $serie): self
    {
        if ($serie <= 0)
            throw new \InvalidArgumentException('Le serie devono essere maggiori di 0.');
        $this->serie = $serie;
        return $this;
    }

    public function setRipetizioni(int $ripetizioni): self
    {
        if ($ripetizioni <= 0)
            throw new \InvalidArgumentException('Le ripetizioni devono essere maggiori di 0.');
        $this->ripetizioni = $ripetizioni;
        return $this;
    }

    public function setCarico(float $carico): self
    {
        if ($carico < 0)
            throw new \InvalidArgumentException('Il carico non può essere negativo.');
        $this->carico = $carico;
        return $this;
    }
}