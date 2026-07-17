<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Attivita;
use App\Entity\Allenatore;
use App\Entity\Repository\AttivitaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAttivitaRepository implements AttivitaRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Attivita
    {
        return $this->em->find(Attivita::class, $id);
    }

    public function save(Attivita $attivita): void
    {
        $this->em->persist($attivita);
        $this->em->flush();
    }

    public function delete(Attivita $attivita): void
    {
        $this->em->remove($attivita);
        $this->em->flush();
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Attivita::class)->findAll();
    }

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------
    
    public function findByAllenatore(Allenatore $allenatore): array
    {
        return $this->em->createQueryBuilder()
            ->select('att')
            ->from(Attivita::class, 'att')
            ->join('att.allenatori', 'a')
            ->where('a = :allenatore')
            ->setParameter('allenatore', $allenatore)
            ->getQuery()
            ->getResult();
    }

    public function findByNome(string $nome): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Attivita::class, 'a')
            ->where('a.nome LIKE :nome')
            ->setParameter('nome', '%' . $nome . '%')
            ->orderBy('a.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function existsByNome(string $nome): bool
    {
        return (int) $this->em->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(Attivita::class, 'a')
            ->where('a.nome = :nome')
            ->setParameter('nome', $nome)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}