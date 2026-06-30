<?php
namespace App\Foundation;

class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function setUtenteLoggato(\App\Entity\Cliente $cliente): void {
        $_SESSION['id_utente'] = $cliente->getId();
    }

    public function getLoggedUserId(): ?int {
        return $_SESSION['id_utente'] ?? null;
    }

    public function isLogged(): bool {
        return isset($_SESSION['id_utente']);
    }

    public function logout(): void {
        session_destroy();
    }
}