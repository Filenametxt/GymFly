<?php

namespace App\Entity;

use GymFly\Enum\Giorno;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Classe AttivitaPianificata.
 * Il mapping ORM è definito esternamente in:
 *   foundation/App.Entity.AttivitaPianificata.dcm.xml
 */
class AttivitaPianificata
{
    private ?int $id = null;
    private \DateTimeImmutable $giorno;
    private int $orario;
    private int $prenotati = 0;
    private Sala $sala;
    private Allenatore $allenatore;
    private Attivita $attivitaDiRiferimento;

    /** @var Collection<int, Cliente> */
    private Collection $utenti;

    public function __construct(
        \DateTimeImmutable     $giorno,
        int        $orario,
        Sala       $sala,
        Allenatore $allenatore,
        Attivita   $attivitaDiRiferimento
    ) {
        $this->utenti               = new ArrayCollection();
        $this->giorno               = $giorno;
        $this->orario               = $orario;
        $this->sala                 = $sala;
        $this->allenatore           = $allenatore;
        $this->attivitaDiRiferimento = $attivitaDiRiferimento;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGiorno(): \DateTimeImmutable
    {
        return $this->giorno;
    }

    public function getOrario(): int
    {
        return $this->orario;
    }

    public function getPrenotati(): int
    {
        return $this->prenotati;
    }

    public function getSala(): Sala
    {
        return $this->sala;
    }

    public function getAllenatore(): Allenatore
    {
        return $this->allenatore;
    }

    public function getAttivita(): Attivita
    {
        return $this->attivitaDiRiferimento;
    }

    public function getMaxPartecipanti(): int
    {
        return min(
            $this->attivitaDiRiferimento->getMaxPartecipanti(),
            $this->sala->getMaxPartecipanti()
        );
    }

    public function setGiorno(\DateTimeImmutable $giorno): void
    {
        $this->giorno = $giorno;
    }

    public function setOrario(int $orario): void
    {
        $this->orario = $orario;
    }

    public function setPrenotati(int $prenotati): void
    {
        $this->prenotati = $prenotati;
    }

    public function setSala(Sala $sala): void
    {
        $this->sala = $sala;
    }

    public function setAllenatore(Allenatore $allenatore): void
    {
        $this->allenatore = $allenatore;
    }

    public function setAttivita(Attivita $attivita): void
    {
        $this->attivitaDiRiferimento = $attivita;
    }

    // -------------------------------------------------------------------------
    // Utenti / Clienti iscritti  (N-N)
    // -------------------------------------------------------------------------

    /** @return Collection<int, Cliente> */
    public function getUtenti(): Collection
    {
        return $this->utenti;
    }

    public function aggiungiCliente(Cliente $cliente): void
    {
        if (!$this->utenti->contains($cliente)) {
            $this->utenti->add($cliente);
        }
    }

    public function rimuoviCliente(Cliente $cliente): void
    {
        $this->utenti->removeElement($cliente);
    }
}
?>
