<?php

namespace App\Entity;

class DettaglioAllenamento
{
    private ?int $id = null;
    private int $serie;
    private ?int $ripetizioni = null;
    private float $carico;
    private ?string $tempo = null;

    // N-1 con Esercizio — NO biunivocità, DettaglioAllenamento conosce Esercizio
    private Esercizio $esercizio;

    // N-1 con Allenamento — biunivoca (Allenamento ha array di dettagli)
    private Allenamento $allenamento;

    public function __construct(
        Esercizio $esercizio,
        Allenamento $allenamento,
        int $serie,
        ?int $ripetizioni,
        float $carico,
        ?string $tempo = null
    ) {
        $this->setEsercizio($esercizio);
        $this->setAllenamento($allenamento);
        $this->setSerie($serie);
        $this->setRipetizioni($ripetizioni);
        $this->setCarico($carico);
        $this->setTempo($tempo);
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
    public function getRipetizioni(): ?int
    {
        return $this->ripetizioni;
    }
    public function getTempo(): ?string
    {
        return $this->tempo;
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

        // BIUNIVOCITÀ: Se la collezione dei dettagli dell'allenamento non contiene questo dettaglio, aggiungilo
        // Nota: Assicurati che in Allenamento.php il getter della collezione si chiami getDettagliAllenamenti() o similare
        if (!$allenamento->getDettagli()->contains($this)) {
            $allenamento->addDettaglio($this); 
        }

        return $this;
    }

    public function setSerie(int $serie): self
    {
        if ($serie <= 0)
            throw new \InvalidArgumentException('Le serie devono essere maggiori di 0.');
        $this->serie = $serie;
        return $this;
    }

    public function setRipetizioni(?int $ripetizioni): self
    {
        if ($ripetizioni !== null && $ripetizioni <= 0)
            throw new \InvalidArgumentException('Le ripetizioni devono essere maggiori di 0.');
        $this->ripetizioni = $ripetizioni;
        return $this;
    }

    public function setTempo(?string $tempo): self
    {
        $this->tempo = $tempo;
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