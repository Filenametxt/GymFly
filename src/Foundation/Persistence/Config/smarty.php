<?php

use Smarty\Smarty;

require_once __DIR__ . '/../../../../vendor/autoload.php';

$smarty = new Smarty();

// Definiamo le cartelle di Smarty
$smarty->setTemplateDir(__DIR__ . '/../../../View/Templates');
$smarty->setCompileDir(__DIR__ . '/../../../View/Templates_c');
$smarty->setCacheDir(__DIR__ . '/../../../View/Cache');

// Impostazioni utili
$smarty->caching = false; // Metti true solo in produzione
$smarty->debugging = false;

return $smarty;