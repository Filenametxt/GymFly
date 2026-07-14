<?php

use Smarty\Smarty; #dfinisce l'oggetto Smarty che si trova nel package Smarty

require_once __DIR__ . '/../../../../vendor/autoload.php'; #permette di includere l'autoload di Composer, che carica automaticamente le classi necessarie

$smarty = new Smarty(); #

// Definiamo le cartelle di Smarty
$smarty->setTemplateDir(__DIR__ . '/../../../View/Templates'); #cartella dei template
$smarty->setCompileDir(__DIR__ . '/../../../View/Templates_c'); #cartella dei template compilati
$smarty->setCacheDir(__DIR__ . '/../../../View/Cache'); #cartella della cache

// Impostazioni utili
$smarty->caching = false; // Metti true solo in produzione
$smarty->debugging = false;

return $smarty;