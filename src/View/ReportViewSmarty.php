<?php
namespace App\View;

use App\View\Interface\ReportView;
use Smarty\Smarty;

class ReportViewSmarty implements ReportView
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = require __DIR__ . '/../Foundation/Persistence/Config/smarty.php';
    }

    public function mostraReport(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('report.tpl');
    }

    public function mostraErrore(string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', false);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $ritorno ?? 'dashboard-admin');
        $this->smarty->assign('testo_bottone', $testoBottone);
        $this->smarty->display('stato_operazione.tpl');
    }
}
