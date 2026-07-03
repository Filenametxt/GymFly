<?php
namespace App\Entity;
use App\Enum\Sesso;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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

    /** @var Collection<int, Messaggio> */
    private Collection $messaggiInviati;
    /** @var Collection<int, Messaggio> */
    private Collection $messaggiRicevuti;

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
        $this->messaggiInviati = new ArrayCollection();
        $this->messaggiRicevuti = new ArrayCollection();

        $this->setNome($nome);
        $this->setCognome($cognome);
        $this->setEmail($email);
        $this->setCF($CF);
        $this->setIndirizzo($indirizzo);
        $this->setSesso($sesso);

        // Se la foto profilo è stata passata (non è null), usa il setter
        if ($profilePicture !== null) {
            $this->setProfilePicture($profilePicture);
        } else {
            $this->profilePicture = null;
            }
        
        // Se il telefono è stato passato (non è null e non è vuoto), avvia il setter con la pulizia
        if ($telefono !== null && trim($telefono) !== '') {
            $this->setTelefono($telefono);
        } else {
            $this->telefono = null;
        }
        $this->setPassword($password);
    
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

    public function getMessaggiInviati(): Collection
    {
        return $this->messaggiInviati;
    }

    public function getMessaggiRicevuti(): Collection
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
        $nomePulito = trim($nome);

        if ($nomePulito === '') {
            throw new \InvalidArgumentException("Il nome non può essere vuoto.");
        }

        if (!preg_match('/^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s]+$/u', $nomePulito)) {
            throw new \InvalidArgumentException("Il nome '$nome' contiene caratteri non validi. Sono ammesse solo lettere e spazi.");
        }

        $this->nome = $nomePulito;
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
        $cognomePulito = trim($cognome);

        if ($cognomePulito === '') {
            throw new \InvalidArgumentException("Il cognome non può essere vuoto.");
        }

        if (!preg_match('/^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s\']+$/u', $cognomePulito)) {
            throw new \InvalidArgumentException("Il cognome '$cognome' contiene caratteri non validi. Sono ammesse solo lettere, spazi e apostrofi.");
        }

        $this->cognome = $cognomePulito;
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
    // Rimuoviamo eventuali spazi vuoti accidentali all'inizio e alla fine
    $emailPulita = trim($email);

    // Controlliamo se il campo è vuoto
    if ($emailPulita === '') {
        throw new \InvalidArgumentException("L'email è obbligatoria e non può essere vuota.");
    }

    // Validazione del formato tramite il filtro nativo di PHP
    if (!filter_var($emailPulita, FILTER_VALIDATE_EMAIL)) {
        throw new \InvalidArgumentException("L'indirizzo email '$email' non è nel formato corretto (es. nome@esempio.com).");
    }

    // Se passa i controlli, salviamo il dato pulito
    $this->email = $emailPulita;
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
    // Rimuoviamo spazi bianchi accidentali e convertiamo tutto in maiuscolo
    $cfPulito = strtoupper(trim($CF));

    // Controlliamo se il campo è vuoto
    if ($cfPulito === '') {
        throw new \InvalidArgumentException("Il codice fiscale è obbligatorio e non può essere vuoto.");
    }

    // Validazione del formato standard italiano a 16 caratteri
    if (!preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $cfPulito)) {
        throw new \InvalidArgumentException("Il codice fiscale '$CF' non è valido. Deve essere composto da 16 caratteri alfanumerici nel formato standard.");
    }

    // Salviamo il dato pulito e formattato in maiuscolo
    $this->CF = $cfPulito;
    return $this;
}

    /**
     * Imposta il contenuto binario (BLOB) della foto profilo.
     *
     * @param string $profilePicture Contenuto binario dell'immagine.
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
        // Rimuoviamo spazi bianchi accidentali all'inizio e alla fine
        $indirizzoPulito = trim($indirizzo);

        // Essendo un campo obbligatorio, controlliamo che non sia vuoto dopo il trim
        if ($indirizzoPulito === '') {
            throw new \InvalidArgumentException("L'indirizzo è obbligatorio e non può essere vuoto.");
        }

        $this->indirizzo = $indirizzoPulito;
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
    // Rimuoviamo eventuali spazi vuoti accidentali inseriti dall'utente
    $passwordPulita = trim($plainPassword);

    // 1. Controllo stringa vuota
    if ($passwordPulita === '') {
        throw new \InvalidArgumentException("La password è obbligatoria e non può essere vuota.");
    }

    // 2. Controllo lunghezza minima (sulla password pulita)
    if (strlen($passwordPulita) < 8) {
        throw new \InvalidArgumentException("La password deve contenere almeno 8 caratteri.");
    }

    // 3. Generazione dell'hash sicuro (Bcrypt)
    // La password in chiaro NON viene mai salvata nella proprietà!
    $this->password = password_hash($passwordPulita, PASSWORD_BCRYPT);
    
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
        if (!$this->messaggiInviati->contains($messaggio)) {
            $this->messaggiInviati->add($messaggio);
        }
        return $this;
    }

    /**
     * Aggiunge un messaggio alla lista dei messaggi ricevuti.
     */
    public function aggiungiMessaggioRicevuto(Messaggio $messaggio): self
    {
        if (!$this->messaggiRicevuti->contains($messaggio)) {
            $this->messaggiRicevuti->add($messaggio);
        }
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