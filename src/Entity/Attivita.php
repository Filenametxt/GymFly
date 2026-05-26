<?php
class Attivita{
    private ?int $id = null;
    private string $nome;
    private string $descrizione;
    private int $max_partecipanti;
    private AttivitaPianificata $attivitapianificata;
    private Allenatore $allenatore; //TODO creare classe Allenatore

    public function __construct(string $nome, string $descrizione, int $max_partecipanti, AttivitaPianificata $attivitapianificata, Allenatore $allenatore){
        $this->nome = $nome;
        $this->descrizione = $descrizione;
        $this->max_partecipanti = $max_partecipanti;
        $this->attivitapianificata = $attivitapianificata;
        $this->allenatore = $allenatore;
    }
    public function getId(): ?int{
        return $this->id;
    }
    public function getNome(): string{
        return $this->nome;
    }
    public function getDescrizione(): string{
        return $this->descrizione;
    }
    public function getMax_partecipanti(): int{
        return $this->max_partecipanti;
    }
    public function getAttivitapianificata(): AttivitaPianificata{
        return $this->attivitapianificata;
    }
    public function getAllenatore(): Allenatore{
        return $this->allenatore;
    }
    public function setNome(string $nome): void{
        $this->nome = $nome;
    }
    public function setDescrizione(string $descrizione): void{
        $this->descrizione = $descrizione;
    }
    public function setMax_partecipanti(int $max_partecipanti): void{
        $this->max_partecipanti = $max_partecipanti;
    }
    public function setAttivitaPianificata(AttivitaPianificata $attivitapianificata): void{
        $this->attivitapianificata = $attivitapianificata;
    }
    public function setAllenatore(Allenatore $allenatore): void{
        $this->allenatore = $allenatore;
    }
}
?>