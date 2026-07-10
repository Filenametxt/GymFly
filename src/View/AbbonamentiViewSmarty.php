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

    public function mostraErrore(string $messaggio): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', false);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', 'clienti');
        $this->smarty->display('stato_operazione.tpl');
    }

    public function mostraConferma(string $messaggio, string $ritorno): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', true);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $ritorno);
        $this->smarty->display('stato_operazione.tpl');
    }
}
