<?php
use GymFly\Enum\Sesso;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
class Allenatore extends Utente{
    private Palestra $palestra;
    private Collection $attivita_abilitate;   //REVIEW da capire se inserire anche le attività pinaificate

    
    public function __construct(string $nome, string $cognome, string $email, string $CF, $profile_picture, int $telefono, string $indirizzo, Sesso $sesso, Palestra $palestra){
        parent::__construct($nome, $cognome, $email, $CF, $profile_picture, $telefono, $indirizzo, $sesso);
        $this->palestra = $palestra;
    }
    public function getAbilitazioni(): Collection{
        return $this->attivita_abilitate;
    }
    public function setAbilitazioni(Collection $attivita_abilitate): self{
        $this->attivita_abilitate = $attivita_abilitate;
        return $this;
    }
    public function mssAllowed(): bool{
        return true;
    }

    public function getPalestra(): Palestra{
        return $this->palestra;
    }
}
?>