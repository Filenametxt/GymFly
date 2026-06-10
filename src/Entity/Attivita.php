<?php

namespace App\Entity;

/**
 * Classe Attivita.
 * Il mapping ORM è definito esternamente in:
 *   foundation/App.Entity.Attivita.dcm.xml
 */
class Attivita
{
    private ?int $id = null;
    private string $nome;
    private string $descrizione;
    private int $maxPartecipanti;

    /** @var Allenatore[] */
    private array $allenatori = [];

    public function __construct(string $nome, string $descrizione, int $maxPartecipanti)
    {
        $this->nome            = $nome;
        $this->descrizione     = $descrizione;
        $this->maxPartecipanti = $maxPartecipanti;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescrizione(): string
    {
        return $this->descrizione;
    }

    public function getMaxPartecipanti(): int
    {
        return $this->maxPartecipanti;
    }

     public function setNome(string $nome): self
    {
        $this->nome = $nome;
        return $this;
    }

     public function setDescrizione(string $descrizione): self
    {
        $this->descrizione = $descrizione;
        return $this;
    }

    public function setMaxPartecipanti(int $maxPartecipanti): self
    {
        $this->maxPartecipanti = $maxPartecipanti;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Allenatori abilitati  (N-N)
    // -------------------------------------------------------------------------

    /** @return Allenatore[] */
    public function getAllenatori(): array
    {
        return $this->allenatori;
    }

    public function addAllenatore(Allenatore $allenatore): self
    {
        if (!in_array($allenatore, $this->allenatori, true)) {
            $this->allenatori[] = $allenatore;
        }
        return $this;
    }

    public function removeAllenatore(Allenatore $allenatore): self
    {
        $this->allenatori = array_values(
            array_filter($this->allenatori, fn(Allenatore $a) => $a !== $allenatore)
        );
        return $this;
    }
}
?>