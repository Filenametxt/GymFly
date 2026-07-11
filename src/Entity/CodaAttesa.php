<?php

namespace App\Entity;

class CodaAttesa
{
    private ?int $id = null;
    private Cliente $cliente;
    private AttivitaPianificata $attivitaPianificata;
    private \DateTimeImmutable $dataInserimento;

    public function __construct(Cliente $cliente, AttivitaPianificata $attivitaPianificata)
    {
        $this->cliente = $cliente;
        $this->attivitaPianificata = $attivitaPianificata;
        $this->dataInserimento = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCliente(): Cliente
    {
        return $this->cliente;
    }

    public function getAttivitaPianificata(): AttivitaPianificata
    {
        return $this->attivitaPianificata;
    }

    public function getDataInserimento(): \DateTimeImmutable
    {
        return $this->dataInserimento;
    }
}
