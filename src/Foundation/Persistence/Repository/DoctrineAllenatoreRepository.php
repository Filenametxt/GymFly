<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Allenatore;
use App\Entity\Attivita;
use App\Entity\Palestra;
use App\Entity\Repository\AllenatoreRepositoryInterface;

class DoctrineAllenatoreRepository extends AbstractDoctrineUtenteRepository //prende il costruttore del padre Utente
    implements AllenatoreRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return Allenatore::class;
    }

    // -------------------------------------------------------------------------
    // CRUD specifici perchè gli altri li eredita dal padre Utente
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Allenatore
    {
        return $this->em->find(Allenatore::class, $id);
    }

    // -------------------------------------------------------------------------
    // Lookup anagrafico
    // -------------------------------------------------------------------------

    public function findByEmail(string $email): ?Allenatore //restituisce specificatamente l'allenatore, non un generico utente.
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Allenatore::class, 'a')
            ->where('a.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // -------------------------------------------------------------------------
    // Filtro per palestra
    // -------------------------------------------------------------------------

    /** @return Allenatore[] */
    public function findByPalestra(Palestra $palestra): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Allenatore::class, 'a')
            ->where('a.palestra = :palestra')
            ->setParameter('palestra', $palestra)
            ->orderBy('a.cognome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Abilitazioni
    // -------------------------------------------------------------------------

    /** @return Allenatore[] */
    public function findAbilitatiPerAttivita(Attivita $attivita): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Allenatore::class, 'a')
            ->join('a.attivitaAbilitate', 'att') //join tra le attività abilitate per un allenatore e un'attività
            ->where('att = :attivita') 
            ->setParameter('attivita', $attivita)
            ->getQuery()
            ->getResult();
    }
}