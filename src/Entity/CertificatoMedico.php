<?php

namespace App\Entity;

class CertificatoMedico
{
    private ?int $id = null;
    private \DateTimeImmutable $dataEmissione;
    private \DateTimeImmutable $dataScadenza;   // calcolata automaticamente: +1 anno da emissione
    private string $medico;
    private $fileContent = null; // contenuto binario del file (BLOB)
    private Cliente $cliente;

    public function __construct(
        \DateTimeImmutable $dataEmissione,
        string $medico,
        Cliente $cliente,
        ?string $fileContent = null,
    ) {
        $this->dataEmissione = $dataEmissione;
        // data scadenza calcolata automaticamente: 1 anno dopo l'emissione
        $this->dataScadenza = $dataEmissione->modify('+1 year');
        $this->medico = $medico;
        $this->cliente = $cliente;
        $this->fileContent = $fileContent;
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDataEmissione(): \DateTimeImmutable
    {
        return $this->dataEmissione;
    }
    public function getDataScadenza(): \DateTimeImmutable
    {
        return $this->dataScadenza;
    }
    public function getMedico(): string
    {
        return $this->medico;
    }
    public function getFileContent(): ?string
    {
        if (is_resource($this->fileContent)) {
            rewind($this->fileContent);
            return stream_get_contents($this->fileContent);
        }
        return $this->fileContent;
    }
    public function getCliente(): Cliente
    {
        return $this->cliente;
    }

    // -------------------------------------------------------------------------
    // Setter
    // -------------------------------------------------------------------------

    /**
     * Aggiorna la data di emissione e ricalcola automaticamente la scadenza.
     */
    public function setDataEmissione(\DateTimeImmutable $dataEmissione): self
    {
        $this->dataEmissione = $dataEmissione;
        $this->dataScadenza = $dataEmissione->modify('+1 year');
        return $this;
    }

    public function setMedico(string $medico): self
    {
        $this->medico = $medico;
        return $this;
    }

    public function setFileContent(?string $fileContent): self
    {
        $this->fileContent = $fileContent;
        return $this;
    }

    public function setCliente(Cliente $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Regole di dominio
    // -------------------------------------------------------------------------

    /**
     * Verifica se il certificato medico è ancora valido alla data odierna.
     */
    public function isValido(): bool
    {
        return $this->dataScadenza > new \DateTimeImmutable();
    }

    /**
     * Restituisce i giorni mancanti alla scadenza (negativo se già scaduto).
     * %a: È il formato di DateInterval che restituisce i giorni totali di differenza tra le due date.
     * %R: È il formato che aggiunge automaticamente il segno (+ se la data di scadenza è futura, - se la data di scadenza è passata).
     * (int): Facendo il casting a intero, stringhe come "+10" o "-5" diventano istantaneamente i numeri interi.
     */
    public function giorniAllaScadenza(): int
    {
        return (int) (new \DateTimeImmutable())->diff($this->dataScadenza)->format('%R%a');
    }
}