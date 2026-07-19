<?php
// /opt/lampp/htdocs/GymFly/test_login.php

// Abilita la visualizzazione completa degli errori per il debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 1. BOOTSTRAP: Carica l'ambiente dell'applicazione
require_once __DIR__ . '/vendor/autoload.php';

use App\Foundation\Session;
use App\Control\AutenticazioneController;
use App\View\AutenticazioneViewSmarty;
use App\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Config\EntityManagerFactory;

try {
    // 1. SETUP: Prepara la view che vogliamo testare
    $view = new AutenticazioneViewSmarty();
    
    echo "Avvio test di visualizzazione per la pagina di login...<br>";
    echo "Chiamata a mostraFormLogin()...<br><hr>";
    
    // 2. ESECUZIONE: Chiama il metodo della view per mostrare la pagina.
    // Questo script ora testa solo la capacità della view di renderizzare il template,
    // senza coinvolgere la logica del controller o l'invio di dati.
    $view->mostraFormLogin();

} catch (\Throwable $e) {
    echo "<h1>Si è verificato un errore!</h1>";
    echo "<p><strong>Messaggio:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " (Riga: " . $e->getLine() . ")</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}