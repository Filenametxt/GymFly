<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Messaggio
{
    private ?int $id = null;
    private Utente $mittente;
    private string $oggetto;
    private string $contenuto;

    // un messaggio può avere più destinatari (relazione Molti-Molti con Utente)
    /** @var Collection<int, Utente> */
    private Collection $destinatari;

    public function __construct(
        Utente $mittente,
        string $oggetto,
        string $contenuto,
    ) {
        $this->destinatari = new ArrayCollection();
        if (!$mittente->mssAllowed()) {
            throw new \InvalidArgumentException('Questo utente non è autorizzato ad inviare messaggi.');
        }

        $this->mittente = $mittente;
        $this->oggetto = $oggetto;
        $this->contenuto = $contenuto;

        // aggiorna anche il lato Utente della relazione
        $mittente->aggiungiMessaggioInviato($this);
    }

    // -------------------------------------------------------------------------
    // Getter
    // -------------------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMittente(): Utente
    {
        return $this->mittente;
    }

    public function getOggetto(): string
    {
        return $this->oggetto;
    }

    public function getContenuto(): string
    {
        return $this->contenuto;
    }

    /** @return Collection<int, Utente> */
    public function getDestinatari(): Collection
    {
        return $this->destinatari;
    }

    // -------------------------------------------------------------------------
    // Setter con validazione — regole di dominio
    // -------------------------------------------------------------------------

    /**
     * Cambia il mittente solo se l'utente è autorizzato ad inviare messaggi.
     * La regola mssAllowed() è definita in ogni sottoclasse di Utente.
     */
    public function setMittente(Utente $mittente): self
    {
        if (!$mittente->mssAllowed()) {
            throw new \InvalidArgumentException('Questo utente non è autorizzato ad inviare messaggi.');
        }
        $this->mittente = $mittente;
        return $this;
    }

    /**
     * Imposta l'oggetto del messaggio.
     * Non può essere una stringa vuota.
     */
    public function setOggetto(string $oggetto): self
    {
        if (trim($oggetto) === '') {
            throw new \InvalidArgumentException("L'oggetto del messaggio non può essere vuoto.");
        }
        $this->oggetto = $oggetto;
        return $this;
    }

    /**
     * Imposta il contenuto del messaggio.
     * Non può essere una stringa vuota.
     */
    public function setContenuto(string $contenuto): self
    {
        if (trim($contenuto) === '') {
            throw new \InvalidArgumentException('Il contenuto del messaggio non può essere vuoto.');
        }
        $this->contenuto = $contenuto;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Gestione destinatari
    // -------------------------------------------------------------------------

    /**
     * Aggiunge un destinatario al messaggio e aggiorna
     * anche il lato Utente della relazione (bidirezionalità).
     */
    public function aggiungiDestinatario(Utente $utente): self
    {
        if (!$this->destinatari->contains($utente)) {
            $this->destinatari->add($utente);

            // aggiorna il lato Utente della relazione
            $utente->aggiungiMessaggioRicevuto($this);
        }

        return $this;
    }

    /**
     * Rimuove un destinatario dal messaggio.
     */
    public function rimuoviDestinatario(Utente $utente): self
    {
        $this->destinatari->removeElement($utente);
        return $this;
    }

    /**
     * Verifica se un utente è tra i destinatari del messaggio.
     */
    public function hasDestinatario(Utente $utente): bool
    {
        return $this->destinatari->contains($utente);
    }
}