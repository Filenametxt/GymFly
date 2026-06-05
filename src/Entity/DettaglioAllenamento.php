<?php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'dettagli_allenamento')]
class DettaglioAllenamento {

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private Esercizio $esercizio;  

    #[ORM\Column]
    private int $serie;

    #[ORM\Column]           
    private int $ripetizioni;

    #[ORM\Column] 
    private float $carico;

    // Questa è la "chiave" della bidirezionalità
    // 'inversedBy' punta alla proprietà 'dettagli' dentro la classe Allenamento
    #[ORM\ManyToOne(targetEntity: Allenamento::class, inversedBy: 'dettagli')]
    #[ORM\JoinColumn(nullable: false)] // La FK non può essere vuota (un dettaglio deve avere un allenamento)
    private Allenamento $allenamento;

    public function __construct(Esercizio $esercizio, int $serie, int $ripetizioni, float $carico) {
        $this->esercizio = $esercizio;
        $this->serie = $serie;
        $this->ripetizioni = $ripetizioni;
        $this->carico = $carico;
    }

    // Getter e Setter
    public function getId(): ?int {
        return $this->id;
    }

    public function getAllenamento(): Allenamento {
        return $this->allenamento;
    }

    // Questo metodo viene chiamato dal metodo 'addDettaglio' nell'Allenamento
    public function setAllenamento(Allenamento $allenamento): self {
        $this->allenamento = $allenamento;
        return $this;
    }
    public function getEsercizio(): Esercizio {   
        return $this->esercizio;
    }
    public function getSerie(): int {
        return $this->serie;
    }
    public function getRipetizioni(): int {
        return $this->ripetizioni;
    }

    public function getCarico(): int {
        return $this->carico;
    }




    public function setSerie(int $serie): self {
        $this->serie = $serie;
        return $this;
    }
    public function setRipetizioni(int $ripetizioni): self {
        $this->ripetizioni = $ripetizioni;  
        return $this;
    }
    public function setEsercizio(Esercizio $esercizio): self { 
        $this->esercizio = $esercizio;
        return $this;
    }
    public function setCarico(int $carico): self {
        $this->carico=$carico;
        return $this;
    }
    
}
?>