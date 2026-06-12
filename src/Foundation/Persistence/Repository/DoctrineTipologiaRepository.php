<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Tipologia;
use App\Entity\Repository\TipologiaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTipologiaRepository implements TipologiaRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Tipologia
    {
        return $this->em->find(Tipologia::class, $id);
    }

    public function save(Tipologia $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Tipologia $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Tipologia[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Tipologia::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Ricerca per nome
    // -------------------------------------------------------------------------

    public function findByNome(string $nomeTipologia): ?Tipologia
    {
        return $this->em->createQueryBuilder()
            ->select('t')
            ->from(Tipologia::class, 't')
            ->where('LOWER(t.nomeTipologia) = LOWER(:nome)')
            ->setParameter('nome', $nomeTipologia)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByNome(string $nomeTipologia): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(Tipologia::class, 't')
            ->where('LOWER(t.nomeTipologia) = LOWER(:nome)')
            ->setParameter('nome', $nomeTipologia)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}