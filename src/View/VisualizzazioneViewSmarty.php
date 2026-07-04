<?php
namespace App\View;

use App\View\Interface\VisualizzazioneView;
use Smarty\Smarty;

class VisualizzazioneViewSmarty implements VisualizzazioneView
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = require __DIR__ . '/../Foundation/Persistence/Config/smarty.php';
    }

    public function mostraHome(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->display('home.tpl');
    }

    public function mostraDashboardAdmin(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('dashboard_admin.tpl');
    }

    public function mostraDashboardAllenatore(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('dashboard_allenatore.tpl');
    }

    public function mostraDashboardCliente(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('dashboard_cliente.tpl');
    }

    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', $successo);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $ritorno);
        $this->smarty->display('stato_operazione.tpl');
    }

    public function reindirizzaDopoLogin(string $ruolo): void
    {
        switch ($ruolo) {
            case 'amministratore':
                header('Location: dashboard-admin');
                break;
            case 'allenatore':
                header('Location: dashboard-allenatore');
                break;
            case 'cliente':
                header('Location: dashboard-cliente');
                break;
            default:
                header('Location: login');
                break;
        }
        exit;
    }
}
