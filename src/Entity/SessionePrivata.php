<?php

class SessionePrivata{
    private $data;
    private $ora_inizio;
    private $ora_fine;
    private Cliente $atleta;
    private Allenatore $allenatore;

    public function __construct($data, $ora_inizio, $ora_fine, Cliente $atleta, Allenatore $allenatore){
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
    public function setOra_inizio($ora_inizio):self{
        $this->ora_inizio = $ora_inizio;
        return $this;
    }
    public function setOra_fine($ora_fine):self{
        $this->ora_fine = $ora_fine;
        return $this;
    }
    public function setAtleta($atleta):self{
        $this->atleta = $atleta;
        return $this;
    }
    
}


?>