<?php
namespace App\View;

use App\View\Interface\AttivitaPianificataView;
use Smarty\Smarty;

class AttivitaPianificataViewSmarty implements AttivitaPianificataView
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = require __DIR__ . '/../Foundation/Persistence/Config/smarty.php';
    }

    private function determinaRitorno(): string
    {
        $offset = isset($_REQUEST['offset']) ? (int)$_REQUEST['offset'] : 0;
        return $offset !== 0 ? 'calendario?offset=' . $offset : 'calendario';
    }

    public function mostraCalendario(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $offset = isset($_REQUEST['offset']) ? (int)$_REQUEST['offset'] : 0;
        $this->smarty->assign('offset', $offset);
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        
        if ($dati['ruolo_utente'] === 'amministratore') {
            $this->smarty->display('planner_settimanale.tpl');
        } else {
            $this->smarty->display('calendario.tpl');
        }
    }

    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', $successo);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $ritorno ?? $this->determinaRitorno());
        $this->smarty->assign('testo_bottone', $testoBottone);
        $this->smarty->display('stato_operazione.tpl');
    }
}
