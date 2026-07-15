<?php
namespace App\View;

use App\View\Interface\MessaggiView;
use Smarty\Smarty;

class MessaggiViewSmarty implements MessaggiView
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = require __DIR__ . '/../Foundation/Persistence/Config/smarty.php';
    }

    private function determinaRitorno(): string
    {
        if (isset($_SESSION['ruolo_utente'])) {
            switch ($_SESSION['ruolo_utente']) {
                case 'amministratore':
                    return 'dashboard-admin';
                case 'allenatore':
                    return 'dashboard-allenatore';
                case 'cliente':
                    return 'dashboard-cliente';
            }
        }
        return 'login';
    }

    public function mostraBachecaMessaggi(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        $this->smarty->display('messaggi.tpl');
    }

    public function mostraErrore(string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', false);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $ritorno ?? 'messaggi');
        $this->smarty->assign('testo_bottone', $testoBottone);
        $this->smarty->display('stato_operazione.tpl');
    }

    public function mostraConfermaInviato(string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', true);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $ritorno ?? 'messaggi');
        $this->smarty->assign('testo_bottone', $testoBottone);
        $this->smarty->display('stato_operazione.tpl');
    }
}
