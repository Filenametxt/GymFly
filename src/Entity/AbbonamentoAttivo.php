<?php
namespace App\Entity;

class AbbonamentoAttivo
{
    private ?int $id = null;
    private \DateTimeImmutable $dataInizio;
    private ?\DateTimeImmutable $dataFine = null;
    private Abbonamento $abbonamento;

    public function __construct(\DateTimeImmutable $dataInizio, Abbonamento $abbonamento) {
        $this->dataInizio = $dataInizio;
        $this->abbonamento = $abbonamento;
        $this->dataFine = $abbonamento->calcolaDataFine($dataInizio);
    }

    public function getId(): ?int { return $this->id; }
    public function getDataInizio(): \DateTimeImmutable { return $this->dataInizio; }
    public function getDataFine(): ?\DateTimeImmutable { return $this->dataFine; }
    public function getAbbonamento(): Abbonamento { return $this->abbonamento; }

    public function setDataInizio(\DateTimeImmutable $dataInizio): self {
        $this->dataInizio = $dataInizio;
        $this->dataFine = $this->abbonamento->calcolaDataFine($dataInizio);
        return $this;
    }

    public function setAbbonamento(Abbonamento $abbonamento): self {
        $this->abbonamento = $abbonamento;
        $this->dataFine = $abbonamento->calcolaDataFine($this->dataInizio);
        return $this;
    }

    // Delega polimorfica completa: non gli interessa come si calcola la scadenza
    public function isScaduto(): bool {
        return $this->abbonamento->isScaduto($this);
    }

    public function getDescrizioneScadenza(): string {
        return $this->abbonamento->descrizioneScadenza($this);
    }

    public function giorniRimanenti(): ?int {
        if ($this->dataFine === null) {
            return null; 
        }
        if ($this->isScaduto()) {
            return 0;
        }
        return (new \DateTimeImmutable())->diff($this->dataFine)->days;
    }
}