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
            'metodo_pagamento' => $_POST['metodo_pagamento'] ?? null,
            'data_nascita' => $_POST['data_nascita'] ?? null,
            'luogo_nascita' => $_POST['luogo_nascita'] ?? null,
            'sesso' => $_POST['sesso'] ?? null,
            'indirizzo_domicilio' => $_POST['indirizzo_domicilio'] ?? null,
            'telefono' => $_POST['telefono'] ?? null,
        ];
    }

    public function richiediIdUtenteDaRimuovere(): int
    {
        return isset($_POST['id_utente']) ? (int)$_POST['id_utente'] : 0;
    }

    public function mostraStatoOperazione(bool $successo, string $messaggio): void
    {
        header('Content-Type: text/html; charset=utf-8');
        $this->smarty->assign('successo', $successo);
        $this->smarty->assign('messaggio', $messaggio);
        $this->smarty->display('stato_operazione.tpl');
    }
}