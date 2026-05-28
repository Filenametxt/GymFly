<?php

class SessionePrivata{

//NOTE: la chiave primaria è Allenatore,ora_inizio e ora_fine
    private $data;
    private \DateTimeImmutable $ora_inizio;
    private \DateTimeImmutable $ora_fine;
    private Cliente $atleta;
    private Allenatore $allenatore;

    public function __construct($data, \DateTimeImmutable $ora_inizio,  \DateTimeImmutable $ora_fine, Cliente $atleta, Allenatore $allenatore){
        $this->data = $data;
        $this->ora_inizio = $ora_inizio;
        $this->ora_fine = $ora_fine;
        $this->atleta = $atleta;
        $this->allenatore = $allenatore;
    }


    public function getData(){
        return $this->data;
    }
    public function getOra_inizio(){
        return $this->ora_inizio;
    }
    public function getOra_fine(){
        return $this->ora_fine;
    }
    public function getAtleta(){
        return $this->atleta;
    }
    public function getAllenatore(){
        return $this->allenatore;
    }

    public function setData($data):self{
        $this->data = $data;
        return $this;
    }
    public function setOra_inizio(\DateTimeImmutable $ora_inizio):self{
        if ($ora_inizio < $this->ora_fine){
            $this->ora_inizio = $ora_inizio;
        }
        else{
            throw new Exception("L'ora inizio non può essere maggiore della ora fine");
        }
        return $this;
    }
    public function setOra_fine($ora_fine):self{
        if ($this->ora_inizio < $ora_fine){
            $this->ora_fine = $ora_fine;
        }
        else{
            throw new Exception("L'ora fine non può essere maggiore della ora inizio");
        }
        return $this;
    }
    public function setAtleta($atleta):self{
        $this->atleta = $atleta;
        return $this;
    }
    
}


?>