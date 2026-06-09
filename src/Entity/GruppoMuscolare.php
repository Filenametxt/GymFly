<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

class GruppoMuscolare{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    private string $nome_gruppo_muscolare;
    private Collection $esercizi; 

    public function __construct(string $nome_gruppo_muscolare){
        $this->nome_gruppo_muscolare = $nome_gruppo_muscolare;
    }
    public function getId(): ?int{
        return $this->id;
    }
    public function getNomeGruppoMuscolare(): string{
        return $this->nome_gruppo_muscolare;
    }
    public function getEsercizi(): Collection {
        return $this->esercizi;
    }
    public function setNomeGruppoMuscolare(string $nome_gruppo_muscolare): self{
        $this->nome_gruppo_muscolare = $nome_gruppo_muscolare;
        return $this;
    }
    public function setEsercizi(Collection $esercizi): self{
        $this->esercizi = $esercizi;
        return $this;
    }
}
?>