<?php
namespace App\Control\DTO;

class ModificaAnagraficaDTO
{
    // --- 1. ATTRIBUTI EREDITATI DA UTENTE ---
    public string $nome;
    public string $cognome;
    public string $email;
    public string $CF;
    public string $indirizzo;
    public string $sesso;          // Stringa grezza (es: "M", "F"). La mapperai all'Enum Sesso nel Controller
    public ?string $telefono = null;
    // Nota: password e profilePicture di solito hanno form/logiche a parte per sicurezza, 

    // --- 2. ATTRIBUTI SPECIFICI DI CLIENTE (Il Figlio) ---
    public string $dataDiNascita; // Stringa grezza "YYYY-MM-DD" La responsabilità di conversione è destinata al controller
    public string $luogoDiNascita;
    public ?string $indirizzoDiDomicilio = null;
    public string $metodoDiPagamento;
}