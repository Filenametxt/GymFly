<?php

namespace App\Entity;

/**
 * Classe SessionePrivata.
 * La chiave primaria è composta da: allenatore, ora_inizio, ora_fine.
 * Il mapping ORM è definito esternamente in:
 *   foundation/App.Entity.SessionePrivata.dcm.xml
 */
class SessionePrivata
{
    private \DateTimeImmutable $data;
    private \DateTimeImmutable $oraInizio;
    private \DateTimeImmutable $oraFine;
    private Cliente $atleta;
    private Allenatore $allenatore;

    public function __construct(
        \DateTimeImmutable $data,
        \DateTimeImmutable $oraInizio,
        \DateTimeImmutable $oraFine,
        Cliente $atleta,
        Allenatore $allenatore
    ) {
        if ($oraInizio >= $oraFine) {
            throw new \InvalidArgumentException("L'ora di inizio non può essere maggiore o uguale all'ora di fine.");
        }
        $this->setData($data);

        // 1. Assegnazione diretta "grezza": inizializziamo le proprietà in memoria.
        // Da questo momento in poi, $this->oraInizio e $this->oraFine ESISTONO e sono leggibili.
        $this->oraInizio = $oraInizio;
        $this->oraFine = $oraFine;

        // 2. Chiamata ai setter: ora i metodi possono fare i controlli incrociati 
        // senza mandare PHP in crash, perché guardano proprietà che esistono già!
        $this->setOraInizio($oraInizio);
        $this->setOraFine($oraFine);

        $this->setAtleta($atleta);              //in questo caso questa entità per esistere ha bisogno assoluto sia di atleta che di allenatore
        $this->setAllenatore($allenatore);
    }

    public function getData(): \DateTimeImmutable
    {
        return $this->data;
    }

    public function getOraInizio(): \DateTimeImmutable
    {
        return $this->oraInizio;
    }

    public function getOraFine(): \DateTimeImmutable
    {
        return $this->oraFine;
    }

    public function getAtleta(): Cliente
    {
        return $this->atleta;
    }

    public function getAllenatore(): Allenatore
    {
        return $this->allenatore;
    }

    public function setData(\DateTimeImmutable $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function setOraInizio(\DateTimeImmutable $oraInizio): self
    {
        if ($oraInizio >= $this->oraFine) {
            throw new \InvalidArgumentException("L'ora di inizio non può essere maggiore o uguale all'ora di fine.");
        }
        $this->oraInizio = $oraInizio;
        return $this;
    }

    public function setOraFine(\DateTimeImmutable $oraFine): self
    {
        if ($this->oraInizio >= $oraFine) {
            throw new \InvalidArgumentException("L'ora di fine non può essere minore o uguale all'ora di inizio.");
        }
        $this->oraFine = $oraFine;
        return $this;
    }

    public function setAtleta(Cliente $atleta): self
    {
        $this->atleta = $atleta;
        return $this;
    }

    public function setAllenatore(Allenatore $allenatore): self
    {
        $this->allenatore = $allenatore;
        return $this;
    }
}
?>