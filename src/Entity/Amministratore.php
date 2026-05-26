<?php
class Amministratore extends Utente{
    public function __construct(string $nome, string $cognome, string $email, string $CF, $profile_picture, int $telefono, string $indirizzo, string $sesso){
        parent::__construct($nome, $cognome, $email, $CF, $profile_picture, $telefono, $indirizzo, $sesso);
    }
}
?>