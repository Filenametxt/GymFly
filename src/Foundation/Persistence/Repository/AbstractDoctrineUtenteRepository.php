<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\UtenteRepositoryInterface;
use App\Entity\Utente;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractDoctrineUtenteRepository implements UtenteRepositoryInterface
{
    public function __construct(protected readonly EntityManagerInterface $em) {}

    /**
     * Restituisce il FQCN della sottoclasse concreta di Utente gestita
     * da questo repository (es. Cliente::class).
     */
    abstract protected function getEntityClass(): string;

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Utente
    {
        return $this->em->find($this->getEntityClass(), $id);
    }

    public function save(Utente $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Utente $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Utente[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository($this->getEntityClass())
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Lookup anagrafico
    // -------------------------------------------------------------------------

    public function findByEmail(string $email): ?Utente
    {
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from($this->getEntityClass(), 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByEmail(string $email): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from($this->getEntityClass(), 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function existsByCF(string $CF): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from($this->getEntityClass(), 'u')
            ->where('u.CF = :cf')
            ->setParameter('cf', $CF)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}