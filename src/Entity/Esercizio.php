<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Esercizio
{
    private ?int $id = null;
    private ?string $nomeEsercizio = null;
    private ?string $descrizione = null;
    // 1-1 con Attrezzatura — NO biunivocità, Esercizio ha una attrezzatura necessaria
    private ?Attrezzatura $attrezzaturaNecessaria = null;

    // N-1 con Tipologia — NO biunivocità, Esercizio conosce Tipologia
    private Tipologia $tipologia;   

    // N-1 con Allenatore — NO biunivocità, Esercizio conosce Allenatore
    // nullable: se l'esercizio viene importato da API esterna non ha creatore
    private ?Allenatore $creatore = null;

    // N-N con GruppoMuscolare — tabella ALLENA, biunivoca
    /** @var Collection<int, GruppoMuscolare> */
    private Collection $gruppiMuscolari;

    public function __construct(
        ?string $nomeEsercizio,
        ?string $descrizione,
        Tipologia $tipologia,
        ?Attrezzatura $attrezzaturaNecessaria = null,
        ?Allenatore $creatore = null,
    ) {
        $this->gruppiMuscolari = new ArrayCollection();
        $this->setNomeEsercizio($nomeEsercizio);
        $this->setDescrizione($descrizione);
        $this->setTipologia($tipologia);
        $this->setAttrezzaturaNecessaria($attrezzaturaNecessaria);
        $this->setCreatore($creatore);
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNomeEsercizio(): ?string
    {
        return $this->nomeEsercizio;
    }
    public function getDescrizione(): ?string
    {
        return $this->descrizione;
    }
    public function getAttrezzaturaNecessaria(): ?Attrezzatura
    {
        return $this->attrezzaturaNecessaria;
    }
    public function getTipologia(): Tipologia
    {
        return $this->tipologia;
    }
    public function getCreatore(): ?Allenatore
    {
        return $this->creatore;
    }

    /** @return Collection<int, GruppoMuscolare> */
    public function getGruppiMuscolari(): Collection
    {
        return $this->gruppiMuscolari;
    }

    // -------------------------------------------------------------------------
    // Setter
    // -------------------------------------------------------------------------

    public function setNomeEsercizio(?string $nomeEsercizio): self
    {
        // Se è null, lo salviamo direttamente come null
        if ($nomeEsercizio === null) {
            $this->nomeEsercizio = null;
            return $this;
        }

        // Se non è null, facciamo il trim in totale sicurezza
        $nomePulito = trim($nomeEsercizio);
        
        // Se l'utente ha inserito solo spazi, lo normalizziamo a null
        $this->nomeEsercizio = ($nomePulito === "") ? null : $nomePulito;
        return $this;
    }

    public function setDescrizione(?string $descrizione): self
    {
        // Gestione sicura del null preventivo per evitare il crash di trim()
        if ($descrizione === null) {
            $this->descrizione = null;
            return $this;
        }

        $descrizionePulita = trim($descrizione);

        // Se la stringa è vuota dopo il trim, salviamo null nel database
        $this->descrizione = ($descrizionePulita === "") ? null : $descrizionePulita;
        return $this;
    }

    public function setAttrezzaturaNecessaria(?Attrezzatura  $attrezzatura): self
    {
        $this->attrezzaturaNecessaria = $attrezzatura;
        return $this;
    }

    public function setTipologia(Tipologia $tipologia): self
    {
        $this->tipologia = $tipologia;
        return $this;
    }

    public function setCreatore(?Allenatore $creatore): self
    {
        $this->creatore = $creatore;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Gestione relazione N-N con GruppoMuscolare — biunivoca
    // -------------------------------------------------------------------------

    public function aggiungiGruppoMuscolare(GruppoMuscolare $gruppo): self
    {
        if (!$this->gruppiMuscolari->contains($gruppo)) {
            $this->gruppiMuscolari->add($gruppo);
            // aggiorna lato inverso
            $gruppo->aggiungiEsercizio($this);
        }
        return $this;
    }

    public function rimuoviGruppoMuscolare(GruppoMuscolare $gruppo): self
    {
        if ($this->gruppiMuscolari->removeElement($gruppo)) {
            $gruppo->rimuoviEsercizio($this);
        }
        return $this;
    }
}