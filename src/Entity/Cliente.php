<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
class Cliente extends Utente{
    private int $data_di_nascita;   //REVIEW da definire il tipo di dato quando passiamo al database
    private string $luogo_di_nascita;
    private string $indirizzo_di_domicilio;
    private CertificatoMedico $certificato_medico;
    private Abbonamento $abbonamento;
    private string $metodo_di_pagamento;
    private Scheda $scheda;
    private Iscrizione $iscrizione;
    private Parametri $parametri;
    private Collection $progressi;

    public function __construct(string $nome, string $cognome, string $email, string $CF, $profile_picture, int $telefono, string $indirizzo, string $sesso, int $data_di_nascita, string $luogo_di_nascita, string $indirizzo_di_domicilio, CertificatoMedico $certificato_medico, Abbonamento $abbonamento, string $metodo_di_pagamento, Scheda $scheda, Iscrizione $iscrizione, Parametri $parametri){
        parent::__construct($nome, $cognome, $email, $CF, $profile_picture, $telefono, $indirizzo, $sesso);
        $this->data_di_nascita = $data_di_nascita;
        $this->luogo_di_nascita = $luogo_di_nascita;
        $this->indirizzo_di_domicilio = $indirizzo_di_domicilio;
        $this->certificato_medico = $certificato_medico;
        $this->abbonamento = $abbonamento;
        $this->metodo_di_pagamento = $metodo_di_pagamento;
        $this->scheda = $scheda;
        $this->iscrizione = $iscrizione;
        $this->parametri = $parametri;
    }

    public function getData_di_nascita(): int{
        return $this->data_di_nascita;
    }
    public function getLuogo_di_nascita(): string{
        return $this->luogo_di_nascita;
    }
    public function getIndirizzo_di_domicilio(): string{
        return $this->indirizzo_di_domicilio;
    }
    public function getCertificato_medico(): CertificatoMedico{
        return $this->certificato_medico;
    }
    public function getAbbonamento(): Abbonamento{
        return $this->abbonamento;
    }
    public function getMetodo_di_pagamento(): string{
        return $this->metodo_di_pagamento;
    }
    public function getScheda(): Scheda{
        return $this->scheda;
    }
    public function getIscrizione(): Iscrizione{
        return $this->iscrizione;
    }
    public function getParametri(): Parametri{
        return $this->parametri;
    }
    public function getProgressi(): Collection{
        return $this->progressi;
    }
    public function setData_di_nascita(int $data_di_nascita): self{
        $this->data_di_nascita = $data_di_nascita;
        return $this;
    }
    public function setLuogo_di_nascita(string $luogo_di_nascita): self{
        $this->luogo_di_nascita = $luogo_di_nascita;
        return $this;
    }
    public function setIndirizzo_di_domicilio(string $indirizzo_di_domicilio): self{
        $this->indirizzo_di_domicilio = $indirizzo_di_domicilio;
        return $this;
    }
    public function setCertificato_medico (CertificatoMedico $certificato_medico): self{
        $this->certificato_medico = $certificato_medico;
        return $this;
    }
    public function setAbbonamento (Abbonamento $abbonamento):self{
        $this->abbonamento = $abbonamento;
        return $this;
    }
    public function setMetodo_di_pagamento(string $metodo_di_pagamento): self{
        $this->metodo_di_pagamento = $metodo_di_pagamento;
        return $this;
    }
    public function setScheda (Scheda $scheda): self{
        $this->scheda = $scheda;
        return $this;
    }
    public function setIscrizione (Iscrizione $iscrizione): self{
        $this->iscrizione = $iscrizione;
        return $this;
    }
    public function setParametri(Parametri $parametri): self{
        $this->parametri = $parametri;
        return $this;
    }
    public function setProgressi(Collection $progressi): self{
        $this->progressi = $progressi;
        return $this;
    }
}