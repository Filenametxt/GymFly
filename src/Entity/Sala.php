<?php

namespace App\Entity;

/**
 * Classe Sala.
 * Il mapping ORM è definito esternamente in:
 *   foundation/App.Entity.Sala.dcm.xml
 */
class Sala
{
    private ?int $id = null;
    private string $nome;
    private int $maxPartecipanti;
    private Palestra $palestra;

    public function __construct(string $nome, int $maxPartecipanti, Palestra $palestra)
    {
        $this->nome             = $nome;
        $this->maxPartecipanti  = $maxPartecipanti;
        $this->palestra         = $palestra;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getMaxPartecipanti(): int
    {
        return $this->maxPartecipanti;
    }

    public function getPalestra(): Palestra
    {
        return $this->palestra;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

    public function setMaxPartecipanti(int $maxPartecipanti): self
    {
        $this->maxPartecipanti = $maxPartecipanti;
        return $this;
    }

    public function setPalestra(Palestra $palestra): self
    {
        $this->palestra = $palestra;
        return $this;
    }
}
?>