<?php
use Doctrine\ORM\Mapping as ORM;

class Tipologia{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private string $nome_tipologia;

    public function __construct(string $nome_tipologia){
        $this->nome_tipologia = $nome_tipologia;
    }

    public function getId(){
        return $this->id;
    }
    public function getNomeTipologia(){
        return $this->nome_tipologia;
    }
    public function setNomeTipologia($nome_tipologia):self{
        $this->nome_tipologia = $nome_tipologia;
        return $this;
    }
}

?>