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
    private Collection $destinatario;
    public function __construct(Utente $mittente, string $oggetto) {
        $this->mittente = $mittente;
        $this->oggetto = $oggetto;
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
    public function setMittente(Utente $mittente): self{
        $this->mittente = $mittente;
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
}
?>