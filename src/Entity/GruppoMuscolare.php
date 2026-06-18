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
        $this->setNomeGruppoMuscolare($nomeGruppoMuscolare);
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
        $nomePulito = trim($nome);
        if ($nomePulito === "") {
            throw new \InvalidArgumentException("Il nome del gruppo muscolare non può essere vuoto.");
        }
        $this->nomeGruppoMuscolare = $nomePulito;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Gestione relazione N-N con Esercizio — lato inverso
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Gestione relazione N-N con Esercizio — Sincronizzazione in memoria RAM
    // -------------------------------------------------------------------------

    /**
     * Associa un esercizio a questo gruppo muscolare e garantisce
     * che l'esercizio si associ a sua volta in modo bidirezionale.
     */
    public function aggiungiEsercizio(Esercizio $esercizio): self
    {
        if (!$this->esercizi->contains($esercizio)) {
            $this->esercizi->add($esercizio);
            
            // BIUNIVOCITÀ: Controlla il lato proprietario (Esercizio)
            // Assicurati che il metodo richiamato corrisponda a quello in Esercizio.php (es. addGruppoMuscolare o aggiungiGruppoMuscolare)
            if (!$esercizio->getGruppiMuscolari()->contains($this)) {
                $esercizio->aggiungiGruppoMuscolare($this);
            }
        }
        return $this;
    }

    /**
     * Rimuove l'associazione con un esercizio in modo bidirezionale.
     */
    public function rimuoviEsercizio(Esercizio $esercizio): self
    {
        if ($this->esercizi->contains($esercizio)) {
            $this->esercizi->removeElement($esercizio);
            
            // BIUNIVOCITÀ: Rimuove lo specchio sul lato proprietario
            if ($esercizio->getGruppiMuscolari()->contains($this)) {
                $esercizio->rimuoviGruppoMuscolare($this);
            }
        }
        return $this;
    }
}