<?php
namespace App\Foundation; //siamo nel package Foundation

use App\Entity\Utente; //andiamo a usare la classe Utente che si trova nel package Entity

class Session {
    public function __construct() { //doppio underscore: funzione magica 
        if (session_status() === PHP_SESSION_NONE) { //se non è presente alcuna sessione, falla partire
            session_start();
        }
    }

    public function setUtenteLoggato(Utente $utente): void { //recupera un utente loggato dal database e lo salva (set) nella sessione
        $_SESSION['id_utente'] = $utente->getId(); //salva l'ID dell'utente loggato
        $_SESSION['ruolo_utente'] = $utente->getRuolo(); //salva il ruolo dell'utente loggato
    }

    public function getLoggedUserId(): ?int { //recupera l'ID dell'utente loggato dalla sessione, se non c'è ritorna null
        return $_SESSION['id_utente'] ?? null;
    }

    public function getLoggedUserRole(): ?string { //recupera il ruolo dell'utente loggato dalla sessione, se non c'è ritorna null
        return $_SESSION['ruolo_utente'] ?? null;
    }

    public function isLogged(): bool { //verifica se l'utente è loggato (se sta visitando il sito in quel momento) controllando se l'ID dell'utente è presente nella sessione
        return isset($_SESSION['id_utente']); //ritorna true se l'ID dell'utente è presente nella sessione, false altrimenti
    }

    public function set(string $key, mixed $value): void { //salva un valore nella sessione con una chiave specifica
        $_SESSION[$key] = $value; //mixed: può essere di qualsiasi tipo (stringa, intero, array, oggetto, ecc.)
    }

    public function get(string $key): mixed { //recupera un valore dalla sessione usando una chiave specifica
        return $_SESSION[$key] ?? null; //se non c'è la chiave nella sessione, ritorna null
    }

    public function logout(): void { //funzione per fare il logout dell'utente, rimuovendo i dati della sessione, quindi la svuota e la distrugge
        session_unset(); //toglie le variabili registrate all'interno di sessione
        session_destroy(); //distrugge la sessione
    }
}