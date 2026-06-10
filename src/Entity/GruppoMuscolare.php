<?php
namespace App\Entity;

class GruppoMuscolare
{
    private ?int $id = null;
    private string $nomeGruppoMuscolare;

    // lato inverso della N-N con Esercizio — biunivoca
    private array $esercizi = [];

    public function __construct(string $nomeGruppoMuscolare)
    {
        $this->nomeGruppoMuscolare = $nomeGruppoMuscolare;
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNomeGruppoMuscolare(): string
    {
        return $this->nomeGruppoMuscolare;
    }

    /** @return Esercizio[] */
    public function getEsercizi(): array
    {
        return $this->esercizi;
    }

    // -------------------------------------------------------------------------
    // Setter
    // -------------------------------------------------------------------------

    public function setNomeGruppoMuscolare(string $nome): self
    {
        $this->nomeGruppoMuscolare = $nome;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Gestione relazione N-N con Esercizio — lato inverso
    // -------------------------------------------------------------------------

    public function aggiungiEsercizio(Esercizio $esercizio): self
    {
        //Controlla per evitare un duplicato
        foreach ($this->esercizi as $e) {
            if ($e === $esercizio)
                return $this;
        }
        $this->esercizi[] = $esercizio;
        return $this;
    }

    public function rimuoviEsercizio(Esercizio $esercizio): self
    {
        $indice = array_search($esercizio, $this->esercizi, true);
        if ($indice !== false) {
            unset($this->esercizi[$indice]);
            $this->esercizi = array_values($this->esercizi); // Re-indicizza l'array
        }
        return $this;
    }
}