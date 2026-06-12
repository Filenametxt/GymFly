<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Palestra;
use App\Entity\Sala;
use App\Entity\Repository\SalaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineSalaRepository implements SalaRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Sala
    {
        return $this->em->find(Sala::class, $id);
    }

    public function save(Sala $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Sala $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Sala[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Sala::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Query per palestra
    // -------------------------------------------------------------------------

    /** @return Sala[] */
    public function findByPalestra(Palestra $palestra): array
    {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Sala::class, 's')
            ->where('s.palestra = :palestra')
            ->setParameter('palestra', $palestra)
            ->orderBy('s.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Sala[] */
    public function findByPalestraConCapienzaMinima(Palestra $palestra, int $minPartecipanti): array
    {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Sala::class, 's')
            ->where('s.palestra = :palestra')
            ->andWhere('s.maxPartecipanti >= :min')
            ->setParameter('palestra', $palestra)
            ->setParameter('min', $minPartecipanti)
            ->orderBy('s.maxPartecipanti', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Unicità
    // -------------------------------------------------------------------------

    public function existsByNomeAndPalestra(string $nome, Palestra $palestra): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(Sala::class, 's')
            ->where('s.nome = :nome')
            ->andWhere('s.palestra = :palestra')
            ->setParameter('nome', $nome)
            ->setParameter('palestra', $palestra)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}