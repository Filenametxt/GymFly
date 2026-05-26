<?php
class AttivitaPIanificata {
    private ?int $id = null;
    private int $giorno;
    private int $orario;
    private int $prenotati;
    private Sala $sala;
    private Allenatore $allenatore;
    private Attivita $attivita_di_riferimento;

    public function __construct(int $giorno, int $orario, int $prenotati, Sala $sala, Allenatore $allenatore, Attivita $attivita_di_riferimento) {
        $this->giorno = $giorno;
        $this->orario = $orario;
        $this->prenotati = $prenotati;
        $this->sala = $sala;
        $this->allenatore = $allenatore;
        $this->attivita_di_riferimento = $attivita_di_riferimento;
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