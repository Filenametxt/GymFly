<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection; 
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'messaggi')]

class Messaggio {
    
    // 1. L'ID è necessario per Doctrine e il Database
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;
    private Utente $mittente;
    private string $oggetto;
    private string $contenuto;
    private Collection $destinatario;
    public function __construct(Utente $mittente, string $oggetto, string $contenuto) {
        $this->mittente = $mittente;
        $this->oggetto = $oggetto;
        $this->contenuto = $contenuto;
    }
    public function getId(): ?int{
        return $this->id;
    }
    public function getMittente(): Utente{
        return $this->mittente;
    }
    public function getDestinatario(): Collection{
        return $this->destinatario;
    }
    public function getOggetto(): string{
        return $this->oggetto;
    }
    public function getContenuto(): string{
        return $this->contenuto;
    }
    public function setMittente(Utente $mittente): self{
        if ($mittente->mssAllowed()) {
            $this->mittente = $mittente;
        }
        return $this;
    }
    public function setDestinatario(Collection $destinatario): self{
        $this->destinatario = $destinatario;
        return $this;
    }
    public function setOggetto(string $oggetto): self{ 
        $this->oggetto = $oggetto;
        return $this;
    }
    public function setContenuto(string $contenuto): self{
        $this->contenuto = $contenuto;
        return $this;
    }
}
?>