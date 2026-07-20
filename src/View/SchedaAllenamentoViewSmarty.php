<?php
namespace App\View;

use App\View\Interface\SchedaAllenamentoView;
use Smarty\Smarty;

class SchedaAllenamentoViewSmarty implements SchedaAllenamentoView
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = require __DIR__ . '/../Foundation/Persistence/Config/smarty.php';
        $this->smarty->registerPlugin('modifier', 'base64_encode', 'base64_encode');
        $this->smarty->clearCompiledTemplate(); // Forza ricompilazione immediata dei template modificati
    }

    public function mostraTemplate(string $tplName, array $dati = []): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display($tplName);
    }

    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', $successo);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $ritorno);
        $this->smarty->assign('testo_bottone', $testoBottone);
        $this->smarty->display('stato_operazione.tpl');
    }

    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }
}
