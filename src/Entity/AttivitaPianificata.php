<?php

namespace App\Entity;

use GymFly\Enum\Giorno;

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
    private Attivita $attivita_di_riferimento;

    /** @var Cliente[] */
    private array $utenti = [];

    public function __construct(
        \DateTimeImmutable     $giorno,
        int        $orario,
        Sala       $sala,
        Allenatore $allenatore,
        Attivita   $attivitaDiRiferimento
    ) {
        $this->giorno               = $giorno;
        $this->orario               = $orario;
        $this->sala                 = $sala;
        $this->allenatore           = $allenatore;
        $this->attivita_di_riferimento = $attivitaDiRiferimento;
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
        return $this->attivita_di_riferimento;
    }

    public function getMaxPartecipanti(): int
    {
        return min(
            $this->attivita_di_riferimento->getMaxPartecipanti(),
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
        $this->attivita_di_riferimento = $attivita;
    }

    // -------------------------------------------------------------------------
    // Utenti / Clienti iscritti  (N-N)
    // -------------------------------------------------------------------------

    /** @return Cliente[] */
    public function getUtenti(): array
    {
        return $this->utenti;
    }

    public function addUtente(Cliente $cliente): void
    {
        if (!in_array($cliente, $this->utenti, true)) {
            $this->utenti[] = $cliente;
        }
    }

    public function removeUtente(Cliente $cliente): void
    {
        $this->utenti = array_values(
            array_filter($this->utenti, fn(Cliente $c) => $c !== $cliente)
        );
    }
}
?>
