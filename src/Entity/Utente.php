<?php

use Doctrine\Common\Collections\Collection;
use GymFly\Enum\Sesso;
abstract class Utente
{
    private ?int $id = null;
    private string $nome;
    private string $cognome;
    private string $email;
    private string $CF; //REVIEW da capire il tipo di dato quando si passa al database;
    private $profile_picture; //REVIEW da caire il tipo di dato
    private ?string $telefono;
    private string $indirizzo;
    private Sesso $sesso;
    private string $password;
    

    //per le relazioni
    private Collection $messaggiRicevuti;

    public function __construct(string $nome, string $cognome, string $email, string $CF, $profilePicture, int $telefono, string $indirizzo, Sesso $sesso, string $password = "")
    {
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->email = $email;
        $this->CF = $CF;
        $this->profile_picture = $profilePicture;
        $this->telefono = $telefono;
        $this->indirizzo = $indirizzo;
        $this->sesso = $sesso;
        $this->password = $password;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNome(): string
    {
        return $this->nome;
    }
    public function getCognome(): string
    {
        return $this->cognome;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getCF(): string
    {
        return $this->CF;
    }
    public function getProfilePicture()
    {    //REVIEW da caire il tipo di dato
        return $this->profile_picture;
    }
    public function getTelefono(): ?string
    {
        return $this->telefono;
    }
    public function getIndirizzo(): string
    {
        return $this->indirizzo;
    }
    public function getSesso(): Sesso
    {
        return $this->sesso;
    }
    public function getMessaggiRicevuti(): Collection
    {
        return $this->messaggiRicevuti;
    }
    public function setMessaggiRicevuti(Collection $messaggiRicevuti): self{
        $this->messaggiRicevuti = $messaggiRicevuti;
        return $this;
    }

    /**
     * Imposta il nome dell'utente.
     * * Accetta solo stringhe contenenti lettere (inclusi caratteri accentati e spazi).
     * La regex utilizzata:
     * ^           : Inizio stringa
     * [a-zA-Z...] : Accetta lettere (A-Z, a-z) e caratteri accentati (à, è, é, ì, ò, ù)
     * \s          : Permette gli spazi (per nomi composti come "Mario Rossi")
     * +           : Deve contenere almeno un carattere
     * $           : Fine stringa
     * /u          : Modificatore per il supporto UTF-8
     *
     * @param string $nome
     * @return self
     */
    public function setNome(string $nome): self
    {
        if (preg_match('/^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s]+$/u', $nome)) {
            $this->nome = $nome;
        }
        return $this;
    }

    /**
     * Imposta il cognome dell'utente.
     * Accetta lettere, spazi e apostrofi (es. "D'Angelo").
     * 
     * @param string $cognome
     * @return self
     */
    public function setCognome(string $cognome): self
    {
        // Aggiunto l'apostrofo (') dopo la lista delle lettere
        if (preg_match('/^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s\']+$/u', $cognome)) {
            $this->cognome = $cognome;
        }
        return $this;
    }

    public function setEmail(string $email): self
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            throw new \InvalidArgumentException('Invalid email address');
        }
        return $this;
    }
    public function setCF(string $CF): self
    {
        if (!preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $CF)) {
            throw new \InvalidArgumentException('Il Codice Fiscale è non valido.');
        }
        $this->CF = $CF;
        return $this;
    }
    public function setProfilePicture(string $profilePicture): self
    {
        $this->profile_picture = $profilePicture;
        return $this;
    }

    /**
     * Imposta il numero di telefono (solo formato nazionale).
     * Accetta solo stringhe composte da 9 o 10 cifre.
     * * @param string $telefono
     * @return self
     */
    public function setTelefono(string $telefono): self {
        // Rimuove eventuali spazi o trattini inseriti dall'utente per errore
        $telefono = str_replace([' ', '-', '.'], '', $telefono);

        //NOTE: Regex: ^\d{9,10}$
        // ^          : Inizio stringa
        // \d{9,10}   : Esattamente 9 o 10 cifre numeriche
        // $          : Fine stringa
        if (preg_match('/^\d{9,10}$/', $telefono)) {
            $this->telefono = $telefono;
        } else {
            throw new \InvalidArgumentException("Il numero di telefono deve essere composto da 9 o 10 cifre.");
        }
        return $this;
    }
    public function setIndirizzo(string $indirizzo): self
    {
        $this->indirizzo = $indirizzo;
        return $this;
    }
    public function setGender(Sesso $sesso): self {
        // Assegni il valore (M o F) definito nell'enum
        $this->sesso = $sesso;
        return $this;
    }

    public function setPassword(string $plainPassword): self
    {
        // Validazione opzionale (es. lunghezza minima)
        if (strlen($plainPassword) < 8) {
            throw new \InvalidArgumentException("La password è troppo corta.");
        }
        $this->password = $plainPassword;
        return $this;
    }
    abstract public function mssAllowed(): bool;
}
?>