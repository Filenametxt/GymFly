<?php
use Doctrine\ORM\Mapping as ORM;

class Parametri{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private float $peso;
    private float $altezza;
    private int $data;  //REVIEW: revisione per la data
    private ?float $bicipite_destro;
    private ?float $bicipite_sinistro;
    private ?float $tricipite_destro;
    private ?float $tricipite_sinistro;
    private ?float $coscia_destra;
    private ?float $coscia_sinistra;
    private ?float $polpaccio_destro;
    private ?float $polpaccio_sinistro;
    private ?float $misura_petto;
    private ?float $misura_vita;
    private ?float $misura_spalle;
    private ?float $misura_fianchi;  

    private Cliente $cliente;

    function __construct($peso, $altezza, $bicipite_destro, $bicipite_sinistro, $tricipite_destro, $data, $tricipite_sinistro, $coscia_destra, $coscia_sinistra,$polpaccio_destro, $polpaccio_sinistro, $misura_fianchi, $misura_petto, $misura_spalle, $misura_vita){
        $this->peso=$peso;
        $this->altezza=$altezza;
        $this->data=$data;
        $this->bicipite_destro=$bicipite_destro;
        $this->bicipite_sinistro=$bicipite_sinistro;
        $this->tricipite_destro=$bicipite_destro;
        $this->coscia_destra=$coscia_destra;
        $this->coscia_sinistra=$coscia_sinistra;
        $this->polpaccio_destro=$polpaccio_destro;
        $this->polpaccio_sinistro=$polpaccio_sinistro;
        $this->misura_petto=$misura_petto;
        $this->misura_fianchi=$misura_fianchi;
        $this->misura_spalle=$misura_spalle;
        $this->misura_fianchi=$misura_fianchi;
    }

    //getter

    public function getId(): ?int{
        return $this->id;
    }
    public function getData(): int{
        return $this->data;
    }
    public function getPeso(): float{
        return $this->peso;
    }
    public function getAltezza(): float{
        return $this->altezza;
    }
    public function getBicipite_destro(): ?float{
        return $this->bicipite_destro;
    }
    public function getBicipite_sinistro(): ?float{
        return $this->bicipite_sinistro;
    }
    public function getTricipite_destro(): ?float{
        return $this->tricipite_destro;
    }
    public function getTricipite_sinistro(): ?float{
        return $this->tricipite_sinistro;
    }
    public function getCoscia_destra(): ?float{
        return $this->coscia_destra;
    }
    public function getCoscia_sinistra(): ?float{
        return $this->coscia_sinistra;
    }
    public function getPolpaccio_destro(): ?float{
        return $this->polpaccio_destro;
    }
    public function getPolpaccio_sinistro(): ?float{
        return $this->polpaccio_sinistro;
    }
    public function getMisura_petto(): ?float{
        return $this->misura_petto;
    }
    public function getMisura_vita(): ?float{
        return $this->misura_vita;
    }
    public function getMisura_spalle(): ?float{
        return $this->misura_spalle;
    }
    public function getMisura_fianchi(): ?float{
        return $this->misura_fianchi;
    }
    public function getCliente(): ?Cliente{
        return $this->cliente;
    }

    //setter

    public function setPeso(float $peso):self{
        $this->peso=$peso;
        return $this;
    }
    public function setData(int $data):self{
        $this->data=$data;
        return $this;
    }
    public function setAltezza(float $altezza):self{
        $this->altezza=$altezza;
        return $this;
    }
    public function setBicipite_destro(float $bicipite_destro):self{
        $this->bicipite_destro=$bicipite_destro;
        return $this;
    }
    public function setBicipite_sinistro(float $bicipite_sinistro):self{
        $this->bicipite_sinistro=$bicipite_sinistro;
        return $this;
    }
    public function setTricipite_destro(float $tricipite_destro):self{
        $this->tricipite_destro=$tricipite_destro;
        return $this;
    }
    public function setTricipite_sinistro(float $tricipite_sinistro):self{
        $this->tricipite_sinistro=$tricipite_sinistro;
        return $this;
    }
    public function setCoscia_destra(float $coscia_destra):self{
        $this->coscia_destra=$coscia_destra;
        return $this;
    }
    public function setCoscia_sinistra(float $coscia_sinistra):self{
        $this->coscia_sinistra=$coscia_sinistra;
        return $this;
    }
    public function setPolpaccio_destro(float $polpaccio_destro):self{
        $this->polpaccio_destro=$polpaccio_destro;
        return $this;
    }
    public function setPolpaccio_sinistro(float $polpaccio_sinistro):self{
        $this->polpaccio_sinistro=$polpaccio_sinistro;
        return $this;
    }
    public function setMisura_petto(float $misura_petto):self{
        $this->misura_petto=$misura_petto;
        return $this;
    }   
    public function setMisura_vita(float $misura_vita):self{
        $this->misura_vita=$misura_vita;
        return $this;
    }
    public function setMisura_spalle(float $misura_spalle):self{
        $this->misura_spalle=$misura_spalle;
        return $this;
    }
    public function setMisura_fianchi(float $misura_fianchi):self{
        $this->misura_fianchi=$misura_fianchi;
        return $this;
    }
    public function setCliente(Cliente $c):self{
        $this->cliente=$c;
        return $this;
    }   
}
?>