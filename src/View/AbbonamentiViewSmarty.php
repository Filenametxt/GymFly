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
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('gestione_abbonamento.tpl');
    }
}
