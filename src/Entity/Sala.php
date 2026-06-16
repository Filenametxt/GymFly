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
        $this->setNome($nome);
        $this->setMaxPartecipanti($maxPartecipanti);
        $this->setPalestra($palestra);
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
        $nomePulito = trim($nome);

        if ($nomePulito === '') {
            throw new \InvalidArgumentException("Il nome non può essere vuoto.");
        }

        $this->nome = $nomePulito;
        return $this;

    }

    public function setMaxPartecipanti(int $maxPartecipanti): self
    {
        if ($maxPartecipanti <= 0) {
            throw new \InvalidArgumentException("Il numero massimo deve essere maggiore di 0.");
        }

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