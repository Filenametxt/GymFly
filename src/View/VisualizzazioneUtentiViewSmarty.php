<?php
namespace App\View;

use App\View\Interface\VisualizzazioneUtentiView;
use Smarty\Smarty;

class VisualizzazioneUtentiViewSmarty implements VisualizzazioneUtentiView
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

    public function mostraListaClienti(array $clientiData): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('clienti', $clientiData);
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        $this->smarty->display('lista_clienti.tpl');
    }

    public function mostraListaAllenatori(array $allenatoriData): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('allenatori', $allenatoriData);
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        $this->smarty->display('lista_allenatori.tpl');
    }

    public function mostraErrore(string $messaggio): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', false);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        $this->smarty->display('stato_operazione.tpl');
    }
}
