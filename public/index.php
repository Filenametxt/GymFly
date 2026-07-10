<?php
/**
 * GymFly - Front Controller
 * 
 * Questo è l'unico punto di ingresso dell'applicazione web.
 * Inizializza l'autoloader e passa il controllo al FrontController di sistema.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Control\FrontController;

$frontController = new FrontController();
$frontController->run();
