<?php
use Doctrine\ORM\Mapping as ORM;

class Attrezzatura{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    private string $nome_attrezzatura;
    
    public function __construct(string $nome_attrezzatura){
        $this->nome_attrezzatura = $nome_attrezzatura;
    }
    public function getId(){
        return $this->id;
    }

    public function getNomeAttrezzatura(){
        return $this->nome_attrezzatura;
    }
    public function setNomeAttrezzatura($nome_attrezzatura):self{
        $this->nome_attrezzatura = $nome_attrezzatura;
        return $this;
    }
    
}
?>