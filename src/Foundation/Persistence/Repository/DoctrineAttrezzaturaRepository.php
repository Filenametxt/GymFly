<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Attrezzatura;
use App\Entity\Repository\AttrezzaturaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAttrezzaturaRepository implements AttrezzaturaRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Attrezzatura
    {
        return $this->em->find(Attrezzatura::class, $id);
    }

    public function save(Attrezzatura $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Attrezzatura $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Attrezzatura[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Attrezzatura::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    public function findByNome(string $nome): ?Attrezzatura
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Attrezzatura::class, 'a')
            ->where('LOWER(a.nomeAttrezzatura) = LOWER(:nome)') // Confronto case-insensitive
            ->setParameter('nome', $nome)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByNome(string $nome): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(Attrezzatura::class, 'a')
            ->where('LOWER(a.nomeAttrezzatura) = LOWER(:nome)')
            ->setParameter('nome', $nome)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /** @return Attrezzatura[] */
    public function findByNomeContaining(string $partial): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Attrezzatura::class, 'a')
            ->where('LOWER(a.nomeAttrezzatura) LIKE LOWER(:partial)')
            ->setParameter('partial', '%' . $partial . '%')
            ->orderBy('a.nomeAttrezzatura', 'ASC')
            ->getQuery()
            ->getResult();
    }
}