<?php

namespace App\Entity;

class AbbonamentoDurata extends Abbonamento
{
    private int $durata;

    public function __construct(string $tipologia, string $categoria, int $durata)
    {
        parent::__construct($tipologia, $categoria);
        $this->durata = $durata;
    }

    public function getDurata(): int
    {
        return $this->durata;
    }
    public function setDurata(int $durata): self
    {
        $this->durata = $durata;
        return $this;
    }

    public function calcolaDataFine(\DateTimeImmutable $dataInizio): ?\DateTimeImmutable
    {
        return $dataInizio->modify('+' . $this->durata . ' days'); //data inizio + durata in giorni
    }

    public function isScaduto(AbbonamentoAttivo $context): bool
    {
        if ($context->getDataFine() === null) {
            return false;
        }
        return $context->getDataFine() < new \DateTimeImmutable();
    }

    public function descrizioneScadenza(AbbonamentoAttivo $context): string
    {
        if ($context->getDataFine() === null) {
            return "Nessuna scadenza configurata";
        }
        return "Scade il " . $context->getDataFine()->format('d/m/Y');
    }
}