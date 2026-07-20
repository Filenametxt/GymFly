<?php
namespace App\View;

use App\View\Interface\AbbonamentiView;
use Smarty\Smarty;

class AbbonamentiViewSmarty implements AbbonamentiView
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = require __DIR__ . '/../Foundation/Persistence/Config/smarty.php';
    }

    public function mostraGestioneAbbonamento(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');            //imposta l'header per la corretta visualizzazione dei caratteri speciali
        foreach ($dati as $key => $value) {                          // Assegna i dati alla variabile Smarty
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('gestione_abbonamento.tpl');         //visualizza il template Smarty per la gestione dell'abbonamento
    }
}
