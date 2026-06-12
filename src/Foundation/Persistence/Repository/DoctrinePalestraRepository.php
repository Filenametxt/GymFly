<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Amministratore;
use App\Entity\Palestra;
use App\Entity\Repository\PalestraRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrinePalestraRepository implements PalestraRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    // --- CRUD base ---

    public function findById(int $id): ?Palestra
    {
        return $this->em->find(Palestra::class, $id);
    }

    public function save(Palestra $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Palestra $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Palestra[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Palestra::class)
            ->findAll();
    }

    // --- Lookup anagrafico ---

    public function findByEmail(string $email): ?Palestra
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from(Palestra::class, 'p')
            ->where('p.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return Palestra[] */
    public function findByNomeContaining(string $partial): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from(Palestra::class, 'p')
            ->where('LOWER(p.nome) LIKE LOWER(:partial)')
            ->setParameter('partial', '%' . $partial . '%')
            ->orderBy('p.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function existsByEmail(string $email): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Palestra::class, 'p')
            ->where('p.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function existsByNomeAndIndirizzo(string $nome, string $indirizzo): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Palestra::class, 'p')
            ->where('LOWER(p.nome) = LOWER(:nome)')
            ->andWhere('LOWER(p.indirizzo) = LOWER(:indirizzo)')
            ->setParameter('nome',      $nome)
            ->setParameter('indirizzo', $indirizzo)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}