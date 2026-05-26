<?php
use GymFly\Enum\Sesso;
class Amministratore extends Utente{
    public function __construct(string $nome, string $cognome, string $email, string $CF, $profile_picture, int $telefono, string $indirizzo, Sesso $sesso){
        parent::__construct($nome, $cognome, $email, $CF, $profile_picture, $telefono, $indirizzo, $sesso);
    }
    public function mssAllowed(): bool{
        return true;
    }
}
?>