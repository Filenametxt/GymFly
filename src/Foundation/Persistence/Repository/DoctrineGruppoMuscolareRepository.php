<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Esercizio;
use App\Entity\GruppoMuscolare;
use App\Entity\Repository\GruppoMuscolareRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineGruppoMuscolareRepository implements GruppoMuscolareRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    // --- CRUD base ---

    public function findById(int $id): ?GruppoMuscolare
    {
        return $this->em->find(GruppoMuscolare::class, $id);
    }

    public function save(GruppoMuscolare $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(GruppoMuscolare $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return GruppoMuscolare[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(GruppoMuscolare::class)
            ->findAll();
    }

    // --- Metodi di dominio ---

    public function findByNome(string $nome): ?GruppoMuscolare
    {
        return $this->em->createQueryBuilder()
            ->select('g')
            ->from(GruppoMuscolare::class, 'g')
            ->where('LOWER(g.nomeGruppoMuscolare) = LOWER(:nome)')
            ->setParameter('nome', $nome)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByNome(string $nome): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(g.id)')
            ->from(GruppoMuscolare::class, 'g')
            ->where('LOWER(g.nomeGruppoMuscolare) = LOWER(:nome)')
            ->setParameter('nome', $nome)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /** @return GruppoMuscolare[] */
    public function findByNomeContaining(string $partial): array
    {
        return $this->em->createQueryBuilder()
            ->select('g')
            ->from(GruppoMuscolare::class, 'g')
            ->where('LOWER(g.nomeGruppoMuscolare) LIKE LOWER(:partial)')
            ->setParameter('partial', '%' . $partial . '%')
            ->orderBy('g.nomeGruppoMuscolare', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return GruppoMuscolare[] */
    public function findByEsercizio(Esercizio $esercizio): array
    {
        return $this->em->createQueryBuilder()
            ->select('g')
            ->from(GruppoMuscolare::class, 'g')
            ->join('g.esercizi', 'e')
            ->where('e = :esercizio')
            ->setParameter('esercizio', $esercizio)
            ->orderBy('g.nomeGruppoMuscolare', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return GruppoMuscolare[] */
    public function findSenzaEsercizi(): array
    {
        return $this->em->createQueryBuilder()
            ->select('g')
            ->from(GruppoMuscolare::class, 'g')
            ->leftJoin('g.esercizi', 'e')
            ->where('e.id IS NULL')
            ->orderBy('g.nomeGruppoMuscolare', 'ASC')
            ->getQuery()
            ->getResult();
    }
}