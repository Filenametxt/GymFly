<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class GruppoMuscolare
{
    private ?int $id = null;
    private string $nomeGruppoMuscolare;

    // lato inverso della N-N con Esercizio — biunivoca
    /** @var Collection<int, Esercizio> */
    private Collection $esercizi;

    public function __construct(string $nomeGruppoMuscolare)
    {
        $this->esercizi = new ArrayCollection();
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

    /** @return Collection<int, Esercizio> */
    public function getEsercizi(): Collection
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
        if (!$this->esercizi->contains($esercizio)) {
            $this->esercizi->add($esercizio);
        }
        return $this;
    }

    public function rimuoviEsercizio(Esercizio $esercizio): self
    {
        $this->esercizi->removeElement($esercizio);
        return $this;
    }
}