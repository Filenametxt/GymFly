<?php
use Doctrine\ORM\Mapping as ORM;
class AbbonamentoAttivo{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    private \DateTimeImmutable $data_inizio;
    private Abbonamento $abbonamento;


    public function __construct(\DateTimeImmutable $data_inizio, Abbonamento $abbonamento){
        $this->data_inizio = $data_inizio;
        $this->abbonamento = $abbonamento;

    }
    public function getId(): ?int{
        return $this->id;
    }

    public function getData_inizio(): \DateTimeImmutable{
        return $this->data_inizio;
    }
    public function getAbbonamento(): Abbonamento{
        return $this->abbonamento;
    }

    public function setData_inizio(\DateTimeImmutable $data_inizio): self{
        $this->data_inizio=$data_inizio;
        return $this;
    }
    public function setAbbonamento(Abbonamento $abbonamento): self{
        $this->abbonamento=$abbonamento;
        return $this;
    }

}
?>