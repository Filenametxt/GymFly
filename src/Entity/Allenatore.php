<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
class Allenatore extends Utente{
    private Collection $attivita_abilitate;   //REVIEW da capire se inserire anche le attività pinaificate
    public function __construct(string $nome, string $cognome, string $email, string $CF, $profile_picture, int $telefono, string $indirizzo, string $sesso){
        parent::__construct($nome, $cognome, $email, $CF, $profile_picture, $telefono, $indirizzo, $sesso);
    }
    public function getAbilitazioni(): Collection{
        return $this->attivita_abilitate;
    }
    public function setAbilitazioni(Collection $attivita_abilitate): self{
        $this->attivita_abilitate = $attivita_abilitate;
        return $this;
    }
}
?>