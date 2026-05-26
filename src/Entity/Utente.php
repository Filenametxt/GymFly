<?php
class Utente{
    private ?int $id = null;
    private string $nome;
    private string $cognome;
    private string $email;
    private string $CF; //REVIEW da capire il tipo di dato quando si passa al database;
    private $profile_picture; //REVIEW da caire il tipo di dato
    private int $telefono;
    private string $indirizzo;
    private string $sesso;

    public function __construct(string $nome, string $cognome, string $email, string $CF, $profilePicture, int $telefono, string $indirizzo, string $sesso) {
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->email = $email;
        $this->CF = $CF;
        $this->profile_picture = $profilePicture;
        $this->telefono = $telefono;
        $this->indirizzo = $indirizzo;
        $this->sesso = $sesso;
    }
    public function getId(): ?int{
        return $this->id;
    }
    public function getNome(): ?string {
        return $this-> nome;
    }
    public function getCognome(): ?string{
        return $this-> cognome;
    }
    public function getEmail(): ?string{
        return $this-> email;
    }
    public function getCF(): ?string{
        return $this-> CF;
    }
    public function getProfilePicture(){
        return $this->profile_picture;
    }
    public function getTelefono(): int{
        return $this->telefono;
    }
    public function getIndirizzo(): string{
        return $this->indirizzo;
    }
    public function getSesso(): string{
        return $this->sesso;
    }
    public function setNome(string $nome): self{
        $this->nome = $nome;
        return $this;
    }
    public function setCognome(string $cognome): self{
        $this-> cognome = $cognome;
        return $this;
    }
    public function setEmail (string $email): self{
        $this->email = $email;
        return $this;
    }
    public function setCF(string $CF): self{
        $this->CF = $CF;
        return $this;
    }
    public function setProfilePicture(string $profilePicture): self{
        $this->profile_picture = $profilePicture;
        return $this;
    }
    public function setTelefono(int $telefono): self{
        $this->telefono = $telefono;
        return $this;
    }
    public function setIndirizzo(string $indirizzo): self{
        $this->indirizzo = $indirizzo;
        return $this;
    }
    public function setSesso(string $sesso): self{
        $this->sesso = $sesso;
        return $this;
    }
}
?>