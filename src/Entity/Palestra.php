<?php

namespace App\Entity;

class Palestra {

    private ?int $id = null;
    private string $nome;
    private string $indirizzo;
    private string $email;
    private string $recapitoTelefonico;
    private Amministratore $amministratore;

    public function __construct(
        string $nome,
        string $indirizzo,
        string $email,
        string $recapitoTelefonico,
        Amministratore $amministratore
    ) {
        $this->nome                = $nome;
        $this->indirizzo           = $indirizzo;
        $this->email               = $email;
        $this->recapitoTelefonico = $recapitoTelefonico;
        $this->amministratore      = $amministratore;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getNome(): string {
        return $this->nome;
    }

    public function getIndirizzo(): string {
        return $this->indirizzo;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getRecapitoTelefonico(): string {
        return $this->recapitoTelefonico;
    }

    public function getAmministratore(): Amministratore {
        return $this->amministratore;
    }

    public function setNome(string $nome): self {
        $this->nome = $nome;
        return $this;
    }

    public function setIndirizzo(string $indirizzo): self {
        $this->indirizzo = $indirizzo;
        return $this;
    }

    public function setEmail(string $email): self {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Indirizzo email non valido.');
        }
        $this->email = $email;
        return $this;
    }

    public function setRecapitoTelefonico(string $recapitoTelefonico): self {
        $recapitoTelefonico = str_replace([' ', '-', '.'], '', $recapitoTelefonico);
        if (!preg_match('/^\d{9,11}$/', $recapitoTelefonico)) {
            throw new \InvalidArgumentException("Il numero di telefono deve essere composto da 9 a 11 cifre.");
        }
        $this->recapitoTelefonico = $recapitoTelefonico;
        return $this;
    }

    public function setAmministratore(Amministratore $amministratore): self {
        $this->amministratore = $amministratore;
        return $this;
    }
}
?>