<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Scheda
{
    private int $id;
    private string $nome_scheda;
    private \DateTimeImmutable $data_inizio;
    private \DateTimeImmutable $data_fine;
    private string $obiettivo;
    private Cliente $cliente;
    private Allenatore $allenatore;

    /** @var Collection<int, Allenamento> */
    private Collection $allenamenti;

    public function __construct(
        string             $nome_scheda,
        \DateTimeImmutable $data_inizio,
        \DateTimeImmutable $data_fine,
        string             $obiettivo,
        Cliente            $cliente,
        Allenatore         $allenatore
    ) {
        $this->allenamenti = new ArrayCollection();

        if ($data_inizio >= $data_fine) {
            throw new \InvalidArgumentException(
                "La data di inizio deve essere precedente alla data di fine."
            );
        }
        $this->nome_scheda  = $nome_scheda;
        $this->data_inizio  = $data_inizio;
        $this->data_fine    = $data_fine;
        $this->obiettivo    = $obiettivo;
        $this->cliente      = $cliente;
        $this->allenatore   = $allenatore;
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome_scheda(): string
    {
        return $this->nome_scheda;
    }

    public function getData_inizio(): \DateTimeImmutable
    {
        return $this->data_inizio;
    }

    public function getData_fine(): \DateTimeImmutable
    {
        return $this->data_fine;
    }

    public function getObiettivo(): string
    {
        return $this->obiettivo;
    }

    public function getCliente(): Cliente
    {
        return $this->cliente;
    }

    public function getAllenatore(): Allenatore
    {
        return $this->allenatore;
    }

    /**
     * Restituisce gli allenamenti contenuti nella scheda.
     *
     * @return Collection<int, Allenamento>
     */
    public function getAllenamenti(): Collection
    {
        return $this->allenamenti;
    }

    // -------------------------------------------------------------------------
    // Setter
    // -------------------------------------------------------------------------

    public function setNome_scheda(string $nome_scheda): self
    {
        $this->nome_scheda = $nome_scheda;
        return $this;
    }

    public function setData_inizio(\DateTimeImmutable $data_inizio): self
    {
        if ($data_inizio >= $this->data_fine) {
            throw new \InvalidArgumentException(
                "La data di inizio deve essere precedente alla data di fine."
            );
        }
        $this->data_inizio = $data_inizio;
        return $this;
    }

    public function setData_fine(\DateTimeImmutable $data_fine): self
    {
        if ($data_fine <= $this->data_inizio) {
            throw new \InvalidArgumentException(
                "La data di fine deve essere successiva alla data di inizio."
            );
        }
        $this->data_fine = $data_fine;
        return $this;
    }

    public function setObiettivo(string $obiettivo): self
    {
        $this->obiettivo = $obiettivo;
        return $this;
    }

    public function setCliente(Cliente $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }

    public function setAllenatore(Allenatore $allenatore): self
    {
        $this->allenatore = $allenatore;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Gestione biunivoca Scheda <-> Allenamento
    // -------------------------------------------------------------------------

    /**
     * Aggiunge un Allenamento alla Scheda (lato inverso della biunivocità).
     * Imposta anche il riferimento alla Scheda sull'Allenamento (lato proprietario),
     * garantendo la coerenza in memoria prima del flush su DB.
     */
    public function addAllenamento(Allenamento $allenamento): self
    {
        if (!$this->allenamenti->contains($allenamento)) {
            $this->allenamenti->add($allenamento);
            // Mantiene la biunivocità: il lato proprietario viene aggiornato
            $allenamento->setScheda($this);
        }
        return $this;
    }

    /**
     * Rimuove un Allenamento dalla Scheda.
     * Grazie a orphanRemoval=true nel mapping XML, l'Allenamento verrà
     * eliminato dal DB al prossimo flush.
     */
    public function removeAllenamento(Allenamento $allenamento): self
    {
        if ($this->allenamenti->removeElement($allenamento)) {
            // Rimuove il riferimento inverso sull'Allenamento
            $allenamento->setScheda(null);
        }
        return $this;
    }
}