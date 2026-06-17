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
        $this->setData($data);
        $this->setCliente($cliente);
        $this->setEsercizio($esercizio);
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
        //Un progresso non può essere registrato in una data futura
        if ($data > new \DateTimeImmutable()) {
            throw new \InvalidArgumentException("La data del progresso non può essere nel futuro.");
        }
        
        $this->data = $data;
        return $this;
    }
    public function setCliente(Cliente $cliente): self
    {
        $this->cliente = $cliente;
        
        // CORRETTO: Garantisce la biunivocità in memoria con la collection di Cliente
        if (!$cliente->getProgressi()->contains($this)) {
            $cliente->aggiungiProgresso($this);
        }
        
        return $this;
    }
    public function setEsercizio(Esercizio $esercizio): self
    {
        $this->esercizio = $esercizio;
        return $this;
    }
    
}