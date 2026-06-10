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
    string $nome,
    string $cognome,
    string $email,
    string $CF,
    string $indirizzo,
    Sesso $sesso,
    string $password = '',
    ?string $profilePicture = null,
    ?string $telefono = null
    ) {
        parent::__construct($nome, $cognome, $email, $CF, $indirizzo, $sesso, $password, $profilePicture, $telefono);
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