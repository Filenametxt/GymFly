<?php

namespace App\Entity;

class Parametri
{
    private ?int $id = null;
    private float $peso;
    private float $altezza;
    private \DateTimeImmutable $data;
    private ?float $bicipiteDestro = null;
    private ?float $bicipiteSinistro = null;
    private ?float $tricipiteDestro = null;
    private ?float $tricipiteSinistro = null;
    private ?float $casciaDestro = null;
    private ?float $cosciaSinistra = null;
    private ?float $polpaccioDestro = null;
    private ?float $polpaccioSinistro = null;
    private ?float $misuraPetto = null;
    private ?float $misuraVita = null;
    private ?float $misuraSpalle = null;
    private ?float $misuraFianchi = null;

    // M-1 con Cliente — NO biunivocità, Parametri conosce Cliente
    private Cliente $cliente;

    public function __construct(
        float $peso,
        float $altezza,
        \DateTimeImmutable $data,
        Cliente $cliente,
        ?float $bicipiteDestro = null,
        ?float $bicipiteSinistro = null,
        ?float $tricipiteDestro = null,
        ?float $tricipiteSinistro = null,
        ?float $cosciaDestro = null,
        ?float $cosciaSinistra = null,
        ?float $polpaccioDestro = null,
        ?float $polpaccioSinistro = null,
        ?float $misuraPetto = null,
        ?float $misuraVita = null,
        ?float $misuraSpalle = null,
        ?float $misuraFianchi = null,
    ) {
        $this->peso = $peso;
        $this->altezza = $altezza;
        $this->data = $data;
        $this->cliente = $cliente;
        $this->bicipiteDestro = $bicipiteDestro;
        $this->bicipiteSinistro = $bicipiteSinistro;
        $this->tricipiteDestro = $tricipiteDestro;   // BUGFIX: era assegnato $bicipiteDestro
        $this->tricipiteSinistro = $tricipiteSinistro;
        $this->casciaDestro = $cosciaDestro;
        $this->cosciaSinistra = $cosciaSinistra;
        $this->polpaccioDestro = $polpaccioDestro;
        $this->polpaccioSinistro = $polpaccioSinistro;
        $this->misuraPetto = $misuraPetto;
        $this->misuraVita = $misuraVita;        // BUGFIX: era non assegnato
        $this->misuraSpalle = $misuraSpalle;
        $this->misuraFianchi = $misuraFianchi;     // BUGFIX: era assegnato due volte
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getPeso(): float
    {
        return $this->peso;
    }
    public function getAltezza(): float
    {
        return $this->altezza;
    }
    public function getData(): \DateTimeImmutable
    {
        return $this->data;
    }
    public function getCliente(): Cliente
    {
        return $this->cliente;
    }
    public function getBicipiteDestro(): ?float
    {
        return $this->bicipiteDestro;
    }
    public function getBicipiteSinistro(): ?float
    {
        return $this->bicipiteSinistro;
    }
    public function getTricipiteDestro(): ?float
    {
        return $this->tricipiteDestro;
    }
    public function getTricipiteSinistro(): ?float
    {
        return $this->tricipiteSinistro;
    }
    public function getCosciaDestro(): ?float
    {
        return $this->casciaDestro;
    }
    public function getCosciaSinistra(): ?float
    {
        return $this->cosciaSinistra;
    }
    public function getPolpaccioDestro(): ?float
    {
        return $this->polpaccioDestro;
    }
    public function getPolpaccioSinistro(): ?float
    {
        return $this->polpaccioSinistro;
    }
    public function getMisuraPetto(): ?float
    {
        return $this->misuraPetto;
    }
    public function getMisuraVita(): ?float
    {
        return $this->misuraVita;
    }
    public function getMisuraSpalle(): ?float
    {
        return $this->misuraSpalle;
    }
    public function getMisuraFianchi(): ?float
    {
        return $this->misuraFianchi;
    }

    // -------------------------------------------------------------------------
    // Setter
    // -------------------------------------------------------------------------

    public function setPeso(float $peso): self
    {
        if ($peso <= 0)
            throw new \InvalidArgumentException('Il peso deve essere positivo.');
        $this->peso = $peso;
        return $this;
    }

    public function setAltezza(float $altezza): self
    {
        if ($altezza <= 0)
            throw new \InvalidArgumentException('L\'altezza deve essere positiva.');
        $this->altezza = $altezza;
        return $this;
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
    public function setBicipiteDestro(?float $v): self
    {
        $this->bicipiteDestro = $v;
        return $this;
    }
    public function setBicipiteSinistro(?float $v): self
    {
        $this->bicipiteSinistro = $v;
        return $this;
    }
    public function setTricipiteDestro(?float $v): self
    {
        $this->tricipiteDestro = $v;
        return $this;
    }
    public function setTricipiteSinistro(?float $v): self
    {
        $this->tricipiteSinistro = $v;
        return $this;
    }
    public function setCosciaDestro(?float $v): self
    {
        $this->casciaDestro = $v;
        return $this;
    }
    public function setCosciaSinistra(?float $v): self
    {
        $this->cosciaSinistra = $v;
        return $this;
    }
    public function setPolpaccioDestro(?float $v): self
    {
        $this->polpaccioDestro = $v;
        return $this;
    }
    public function setPolpaccioSinistro(?float $v): self
    {
        $this->polpaccioSinistro = $v;
        return $this;
    }
    public function setMisuraPetto(?float $v): self
    {
        $this->misuraPetto = $v;
        return $this;
    }
    public function setMisuraVita(?float $v): self
    {
        $this->misuraVita = $v;
        return $this;
    }
    public function setMisuraSpalle(?float $v): self
    {
        $this->misuraSpalle = $v;
        return $this;
    }
    public function setMisuraFianchi(?float $v): self
    {
        $this->misuraFianchi = $v;
        return $this;
    }

}