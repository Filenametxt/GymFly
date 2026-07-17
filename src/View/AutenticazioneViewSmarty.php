<?php
namespace App\View;

use App\View\Interface\AutenticazioneView;
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
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? ''
        ];
    }

    public function richiediDatiRegistrazione(): array
    {
        return [
            'nome' => $_POST['nome'] ?? null,
            'cognome' => $_POST['cognome'] ?? null,
            'email' => $_POST['email'] ?? null,
            'cf' => $_POST['cf'] ?? null,
            'password' => $_POST['password'] ?? null,
            'indirizzo' => $_POST['indirizzo'] ?? null,
            'data_nascita' => $_POST['data_nascita'] ?? null,
            'luogo_nascita' => $_POST['luogo_nascita'] ?? null,
            'sesso' => $_POST['sesso'] ?? null,
            'telefono' => $_POST['telefono'] ?? null,
            // Campi aggiuntivi per la palestra
            'nome_palestra' => $_POST['nome_palestra'] ?? null,
            'indirizzo_palestra' => $_POST['indirizzo_palestra'] ?? null,
            'email_palestra' => $_POST['email_palestra'] ?? null,
            'telefono_palestra' => $_POST['telefono_palestra'] ?? null,
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