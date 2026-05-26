<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

class Esercizio{
    


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private ?string  $nome_esercizio;
    private Collection $gruppiMuscolari; 
    private Attrezzatura $attrezzatura_necessaria; 

    private ?string $descrizione;
    private Tipologia $tipologia;        

    private ?Allenatore $creatore;

    public function __construct( ?string $nome_esercizio, Attrezzatura $attrezzatura_necessaria, ?string $descrizione, Tipologia $tipologia){
        $this->nome_esercizio = $nome_esercizio;
        $this->attrezzatura_necessaria = $attrezzatura_necessaria;
        $this->descrizione = $descrizione;
        $this->tipologia = $tipologia;
    }
    public function getId(){
        return $this->id;
    }

    public function getNome_esercizio(): ?string {
        return $this->nome_esercizio;
    }
    public function getGruppo_muscolare(): Collection{
        return $this->gruppiMuscolari;
    }

    public function getAttrezzatura_necessaria(){
        return $this->attrezzatura_necessaria;
    }
    public function getDescrizione(){
        return $this->descrizione;
    }
    public function getTipologia(){
        return $this->tipologia;
    }
    public function getCreatore():?Allenatore{
        return $this->creatore;
    }

    public function setNome_esercizio(?string $nome_esercizio){
        $this->nome_esercizio = $nome_esercizio;
    }

    public function setGruppo_muscolare(Collection $gruppiMuscolari) :self{
        $this->gruppiMuscolari = $gruppiMuscolari;
        return $this;
    }
    public function setAttrezzatura_necessaria($attrezzatura_necessaria):self{
        $this->attrezzatura_necessaria = $attrezzatura_necessaria;
        return $this;
    }
    public function setDescrizione($descrizione):self{
        $this->descrizione = $descrizione;
        return $this;
    }
    public function setTipologia($tipologia):self{
        $this->tipologia = $tipologia;
        return $this;
    }
    public function setCreatore(?Allenatore $creatore):self{
        $this->creatore = $creatore;
        return $this;
    }
    


}
?>