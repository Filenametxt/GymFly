<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

abstract class Abbonamento{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    private string $categoria;
    private Collection $cliente;

    public function __construct(string $categoria){
        $this->categoria = $categoria;
    }

    public function getId(): ?int{
        return $this->id;
    }


    public function getcategoria(): string{
        return $this->categoria;
    }

    public function getCliente(): Collection{
        return $this->cliente;
    }

    public function setcategoria(string $categoria):self{
        $this->categoria = $categoria;
        return $this;
    }
    public function setCliente(Collection $cliente):self{
        $this->cliente = $cliente;
        return $this;
    }


    //TODO: gestionde dell'abbonamento scaduto
}

?>