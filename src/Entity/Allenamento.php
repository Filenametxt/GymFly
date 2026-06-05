<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'allenamenti')]
class Allenamento {
    
    // 1. L'ID è necessario per Doctrine e il Database
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 2. Proprietà del dominio
    private string $nome;
    private ?string $descrizione=null;

    // 3. Relazione 1:N (Un allenamento ha molti dettagli)
    #[ORM\OneToMany(targetEntity: DettaglioAllenamento::class, mappedBy: 'allenamento', cascade: ['persist', 'remove'])]
    private Collection $dettagli;


    //4.Relazione N:1 (Un allenamento è associato a una Scheda)
    #[ORM\ManyToOne(targetEntity: Scheda::class, inversedBy: 'allenamenti')] //TODO: Classe Scheda da aggiornare con doctrine
    #[ORM\JoinColumn(nullable: false)] // La FK non può essere vuota (un dettaglio deve avere un allenamento)
    private ?Scheda $scheda;
    public function __construct(string $nome,Scheda $scheda) {
        $this->nome = $nome;
        $this->scheda=$scheda;
        // Inizializziamo la collezione come ArrayCollection
        $this->dettagli = new ArrayCollection();
    }

    // Metodi di accesso (Getter/Setter)
    public function getId(): ?int {
        return $this->id;
    }

    public function getNome(): ?string {
        return $this->nome;
    }
    public function setNome(string $nome): self {
        $this->nome = $nome;
        return $this;
    }
    public function getDescrizione(): ?string {
        return $this->descrizione;
    }
    public function setDescrizione(string $descrizione): self {
        $this->descrizione = $descrizione;
        return $this;
    }

    /**
     * @return Collection<int, DettaglioAllenamento>
     */
    public function getDettagli(): Collection {
        return $this->dettagli;
    }

    // Metodo per gestire la relazione in modo coerente
    public function addDettaglio(DettaglioAllenamento $dettaglio): self {
        if (!$this->dettagli->contains($dettaglio)) {
            $this->dettagli->add($dettaglio);
            $dettaglio->setAllenamento($this); // Impostiamo il lato inverso
        }
        return $this;
    }
    public function getScheda(): Scheda {
        return $this->scheda;
    }
    public function setScheda(Scheda $scheda): self {
        $this->scheda = $scheda;
        return $this;
    }
}
?>
