<?php
use Doctrine\ORM\Mapping as ORM;

class AbbonamentoMensile extends Abbonamento{

    private int $durata; //REVIEW: revisione per la durata
    
    public function __construct(int $data_inizio, string $tipologia, int $durata){
        parent::__construct($data_inizio, $tipologia);
        $this->durata = $durata;
    }

    public function getDurata(): int{
        return $this->durata;
    }
    public function setDurata(int $durata):self{
        $this->durata = $durata;
        return $this;
    }    
}
?>