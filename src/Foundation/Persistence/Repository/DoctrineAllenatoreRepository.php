<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Allenatore;
use App\Entity\Attivita;
use App\Entity\Palestra;
use App\Entity\Repository\AllenatoreRepositoryInterface;

class DoctrineAllenatoreRepository extends AbstractDoctrineUtenteRepository
    implements AllenatoreRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return Allenatore::class;
    }

    // -------------------------------------------------------------------------
    // CRUD tipizzato
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Allenatore
    {
        return $this->em->find(Allenatore::class, $id);
    }

    public function save(Allenatore $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Allenatore $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Allenatore[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Allenatore::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Lookup anagrafico
    // -------------------------------------------------------------------------

    public function findByEmail(string $email): ?Allenatore
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
            ->join('a.attivitaAbilitate', 'att')
            ->where('att = :attivita')
            ->setParameter('attivita', $attivita)
            ->getQuery()
            ->getResult();
    }
}