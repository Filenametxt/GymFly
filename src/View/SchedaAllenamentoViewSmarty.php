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
        $this->smarty->registerPlugin('modifier', 'pulisci_descrizione', [self::class, 'pulisciDescrizione']);
        $this->smarty->registerPlugin('modifier', 'estrai_recupero', [self::class, 'estraiRecupero']);
        $this->smarty->clearCompiledTemplate(); // Forza ricompilazione immediata dei template modificati
    }

    public static function pulisciDescrizione(?string $descrizione): string
    {
        if ($descrizione === null) {
            return '';
        }
        return trim(preg_replace('/\[[^\]]+\]/', '', $descrizione));
    }

    public static function estraiRecupero(?string $descrizione, string $nomeEsercizio, int $serie = 1, ?int $dettaglioId = null): string
    {
        if ($descrizione === null) {
            return 'Non specificato';
        }
        
        // 1. Se abbiamo il dettaglio ID, proviamo prima il pattern univoco per ID
        if ($dettaglioId !== null && $dettaglioId > 0) {
            $patternId = '/\[DetId - ' . $dettaglioId . ' - Recupero: ([^\]\n]+)\]/';
            if (preg_match($patternId, $descrizione, $matches)) {
                return trim($matches[1]);
            }
        }
        
        // 2. Proviamo il pattern specifico per serie: "[Nome Esercizio - Serie S - Recupero: X]"
        $patternSeries = '/' . preg_quote($nomeEsercizio, '/') . ' - Serie ' . $serie . ' - Recupero: ([^\]\n]+)/';
        if (preg_match($patternSeries, $descrizione, $matches)) {
            return trim($matches[1]);
        }
        // Fallback al pattern globale senza serie: "[Nome Esercizio - Recupero: X]"
        $patternGlobal = '/' . preg_quote($nomeEsercizio, '/') . ' - Recupero: ([^\]\n]+)/';
        if (preg_match($patternGlobal, $descrizione, $matches)) {
            return trim($matches[1]);
        }
        return 'Non specificato';
    }

    public function mostraTemplate(string $tplName, array $dati = []): void
    {
        header('Content-Type: text/html; charset=utf-8');
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $this->smarty->display($tplName);
    }

    public function fetchTemplate(string $tplName, array $dati = []): string
    {
        foreach ($dati as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        return $this->smarty->fetch($tplName);
    }

    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', $successo);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->assign('ritorno', $ritorno);
        $this->smarty->display('stato_operazione.tpl');
    }
}
