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
        $this->setNome($nome);
        $this->setIndirizzo($indirizzo);
        $this->setAmministratore($amministratore);
        
        // Sfruttiamo i setter per validare immediatamente le regole di dominio!
        $this->setEmail($email);
        $this->setRecapitoTelefonico($recapitoTelefonico);
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
        $nomePulito = trim($nome);
        if ($nomePulito === '') {
            throw new \InvalidArgumentException("Il nome non può essere vuoto.");
        }
        $this->nome = $nomePulito;
        return $this;
    }

    public function setIndirizzo(string $indirizzo): self
    {
        // Rimuoviamo spazi bianchi accidentali all'inizio e alla fine
        $indirizzoPulito = trim($indirizzo);

        // Essendo un campo obbligatorio, controlliamo che non sia vuoto dopo il trim
        if ($indirizzoPulito === '') {
            throw new \InvalidArgumentException("L'indirizzo è obbligatorio e non può essere vuoto.");
        }

        $this->indirizzo = $indirizzoPulito;
        return $this;
    }

    public function setEmail(string $email): self {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Indirizzo email non valido.');
        }
        $this->email = $email;
        return $this;
    }

    public function setRecapitoTelefonico(string $recapitoTelefonico): self 
    {
        // Sanitizzazione completa: rimuove spazi bianchi ed elementi di formattazione comuni
        $telefonoPulito = trim($recapitoTelefonico);
        $telefonoPulito = str_replace([' ', '-', '.'], '', $telefonoPulito);
        
        if (!preg_match('/^\d{9,11}$/', $telefonoPulito)) {
            throw new \InvalidArgumentException("Il numero di telefono deve essere composto esclusivamente da un minimo di 9 a un massimo di 11 cifre numeriche.");
        }
        
        $this->recapitoTelefonico = $telefonoPulito;
        return $this;
    }

    public function setAmministratore(Amministratore $amministratore): self {
        $this->amministratore = $amministratore;
        return $this;
    }
}
?>