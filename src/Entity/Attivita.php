<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
class Attivita{
    private ?int $id = null;
    private string $nome;
    private string $descrizione;
    private int $max_partecipanti;
    private AttivitaPianificata $attivitapianificata;
    private Collection $allenatori;

    public function __construct(string $nome, string $descrizione, int $max_partecipanti, AttivitaPianificata $attivitapianificata){
        $this->nome = $nome;
        $this->descrizione = $descrizione;
        $this->max_partecipanti = $max_partecipanti;
        $this->attivitapianificata = $attivitapianificata;
        $this->allenatori = new ArrayCollection();
    }
    public function getId(): ?int{
        return $this->id;
    }
    public function getNome(): string{
        return $this->nome;
    }
    public function getDescrizione(): string{
        return $this->descrizione;
    }
    public function getMax_partecipanti(): int{
        return $this->max_partecipanti;
    }
    public function getAttivitapianificata(): AttivitaPianificata{
        return $this->attivitapianificata;
    }
    public function getAllenatore(): Collection{
        return $this->allenatori;
    }
    public function setNome(string $nome): self{
        $this->nome = $nome;
        return $this;
    }
    public function setDescrizione(string $descrizione): self{
        $this->descrizione = $descrizione;
        return $this;
    }
    public function setMax_partecipanti(int $max_partecipanti): self{
        $this->max_partecipanti = $max_partecipanti;
        return $this;
    }
    public function setAttivitaPianificata(AttivitaPianificata $attivitapianificata): self{
        $this->attivitapianificata = $attivitapianificata;
        return $this;
    }
    public function setAllenatore(Collection $allenatori): self{
        $this->allenatori = $allenatori;
        return $this;
    }
}
?>