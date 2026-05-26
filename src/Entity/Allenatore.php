<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
class Allenatore extends Utente{
    private Collection $attivitapianificate;
    public function __construct(string $nome, string $cognome, string $email, string $CF, $profile_picture, int $telefono, string $indirizzo, string $sesso){
        parent::__construct($nome, $cognome, $email, $CF, $profile_picture, $telefono, $indirizzo, $sesso);
    }
}