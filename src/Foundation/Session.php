<?php
namespace App\Foundation;

use App\Entity\Utente;

class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function setUtenteLoggato(Utente $utente): void {
        $_SESSION['id_utente'] = $utente->getId();
        $_SESSION['ruolo_utente'] = $utente->getRuolo();
    }

    public function getLoggedUserId(): ?int {
        return $_SESSION['id_utente'] ?? null;
    }

    public function getLoggedUserRole(): ?string {
        return $_SESSION['ruolo_utente'] ?? null;
    }

    public function isLogged(): bool {
        return isset($_SESSION['id_utente']);
    }

    public function logout(): void {
        session_unset();
        session_destroy();
    }
}