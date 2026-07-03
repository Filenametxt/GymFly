<?php
namespace App\View;

use App\View\Interface\ProfiloView;
use Smarty\Smarty;

class ProfiloViewSmarty implements ProfiloView
{
    private Smarty $smarty;

    public function __construct()
    {
        // Carica la configurazione di Smarty dell'applicazione
        $this->smarty = require __DIR__ . '/../Foundation/Persistence/Config/smarty.php';
    }

    /**
     * Mostra la pagina di dettaglio del profilo utente (Page 17 del mock-up).
     */
    public function mostraProfilo(array $datiCliente): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($datiCliente as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('profilo.tpl');
    }

    /**
     * Mostra un messaggio di conferma per una modifica andata a buon fine.
     */
    public function mostraConfermaModifica(string $messaggio): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', true);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->display('stato_operazione.tpl');
    }

    /**
     * Mostra una schermata di errore in caso di fallimento o accesso vietato.
     */
    public function mostraErrore(string $messaggio): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', false);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->display('stato_operazione.tpl');
    }
}
