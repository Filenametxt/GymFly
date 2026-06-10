<?php
namespace App\Entity;
use App\Enum\Sesso;

abstract class Utente
{
    private ?int $id = null;
    private string $nome;
    private string $cognome;
    private string $email;
    private string $CF;
    private ?string $profilePicture = null;
    private ?string $telefono = null;
    private string $indirizzo;
    private Sesso $sesso;
    private string $password;

    // array nativi PHP — zero dipendenze da Doctrine
    private array $messaggiInviati = [];
    private array $messaggiRicevuti = [];

    public function __construct(
        string $nome,
        string $cognome,
        string $email,
        string $CF,
        string $indirizzo,
        Sesso $sesso,
        string $password = "",
        ?string $profilePicture = null,
        ?string $telefono = null
    ) {
        $this->nome = $nome;
        $this->cognome = $cognome;
        $this->email = $email;
        $this->CF = $CF;
        $this->indirizzo = $indirizzo;
        $this->sesso = $sesso;
        $this->profilePicture = $profilePicture;
        $this->telefono = $telefono;

        if ($password !== "") {
            $this->setPassword($password);
        }
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

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

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
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

    public function getMessaggiInviati(): array
    {
        return $this->messaggiInviati;
    }

    public function getMessaggiRicevuti(): array
    {
        return $this->messaggiRicevuti;
    }

    // -------------------------------------------------------------------------
    // Setter con validazione — regole di dominio
    // -------------------------------------------------------------------------

    /**
     * Imposta il nome dell'utente.
     * Accetta solo lettere (inclusi caratteri accentati) e spazi.
     *
     * Regex: ^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s]+$  /u
     * ^    : inizio stringa
     * [..] : lettere latine + accentate + spazio
     * +    : almeno un carattere
     * $    : fine stringa
     * /u   : supporto UTF-8
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
     * Regex: ^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s']+$  /u
     */
    public function setCognome(string $cognome): self
    {
        if (preg_match('/^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s\']+$/u', $cognome)) {
            $this->cognome = $cognome;
        }
        return $this;
    }

    /**
     * Imposta l'email dell'utente.
     * Usa il filtro nativo PHP per la validazione del formato.
     *
     * @throws \InvalidArgumentException se il formato non è valido
     */
    public function setEmail(string $email): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Indirizzo email non valido.');
        }
        $this->email = $email;
        return $this;
    }

    /**
     * Imposta il codice fiscale dell'utente.
     * Valida il formato standard italiano a 16 caratteri.
     *
     * Regex: ^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$
     *
     * @throws \InvalidArgumentException se il formato non è valido
     */
    public function setCF(string $CF): self
    {
        if (!preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', strtoupper($CF))) {
            throw new \InvalidArgumentException('Codice fiscale non valido.');
        }
        $this->CF = strtoupper($CF);
        return $this;
    }

    /**
     * Imposta il percorso o URL della foto profilo.
     */
    public function setProfilePicture(string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    /**
     * Imposta il numero di telefono (solo formato nazionale).
     * Rimuove spazi, trattini e punti prima della validazione.
     *
     * Regex: ^\d{9,10}$
     * ^        : inizio stringa
     * \d{9,10} : esattamente 9 o 10 cifre
     * $        : fine stringa
     *
     * @throws \InvalidArgumentException se il formato non è valido
     */
    public function setTelefono(string $telefono): self
    {
        $telefono = str_replace([' ', '-', '.'], '', $telefono);
        if (!preg_match('/^\d{9,10}$/', $telefono)) {
            throw new \InvalidArgumentException('Numero di telefono non valido. Inserire 9 o 10 cifre.');
        }
        $this->telefono = $telefono;
        return $this;
    }

    /**
     * Imposta l'indirizzo di residenza dell'utente.
     */
    public function setIndirizzo(string $indirizzo): self
    {
        $this->indirizzo = $indirizzo;
        return $this;
    }

    /**
     * Imposta il sesso dell'utente tramite enum Sesso.
     */
    public function setSesso(Sesso $sesso): self
    {
        $this->sesso = $sesso;
        return $this;
    }

    /**
     * Imposta la password dell'utente hashandola con bcrypt.
     * La password in chiaro non viene mai salvata.
     *
     * @throws \InvalidArgumentException se la password è più corta di 8 caratteri
     */
    public function setPassword(string $plainPassword): self
    {
        if (strlen($plainPassword) < 8) {
            throw new \InvalidArgumentException('La password deve contenere almeno 8 caratteri.');
        }
        $this->password = password_hash($plainPassword, PASSWORD_BCRYPT);
        return $this;
    }

    /**
     * Verifica che la password in chiaro corrisponda all'hash salvato.
     * Da chiamare nel Control durante il login.
     */
    public function verificaPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->password);
    }

    // -------------------------------------------------------------------------
    // Gestione relazioni — messaggi
    // -------------------------------------------------------------------------

    /**
     * Aggiunge un messaggio alla lista dei messaggi inviati.
     */
    public function aggiungiMessaggioInviato(Messaggio $messaggio): self
    {
        $this->messaggiInviati[] = $messaggio;
        return $this;
    }

    /**
     * Aggiunge un messaggio alla lista dei messaggi ricevuti.
     */
    public function aggiungiMessaggioRicevuto(Messaggio $messaggio): self
    {
        $this->messaggiRicevuti[] = $messaggio;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Metodi astratti — ogni sottoclasse definisce il proprio comportamento
    // -------------------------------------------------------------------------

    /**
     * Indica se l'utente ha il permesso di inviare messaggi.
     * Ogni sottoclasse (Cliente, Allenatore, Amministratore) implementa
     * la propria regola di dominio.
     */
    abstract public function mssAllowed(): bool;

    /**
     * Restituisce il ruolo dell'utente come stringa.
     * Usato nel Control per il routing dopo il login.
     */
    abstract public function getRuolo(): string;
}
?>