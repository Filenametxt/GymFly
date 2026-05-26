<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

class GruppoMuscolare{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    private string $nome_gruppo_muscolare;
    private Collection $esercizi; 

    public function __construct(string $nome_gruppo_muscolare){
        $this->nome_gruppo_muscolare = $nome_gruppo_muscolare;
    }
}

?>