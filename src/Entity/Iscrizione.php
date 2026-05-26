<?php
class Iscrizione{
    private ?int $id = null;
    private int $data_inizio;
    private int $data_fine;
    private Cliente $cliente;

    public function __construct(int $data_inizio, int $data_fine, Cliente $cliente){
        $this->data_inizio = $data_inizio;
        $this->data_fine = $data_fine;
    }
    public function getId(): ?int{
        return $this->id;
    }
    public function getData_inizio(): int{
        return $this->data_inizio;
    }
    public function getData_fine(): int{
        return $this->data_fine;
    }
    public function getCliente(): Cliente{
        return $this->cliente;
    }
    public function setData_inizio(int $data_inizio): self{
        $this->data_inizio = $data_inizio;
        return $this;
    }
    public function setData_fine(int $data_fine): self{
        $this->data_fine = $data_fine;
        return $this;
    }
    public function setCliente(Cliente $cliente): self{
        $this->cliente = $cliente;
        return $this;
    }
}
?>