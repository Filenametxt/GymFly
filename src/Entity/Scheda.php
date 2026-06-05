<?php
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
class Scheda{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    private string $nome_scheda;
    private \DateTimeImmutable $data_inizio;
    private \DateTimeImmutable $data_fine;
    private string $obiettivo;
    private Cliente $cliente;
    private Allenatore $allenatore;

    public function __construct(string $nome_scheda, \DateTimeImmutable $data_inizio, \DateTimeImmutable $data_fine,string  $obiettivo, Cliente $cliente, Allenatore $allenatore){
        $this->nome_scheda = $nome_scheda;
        $this->data_inizio = $data_inizio;
        $this->data_fine = $data_fine;
        $this->obiettivo = $obiettivo;
        $this->cliente = $cliente;
        $this->allenatore = $allenatore;
    }

    public function getId(){
        return $this->id;
    }

    public function getNome_scheda(): string{
        return $this->nome_scheda;
    }
    public function getData_inizio(): \DateTimeImmutable{
        return $this->data_inizio;
    }
    public function getData_fine(): \DateTimeImmutable{
        return $this->data_fine;
    }
    public function getObiettivo(): string{
        return $this->obiettivo;
    }
    public function getCliente(): Cliente{
        return $this->cliente;
    }
    public function getAllenatore(): Allenatore{
        return $this->allenatore;
    }

    public function setCliente(Cliente $cliente): self{
        $this->cliente = $cliente;
        return $this;
    }

    public function setNome_scheda(string $nome_scheda):self{
        $this->nome_scheda = $nome_scheda;
        return $this;
    }
    public function setData_inizio(\DateTimeImmutable $data_inizio): self{
        if ($this->data_fine > $data_inizio){
            $this->data_inizio = $data_inizio;
        }
        else{
            throw new Exception("La data di inizio deve essere inferiore alla data di fine");
        }
        return $this;
    }

    public function setData_fine(\DateTimeImmutable $data_fine): self{

        if ($this->data_inizio < $data_fine){
            $this->data_fine = $data_fine;
        }
        else{
            throw new Exception("La data di fine non può essere maggiore della data di inizio");
        }
        return $this;
    }
    public function setObiettivo(string $obiettivo):self{
        $this->obiettivo = $obiettivo;
        return $this;
    }
    public function setAllenatore(Allenatore $allenatore):self{
        $this->allenatore = $allenatore;
        return $this;
    }
}