<?php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Sala{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    private string $nome;
    private int $max_partecipanti;
    private Palestra $palestra;

    public function __construct(string $nome, int $max_partecipanti, Palestra $palestra) {
        $this->nome = $nome;
        $this->max_partecipanti = $max_partecipanti;
        $this->palestra = $palestra;
    }
    public function getId(): ?int {
        return $this->id;
    }
    public function getNome(): string{
        return $this->nome;
    }
    public function getMax_partecipanti(): int{
        return $this->max_partecipanti;
    }
    public function getPalestra(): Palestra{
        return $this->palestra;
    }
    public function setNome(string $nome): self{
        $this->nome = $nome;
        return $this;
    }
    public function setMax_partecipanti(int $max_partecipanti): self{
        $this->max_partecipanti = $max_partecipanti;
        return $this;
    }
    public function setPalestra(Palestra $palestra): self{
        $this->palestra = $palestra;
        return $this;
    }
}
?>