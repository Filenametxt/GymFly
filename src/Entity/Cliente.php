<?php

namespace App\Entity;

use GymFly\Enum\Sesso;

class Cliente extends Utente
{
    private \DateTimeImmutable $dataDiNascita;
    private string $luogoDiNascita;
    private string $indirizzoDiDomicilio;
    private string $metodoDiPagamento;

    // 1-1 con CertificatoMedico — Cliente owner, biunivoca
    private ?CertificatoMedico $certificatoMedico = null;

    // 1-1 con AbbonamentoAttivo — Cliente owner, NO biunivocità
    private ?AbbonamentoAttivo $abbonamento = null;

    // 1-1 con Iscrizione — biunivoca
    private ?Iscrizione $iscrizione = null;

    // 1-1 con Scheda — biunivoca
    private ?Scheda $scheda = null;

    // M-1 con Palestra — NO biunivocità, Cliente conosce Palestra
    private ?Palestra $palestra = null;

    // 1-N con Progresso — biunivoca, Cliente ha array di progressi
    private array $progressi = [];

    // N-N con AttivitaPianificata — tabella ISCRITTO
    private array $attivitaPianificate = [];

    public function __construct(
        string $nome,
        string $cognome,
        string $email,
        string $CF,
        string $indirizzo,
        Sesso $sesso,
        \DateTimeImmutable $dataDiNascita,
        string $luogoDiNascita,
        string $indirizzoDiDomicilio,
        string $metodoDiPagamento,
        string $password = "",
        ?string $profilePicture = null,
        ?string $telefono = null,
    ) {
        parent::__construct(
            $nome,
            $cognome,
            $email,
            $CF,
            $indirizzo,
            $sesso,
            $password,
            $profilePicture,
            $telefono
        );

        $this->dataDiNascita = $dataDiNascita;
        $this->luogoDiNascita = $luogoDiNascita;
        $this->indirizzoDiDomicilio = $indirizzoDiDomicilio;
        $this->metodoDiPagamento = $metodoDiPagamento;
    }

    // -------------------------------------------------------------------------
    // Metodi astratti ereditati da Utente
    // -------------------------------------------------------------------------

    public function mssAllowed(): bool
    {
        return false;
    }

    public function getRuolo(): string
    {
        return 'cliente';
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

    public function getDataDiNascita(): \DateTimeImmutable
    {
        return $this->dataDiNascita;
    }
    public function getLuogoDiNascita(): string
    {
        return $this->luogoDiNascita;
    }
    public function getIndirizzoDiDomicilio(): string
    {
        return $this->indirizzoDiDomicilio;
    }
    public function getMetodoDiPagamento(): string
    {
        return $this->metodoDiPagamento;
    }
    public function getCertificatoMedico(): ?CertificatoMedico
    {
        return $this->certificatoMedico;
    }
    public function getAbbonamento(): ?AbbonamentoAttivo
    {
        return $this->abbonamento;
    }
    public function getIscrizione(): ?Iscrizione
    {
        return $this->iscrizione;
    }
    public function getScheda(): ?Scheda
    {
        return $this->scheda;
    }
    public function getPalestra(): ?Palestra
    {
        return $this->palestra;
    }

    /** @return Progresso[] */
    public function getProgressi(): array
    {
        return $this->progressi;
    }

    /** @return AttivitaPianificata[] */
    public function getAttivitaPianificate(): array
    {
        return $this->attivitaPianificate;
    }

    // -------------------------------------------------------------------------
    // Setter
    // -------------------------------------------------------------------------

    public function setDataDiNascita(\DateTimeImmutable $data): self
    {
        if ($data > new \DateTimeImmutable()) {
            throw new \InvalidArgumentException('La data di nascita non può essere nel futuro.');
        }
        $this->dataDiNascita = $data;
        return $this;
    }

    public function setLuogoDiNascita(string $luogo): self
    {
        $this->luogoDiNascita = $luogo;
        return $this;
    }

    public function setIndirizzoDiDomicilio(string $indirizzo): self
    {
        $this->indirizzoDiDomicilio = $indirizzo;
        return $this;
    }

    public function setMetodoDiPagamento(string $metodo): self
    {
        $this->metodoDiPagamento = $metodo;
        return $this;
    }

    public function setPalestra(?Palestra $palestra): self
    {
        $this->palestra = $palestra;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Gestione relazioni biunivoche
    // -------------------------------------------------------------------------

    /**
     * 1-1 con CertificatoMedico — biunivoca.
     * Aggiorna anche il lato CertificatoMedico.
     */
    public function setCertificatoMedico(?CertificatoMedico $cert): self
    {
        $this->certificatoMedico = $cert;
        if ($cert !== null && $cert->getCliente() !== $this) {
            $cert->setCliente($this);
        }
        return $this;
    }

    /**
     * 1-1 con AbbonamentoAttivo — NO biunivocità, solo Cliente conosce.
     */
    public function setAbbonamento(?AbbonamentoAttivo $abbonamento): self
    {
        $this->abbonamento = $abbonamento;
        return $this;
    }

    /**
     * 1-1 con Iscrizione — biunivoca.
     * Aggiorna anche il lato Iscrizione.
     */
    public function setIscrizione(?Iscrizione $iscrizione): self
    {
        $this->iscrizione = $iscrizione;
        if ($iscrizione !== null && $iscrizione->getCliente() !== $this) {
            $iscrizione->setCliente($this);
        }
        return $this;
    }

    /**
     * 1-1 con Scheda — biunivoca.
     * Aggiorna anche il lato Scheda.
     */
    public function setScheda(?Scheda $scheda): self
    {
        $this->scheda = $scheda;
        if ($scheda !== null && $scheda->getCliente() !== $this) {
            $scheda->setCliente($this);
        }
        return $this;
    }

    /**
     * 1-N con Progresso — biunivoca.
     * Aggiorna anche il lato Progresso.
     */
    public function aggiungiProgresso(Progresso $progresso): self
    {
        $this->progressi[] = $progresso;
        if ($progresso->getCliente() !== $this) {
            $progresso->setCliente($this);
        }
        return $this;
    }

    /**
     * N-N con AttivitaPianificata — tabella ISCRITTO.
     * Aggiorna anche il lato AttivitaPianificata.
     */
    public function iscriviAAttivita(AttivitaPianificata $attivita): self
    {
        foreach ($this->attivitaPianificate as $a) {
            if ($a === $attivita)
                return $this;
        }
        $this->attivitaPianificate[] = $attivita;
        $attivita->aggiungiCliente($this);
        return $this;
    }

    public function cancellaIscrizioneAttivita(AttivitaPianificata $attivita): self
    {
        // 1. Cerca l'indice esatto dell'attività (il 'true' garantisce che sia lo stesso identico oggetto)
        $indice = array_search($attivita, $this->attivitaPianificate, true);
        
        if ($indice !== false) {
            // 2. Rimuove l'attività dall'array
            unset($this->attivitaPianificate[$indice]);
            
            // 3. Aggiorna il lato inverso della relazione
            $attivita->rimuoviCliente($this);
        }
        return $this;
    }

    // -------------------------------------------------------------------------
    // Regole di dominio
    // -------------------------------------------------------------------------

    /**
     * Verifica se l'abbonamento del cliente è attivo.
     */
    public function isAbbonamentoAttivo(): bool
    {
        return $this->abbonamento !== null
            && !$this->abbonamento->isScaduto();
    }

    /**
     * Verifica se il certificato medico è valido.
     */
    public function isCertificatoValido(): bool
    {
        return $this->certificatoMedico !== null
            && $this->certificatoMedico->isValido();
    }

    /**
     * Verifica se il cliente può prenotare un'attività.
     * Richiede abbonamento attivo e certificato medico valido.
     */
    public function puoPrenotareAttivita(): bool
    {
        return $this->isAbbonamentoAttivo() && $this->isCertificatoValido();
    }
}
?>