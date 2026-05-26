<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

class Abbonamento{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private int $data_inizio;
    private string $tipologia;
    private Collection $cliente;

    public function __construct(int $data_inizio, string $tipologia){
        $this->data_inizio = $data_inizio;
        $this->tipologia = $tipologia;
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getData_inizio(): int{
        return $this->data_inizio;
    }

    public function getTipologia(): string{
        return $this->tipologia;
    }

    public function getCliente(): Collection{
        return $this->cliente;
    }

    public function setData_inizio(int $data_inizio):self{
        $this->data_inizio = $data_inizio;   
        return $this;                   
    }
    public function setTipologia(string $tipologia):self{
        $this->tipologia = $tipologia;
        return $this;
    }
    public function setCliente(Collection $cliente):self{
        $this->cliente = $cliente;
        return $this;
    }
}

?>