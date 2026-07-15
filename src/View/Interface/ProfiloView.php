<?php
namespace App\View\Interface;

interface ProfiloView {
    public function mostraProfilo(array $datiCliente): void;
    public function mostraConfermaModifica(string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void;
    public function mostraErrore(string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void;
    public function mostraFormModifica(array $dati): void;
    public function mostraFormMisure(array $dati): void;
    public function mostraFormInserimentoMisure(array $dati): void;
    public function mostraFormCertificato(array $dati): void;
    public function mostraFormCambioPassword(): void;
    public function mostraGrafico(array $dati): void;
}