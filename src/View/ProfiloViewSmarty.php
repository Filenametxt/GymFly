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

    /**
     * Mostra un messaggio di conferma per una modifica andata a buon fine.
     */
    public function mostraConfermaModifica(string $messaggio): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', true);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $this->determinaRitorno());
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
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        $this->smarty->display('stato_operazione.tpl');
    }

    public function mostraFormModifica(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('modifica_anagrafica.tpl');
    }

    public function mostraFormMisure(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('aggiorna_misure.tpl');
    }

    public function mostraFormInserimentoMisure(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('inserisci_misure.tpl');
    }

    public function mostraFormCertificato(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('carica_certificato.tpl');
    }

    public function mostraFormCambioPassword(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('ritorno', $this->determinaRitorno());
        $this->smarty->display('cambia_password.tpl');
    }

    public function mostraGrafico(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('visualizza_grafico.tpl');
    }
}
