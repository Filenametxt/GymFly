<?php
use Doctrine\ORM\Mapping as ORM;

class Progresso{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private Cliente $cliente_riferito;
    private \DateTimeImmutable $data;   //REVIEW: Tipo da definire bene
    private Esercizio $esercizio_riferito;


    public function __construct(Cliente $cliente_riferito, \DateTimeImmutable $data, Esercizio $esercizio_riferito) {
        $this->cliente_riferito = $cliente_riferito;
        $this->data = $data;
        $this->esercizio_riferito = $esercizio_riferito;
    }
    public function getId(): ?int {
       return $this->id;
    }
    public function getCliente_riferito(): Cliente{
        return $this->cliente_riferito;
    }
    public function getEsercizio_riferito(): Esercizio{
        return $this->esercizio_riferito;
    }
    public function setEsercizio_riferito(Esercizio $esercizio_riferito): self{
        $this->esercizio_riferito = $esercizio_riferito;
        return $this;
    }
    public function setCliente_riferito(Cliente $cliente_riferito): self{
        $this->cliente_riferito = $cliente_riferito;
        return $this;
    }
    public function getData(): \DateTimeImmutable {
        return $this->data;
    }
    public function setData(\DateTimeImmutable $data): self{
        $this->data = $data;
        return $this;
    }




}
?>