<?php
namespace App\View;

use App\View\Interface\AmministratoreView;
use Smarty\Smarty;

class AmministratoreViewSmarty implements AmministratoreView
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

    public function mostraFormCreaCliente(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        $this->smarty->display('crea_cliente.tpl');
    }

    public function mostraFormCreaAllenatore(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        $this->smarty->display('crea_allenatore.tpl');
    }

    public function mostraFormCreaAttivita(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        $this->smarty->display('crea_attivita.tpl');
    }




}
