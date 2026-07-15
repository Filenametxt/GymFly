<?php
namespace App\View;

use App\View\Interface\EserciziView;
use Smarty\Smarty;

class EserciziViewSmarty implements EserciziView
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = require __DIR__ . '/../Foundation/Persistence/Config/smarty.php';
    }

    public function mostraFormEsercizio(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('crea_esercizio.tpl');
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

    public function mostraListaEsercizi(array $eserciziData): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('esercizi', $eserciziData);
        $this->smarty->assign('search_query', $_POST['search_query'] ?? $_GET['search_query'] ?? null);
        $this->smarty->display('lista_esercizi.tpl');
    }

    public function mostraDettaglioEsercizio(array $dati): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display('dettaglio_esercizio.tpl');
    }
}
