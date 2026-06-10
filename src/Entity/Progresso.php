<?php

namespace App\Entity;

abstract class Progresso
{
    protected ?int $id = null;
    protected \DateTimeImmutable $data;
    protected Cliente $cliente;
    protected Esercizio $esercizio;

    public function __construct(\DateTimeImmutable $data, Cliente $cliente, Esercizio $esercizio)
    {
        $this->data = $data;
        $this->cliente = $cliente;
        $this->esercizio = $esercizio;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getData(): \DateTimeImmutable
    {
        return $this->data;
    }
    public function getCliente(): Cliente
    {
        return $this->cliente;
    }
    public function getEsercizio(): Esercizio
    {
        return $this->esercizio;
    }

    public function setData(\DateTimeImmutable $data): self
    {
        $this->data = $data;
        return $this;
    }
    public function setCliente(Cliente $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }
    public function setEsercizio(Esercizio $esercizio): self
    {
        $this->esercizio = $esercizio;
        return $this;
    }
    
}