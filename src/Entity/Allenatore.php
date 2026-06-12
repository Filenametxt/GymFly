<?php

namespace App\Entity;

use App\Enum\Sesso;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Classe Allenatore – figlia di Utente (Class Table Inheritance).
 * Il mapping ORM è definito esternamente in:
 *   foundation/GymFly.Entity.Allenatore.dcm.xml
 */
class Allenatore extends Utente
{
    private Palestra $palestra;

    /** @var Collection<int, Attivita> */
    private Collection $attivitaAbilitate;


    public function __construct(
        string $nome,
        string $cognome,
        string $email,
        string $CF,
        string $indirizzo,
        Sesso $sesso,
        string $password = "",
        ?string $profilePicture = null,
        ?string $telefono = null,
        Palestra $palestra
    ) {
        $this->attivitaAbilitate = new ArrayCollection();
        parent::__construct($nome, $cognome, $email, $CF,$indirizzo,$sesso,$password, $profilePicture, $telefono);
        $this->palestra = $palestra;
    }

    // -------------------------------------------------------------------------
    // Palestra
    // -------------------------------------------------------------------------

    public function getPalestra(): Palestra
    {
        return $this->palestra;
    }

    public function setPalestra(Palestra $palestra): self
    {
        $this->palestra = $palestra;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Attività Abilitate  (N-N: l'allenatore è "abilitato" per certe attività)
    // -------------------------------------------------------------------------

    /** @return Collection<int, Attivita> */
    public function getAttivitaAbilitate(): Collection
    {
        return $this->attivitaAbilitate;
    }

    public function addAbilitazione(Attivita $attivita): self
    {
        if (!$this->attivitaAbilitate->contains($attivita)) {
            $this->attivitaAbilitate->add($attivita);
        }
        return $this;
    }

    public function removeAbilitazione(Attivita $attivita): self
    {
        $this->attivitaAbilitate->removeElement($attivita);
        return $this;
    }

    // -------------------------------------------------------------------------
    // Permessi
    // -------------------------------------------------------------------------

    public function mssAllowed(): bool
    {
        return true;
    }

    // -------------------------------------------------------------------------
    // Definizione del ruolo
    // -------------------------------------------------------------------------

    public function getRuolo(): string{
        return "allenatore";
    }
}
?>