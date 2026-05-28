<?php
use Doctrine\ORM\Mapping as ORM;

class Scheda{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    private string $nome_scheda;
    private $data_inizio;
    private $data_fine;
    private string $obiettivo;
    private Cliente $cliente;
    private Allenatore $allenatore;

    public function __construct(string $nome_scheda, $data_inizio, $data_fine,string  $obiettivo, Cliente $cliente, Allenatore $allenatore){
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
    public function getData_inizio(){
        return $this->data_inizio;
    }
    public function getData_fine(){
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
    public function setData_inizio($data_inizio): self{
        $this->data_inizio = $data_inizio;
        return $this;
    }

    public function setData_fine($data_fine): self{
        $this->data_fine = $data_fine;
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