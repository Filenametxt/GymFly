<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Amministratore;
use App\Entity\Palestra;
use App\Entity\Repository\AmministratoreRepositoryInterface;

class DoctrineAmministratoreRepository extends AbstractDoctrineUtenteRepository
    implements AmministratoreRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return Amministratore::class;
    }

    // -------------------------------------------------------------------------
    // CRUD tipizzato
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Amministratore
    {
        return $this->em->find(Amministratore::class, $id);
    }

    public function save(Amministratore $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Amministratore $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Amministratore[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Amministratore::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Lookup anagrafico
    // -------------------------------------------------------------------------

    public function findByEmail(string $email): ?Amministratore
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Amministratore::class, 'a')
            ->where('a.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // -------------------------------------------------------------------------
    // Filtro per palestra
    // -------------------------------------------------------------------------

    public function findByPalestra(Palestra $palestra): ?Amministratore
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Amministratore::class, 'a')
            ->join('App\Entity\Palestra', 'p', 'WITH', 'p.amministratore = a')
            ->where('p = :palestra')
            ->setParameter('palestra', $palestra)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}