<?php

namespace App\Entity;

class Tipologia
{
    private ?int $id = null;
    private string $nomeTipologia;

    public function __construct(string $nomeTipologia)
    {
        $this->setNomeTipologia($nomeTipologia);
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNomeTipologia(): string
    {
        return $this->nomeTipologia;
    }

    public function setNomeTipologia(string $nomeTipologia): self
    {
        // 1. Pulizia con trim per rimuovere spazi bianchi all'inizio e alla fine
        $nomePulito = trim($nomeTipologia);

        // 2. Controllo stringa vuota (evita che il nome sia nullo o composto solo da spazi)
        if ($nomePulito === '') {
            throw new InvalidArgumentException("Il nome della tipologia è obbligatorio e non può essere vuoto.");
        }

        $this->nomeTipologia = $nomePulito;
        return $this;
    }
}