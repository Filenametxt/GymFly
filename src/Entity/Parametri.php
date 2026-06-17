<?php

namespace App\Entity;

use InvalidArgumentException; // CORRETTO: Importata l'eccezione nativa di PHP

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
    private ?float $cosciaDestra = null;
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
        float              $peso,
        float              $altezza,
        \DateTimeImmutable $data,
        Cliente            $cliente,
        ?float             $bicipiteDestro = null,
        ?float             $bicipiteSinistro = null,
        ?float             $tricipiteDestro = null,
        ?float             $tricipiteSinistro = null,
        ?float             $cosciaDestra = null,
        ?float             $cosciaSinistra = null,
        ?float             $polpaccioDestro = null,
        ?float             $polpaccioSinistro = null,
        ?float             $misuraPetto = null,
        ?float             $misuraVita = null,
        ?float             $misuraSpalle = null,
        ?float             $misuraFianchi = null
    ) {
        // CORRETTO: Ogni assegnazione grezza è stata rimossa a favore dei setter
        $this->setPeso($peso);
        $this->setAltezza($altezza);
        $this->setData($data);
        $this->setCliente($cliente);
        
        $this->setBicipiteDestro($bicipiteDestro);
        $this->setBicipiteSinistro($bicipiteSinistro);
        $this->setTricipiteDestro($tricipiteDestro);
        $this->setTricipiteSinistro($tricipiteSinistro);
        $this->setCosciaDestra($cosciaDestra);
        $this->setCosciaSinistra($cosciaSinistra);
        $this->setPolpaccioDestro($polpaccioDestro);
        $this->setPolpaccioSinistro($polpaccioSinistro);
        $this->setMisuraPetto($misuraPetto);
        $this->setMisuraVita($misuraVita);
        $this->setMisuraSpalle($misuraSpalle);
        $this->setMisuraFianchi($misuraFianchi);
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
    public function getCosciaDestra(): ?float
    {
        return $this->cosciaDestra;
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
    // Setter con Validazione Difensiva
    // -------------------------------------------------------------------------

    public function setPeso(float $peso): self
    {
        if ($peso <= 0) {
            throw new InvalidArgumentException('Il peso deve essere strettamente positivo.');
        }
        $this->peso = $peso;
        return $this;
    }

    public function setAltezza(float $altezza): self
    {
        if ($altezza <= 0) {
            throw new InvalidArgumentException("L'altezza deve essere strettamente positiva.");
        }
        $this->altezza = $altezza;
        return $this;
    }

    public function setData(\DateTimeImmutable $data): self
    {
        // CORRETTO: Una misurazione corporea non può essere registrata nel futuro
        if ($data > new \DateTimeImmutable()) {
            throw new InvalidArgumentException("La data di misurazione non può essere nel futuro.");
        }
        $this->data = $data;
        return $this;
    }

    public function setCliente(Cliente $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Setter Campi Opzionali (Controllano la positività del float se non è null)
    // -------------------------------------------------------------------------

    public function setBicipiteDestro(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura del bicipite destro deve essere maggiore di 0.");
        }
        $this->bicipiteDestro = $v;
        return $this;
    }

    public function setBicipiteSinistro(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura del bicipite sinistro deve essere maggiore di 0.");
        }
        $this->bicipiteSinistro = $v;
        return $this;
    }

    public function setTricipiteDestro(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura del tricipite destro deve essere maggiore di 0.");
        }
        $this->tricipiteDestro = $v;
        return $this;
    }

    public function setTricipiteSinistro(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura del tricipite sinistro deve essere maggiore di 0.");
        }
        $this->tricipiteSinistro = $v;
        return $this;
    }

    public function setCosciaDestra(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura della coscia destra deve essere maggiore di 0.");
        }
        $this->cosciaDestra = $v;
        return $this;
    }

    public function setCosciaSinistra(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura della coscia sinistra deve essere maggiore di 0.");
        }
        $this->cosciaSinistra = $v;
        return $this;
    }

    public function setPolpaccioDestro(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura del polpaccio destro deve essere maggiore di 0.");
        }
        $this->polpaccioDestro = $v;
        return $this;
    }

    public function setPolpaccioSinistro(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura del polpaccio sinistro deve essere maggiore di 0.");
        }
        $this->polpaccioSinistro = $v;
        return $this;
    }

    public function setMisuraPetto(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura del petto deve essere maggiore di 0.");
        }
        $this->misuraPetto = $v;
        return $this;
    }

    public function setMisuraVita(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura della vita deve essere maggiore di 0.");
        }
        $this->misuraVita = $v;
        return $this;
    }

    public function setMisuraSpalle(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura delle spalle deve essere maggiore di 0.");
        }
        $this->misuraSpalle = $v;
        return $this;
    }

    public function setMisuraFianchi(?float $v): self
    {
        if ($v !== null && $v <= 0) {
            throw new InvalidArgumentException("La misura dei fianchi deve essere maggiore di 0.");
        }
        $this->misuraFianchi = $v;
        return $this;
    }
}