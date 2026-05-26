<?php
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
//TODO: Aggiungere relazione amministratore, cliente e allenatore
class Palestra {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[ORM\Column(type: 'string', length: 150)]
    private string $nome;
    #[ORM\Column(type: 'string', length: 150)]
    private string $indirizzo;
     #[ORM\Column(type: 'string', length: 150)]
     private string $email;
     #[ORM\Column(type: 'string', length: 150)]
     private string $recapito_telefonico;

    public function __construct(string $nome,string $indirizzo,string $email,string $recapito_telefonico){
        $this->nome = $nome;
        $this->indirizzo = $indirizzo;
        $this->email = $email;
        $this->recapito_telefonico = $recapito_telefonico;
    }
    public function get_id(): ?int{
        return $this->id;
    }
    public function get_nome() : string{
        return $this->nome;
    }
    public function get_indirizzo(): string{
        return $this->indirizzo;
    }
    public function get_email(): string {
        return $this->email;
    }
    public function get_recapito_telefonico():string{
        return $this->recapito_telefonico;
    }
    public function setNome(string $nome): self{
        $this->nome = $nome;
        return $this; // Ritorna self per il method chaining
    }
    public function set_indirizzo(string $indirizzo): self{
        $this->indirizzo = $indirizzo;
        return $this;
    }
    public function set_email(string $email): self{
        $this->email = $email;
        return $this;
    }
    public function set_recapito_telefonico(string $recapito_telefonico): self{
        $this->recapito_telefonico = $recapito_telefonico;
        return $this;
    }
}
?>
