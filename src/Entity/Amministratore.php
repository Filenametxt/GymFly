<?php

namespace App\Entity;

use GymFly\Enum\Sesso;

/**
 * Classe Amministratore – figlia di Utente (Class Table Inheritance).
 * Il mapping ORM è definito esternamente in:
 *   foundation/App.Entity.Amministratore.dcm.xml
 */
class Amministratore extends Utente
{
    public function __construct(
        string   $nome,
        string   $cognome,
        string   $email,
        string   $CF,
        mixed    $profile_picture,
        int      $telefono,
        string   $indirizzo,
        Sesso    $sesso,
    ) {
        parent::__construct($nome, $cognome, $email, $CF, $profile_picture, $telefono, $indirizzo, $sesso);
    }
    public function mssAllowed(): bool
    {
        return true;
    }

    // -------------------------------------------------------------------------
    // Definizione del ruolo
    // -------------------------------------------------------------------------

    public function getRuolo(): string{
        return "amministratore";
    }
}
?>