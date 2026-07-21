<?php
namespace App\View;

use App\View\Interface\AutenticazioneView;
use App\Foundation\Utility\HTTPMethods;
use Smarty\Smarty;

class AutenticazioneViewSmarty implements AutenticazioneView
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = require __DIR__ . '/../Foundation/Persistence/Config/smarty.php';
    }

    public function mostraFormLogin(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->display('login.tpl');
    }

    public function mostraFormRegistrazione(): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->display('registrazione.tpl');
    }

    public function richiediCredenzialiLogin(): array
    {
        return [
            'email' => HTTPMethods::post('email', ''),
            'password' => HTTPMethods::post('password', '')
        ];
    }

    public function richiediDatiRegistrazione(): array
    {
        return [
            'nome' => HTTPMethods::post('nome'),
            'cognome' => HTTPMethods::post('cognome'),
            'email' => HTTPMethods::post('email'),
            'cf' => HTTPMethods::post('cf'),
            'password' => HTTPMethods::post('password'),
            'indirizzo' => HTTPMethods::post('indirizzo'),
            'data_nascita' => HTTPMethods::post('data_nascita'),
            'luogo_nascita' => HTTPMethods::post('luogo_nascita'),
            'sesso' => HTTPMethods::post('sesso'),
            'telefono' => HTTPMethods::post('telefono'),
            // Campi aggiuntivi per la palestra
            'nome_palestra' => HTTPMethods::post('nome_palestra'),
            'indirizzo_palestra' => HTTPMethods::post('indirizzo_palestra'),
            'email_palestra' => HTTPMethods::post('email_palestra'),
            'telefono_palestra' => HTTPMethods::post('telefono_palestra'),
        ];
    }

    public function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', $successo);
        $this->smarty->assign('messaggio', $messaggio);
        if ($ritorno !== null) {
            $this->smarty->assign('ritorno', $ritorno);
        }
        $this->smarty->assign('testo_bottone', $testoBottone);
        $this->smarty->display('stato_operazione.tpl');
    }

    public function reindirizzaDopoLogin(string $ruolo): void
    {
        $url = './'; // Default

        switch ($ruolo) {
            case 'amministratore':
                $url = 'dashboard-admin';
                break;
            case 'allenatore':
                $url = 'dashboard-allenatore';
                break;
            case 'cliente':
                $url = 'dashboard-cliente';
                break;
            default:
                $url = 'errore?msg=' . urlencode("Ruolo utente non riconosciuto.");
                break;
        }
        header('Location: ' . $url);
        exit();
    }
}