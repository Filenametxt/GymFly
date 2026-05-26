<?php
class Scheda{
    private $nome_scheda;
    private $data_inizio;
    private $data_fine;
    private $obiettivo;

    public function __construct($nome_scheda, $data_inizio, $data_fine, $obiettivo){
        $this->nome_scheda = $nome_scheda;
        $this->data_inizio = $data_inizio;
        $this->data_fine = $data_fine;
        $this->obiettivo = $obiettivo;
    }
    public function getNome_scheda(){
        return $this->nome_scheda;
    }
    public function getData_inizio(){
        return $this->data_inizio;
    }
    public function getData_fine(){
        return $this->data_fine;
    }
    public function getObiettivo(){
        return $this->obiettivo;
    }

    public function setNome_scheda($nome_scheda):self{
        $this->nome_scheda = $nome_scheda;
        return $this;
    }
    public function setData_inizio($data_inizio): self{
        $this->data_inizio = $data_inizio;
        return $this;
    }

    public function setData_fine($data_fine): self{
        $this->data_fine = $data_fine;
        return $this;
    }
    public function setObiettivo($obiettivo):self{
        $this->obiettivo = $obiettivo;
        return $this;
    }



}