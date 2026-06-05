<?php

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
class AttivitaPIanificata {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    private int $giorno;
    private int $orario;
    private int $prenotati=0;
    private Sala $sala;
    private Allenatore $allenatore;
    private Attivita $attivita_di_riferimento;

    private Collection $utenti;

    public function __construct(int $giorno, int $orario, Sala $sala, Allenatore $allenatore, Attivita $attivita_di_riferimento) {
        $this->giorno = $giorno;
        $this->orario = $orario;
        $this->allenatore = $allenatore;
        $this->attivita_di_riferimento = $attivita_di_riferimento;
        $this->utenti = new ArrayCollection();
    }
    public function getId(): ?int{
        return $this->id;
    }
    public function getGiorno(): int {
        return $this->giorno;
    }
    public function getOrario(): int{
        return $this->orario;
    }  
    public function getPrenotati(): int{
        return $this->prenotati;
    }
    public function getSala(): Sala{
        return $this->sala;
    }
    public function getAllenatore(): Allenatore{
        return $this-> allenatore;
    }
    public function getAttivita(): Attivita {
        return $this->attivita_di_riferimento;
    }

    public function getMaxPartecipanti(){
        return min([$this->attivita_di_riferimento->getMax_partecipanti(), $this->sala->getMax_partecipanti()]);
    }
    public function setGiorno(int $giorno): void {    //REVIEW da capire il tipo di dato quando si passa al database
        $this->giorno = $giorno;
    }
    public function setOrario(int $orario): void {    //REVIEW da capire il tipo di dato quando si passa al database
        $this->orario = $orario;
    }
    public function setPrenotati(int $prenotati): void {
        $this->prenotati = $prenotati;
    }
    public function setSala(Sala $sala): void {
        $this->sala = $sala;
    }
    public function setAllenatore(Allenatore $allenatore): void {
        $this->allenatore = $allenatore;
    }
    public function setAttivita(Attivita $attivita_di_riferimento): void{
        $this->attivita_di_riferimento = $attivita_di_riferimento;
    }
}
?>
