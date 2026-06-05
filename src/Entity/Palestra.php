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
    
     // Associazione 1-1 Unidirezionale
    #[ORM\OneToOne(targetEntity: Amministratore::class)]
    #[ORM\JoinColumn(name: 'amministratore_id', referencedColumnName: 'id', nullable: true)]
    private Amministratore $amministratore;

    public function __construct(string $nome,string $indirizzo,string $email,string $recapito_telefonico, Amministratore $amministratore){
        $this->nome = $nome;
        $this->indirizzo = $indirizzo;
        $this->email = $email;
        $this->recapito_telefonico = $recapito_telefonico;
        $this->amministratore = $amministratore;
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

    public function getAmministratore(){
        return $this->amministratore;
    }
    public function setAmministratore(Amministratore $amministratore): self{
        $this->amministratore = $amministratore;
        return $this;
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
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            throw new \InvalidArgumentException('Invalid email address');
        }
        return $this;
    }
    public function set_recapito_telefonico(string $recapito_telefonico): self{

        // Rimuove eventuali spazi o trattini inseriti dall'utente per errore
        $recapito_telefonico = str_replace([' ', '-', '.'], '', $recapito_telefonico);
        //NOTE: Regex: ^\d{9,10}$
        // ^          : Inizio stringa
        // \d{9,10,11}   : Esattamente 9 o 10 o 11 cifre numeriche
        // $          : Fine stringa
        if (preg_match('/^\d{9,10,11}$/', $recapito_telefonico)) {
            $this->recapito_telefonico = $recapito_telefonico;
        } else {
            throw new \InvalidArgumentException("Il numero di telefono deve essere composto da 9 o 10 cifre.");
        }
        return $this;
    }
}
?>
