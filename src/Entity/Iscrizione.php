<?php

namespace App\Entity;

class Iscrizione
{
    private ?int $id = null;
    private \DateTimeImmutable $dataInizio;
    private \DateTimeImmutable $dataFine;   // calcolata: +1 anno da dataInizio
    private Cliente $cliente;

    public function __construct(
        \DateTimeImmutable $dataInizio,
        Cliente $cliente,
    ) {
        $this->setDataInizio($dataInizio);
        $this->setCliente($cliente);
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDataInizio(): \DateTimeImmutable
    {
        return $this->dataInizio;
    }
    public function getDataFine(): \DateTimeImmutable
    {
        return $this->dataFine;
    }
    public function getCliente(): Cliente
    {
        return $this->cliente;
    }

    // -------------------------------------------------------------------------
    // Setter
    // -------------------------------------------------------------------------

    /**
     * Aggiorna la data di inizio e ricalcola automaticamente la data di fine.
     */
    public function setDataInizio(\DateTimeImmutable $dataInizio): self
    {
        $oggi = new \DateTimeImmutable();

        // Controllo difensivo 1: Impedisce l'attivazione di iscrizioni nel futuro remoto
        if ($dataInizio > $oggi->modify('+1 day')) { 
            throw new \InvalidArgumentException("La data di inizio dell'iscrizione non può essere futura.");
        }
        $this->dataInizio = $dataInizio;
        $this->dataFine = $dataInizio->modify('+1 year');
        return $this;
    }

    public function setCliente(Cliente $cliente): self
    {
        $this->cliente = $cliente;

        // BIUNIVOCITÀ: Se il cliente non punta ancora a questa iscrizione, sincronizzalo
        if ($cliente->getIscrizione() !== $this) {
            $cliente->setIscrizione($this);
        }

        return $this;
    }

    // -------------------------------------------------------------------------
    // Regole di dominio
    // -------------------------------------------------------------------------

    /**
     * Verifica se l'iscrizione è ancora attiva.
     */
    public function isAttiva(): bool
    {
        return $this->dataFine > new \DateTimeImmutable();
    }

    /**
     * Restituisce i giorni rimanenti all'iscrizione (0 se scaduta).
     */
    public function giorniRimanenti(): int
    {
        if (!$this->isAttiva())
            return 0;
        return (new \DateTimeImmutable())->diff($this->dataFine)->days;
    }
}