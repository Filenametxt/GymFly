<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Allenamento;
use App\Entity\Scheda;
use App\Entity\Repository\AllenamentoRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAllenamentoRepository implements AllenamentoRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Allenamento
    {
        return $this->em->find(Allenamento::class, $id);
    }

    public function save(Allenamento $allenamento): void
    {
        $this->em->persist($allenamento);
        $this->em->flush();
    }

    public function delete(Allenamento $allenamento): void
    {
        $this->em->remove($allenamento);
        $this->em->flush();
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Allenamento::class)->findAll();
    }

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    public function findByScheda(Scheda $scheda): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Allenamento::class, 'a')
            ->where('a.scheda = :scheda')
            ->setParameter('scheda', $scheda)
            ->orderBy('a.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findSenzaScheda(): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Allenamento::class, 'a')
            ->where('a.scheda IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function existsByNomeInScheda(string $nome, Scheda $scheda): bool
    {
        return (int) $this->em->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(Allenamento::class, 'a')
            ->where('a.nome = :nome')
            ->andWhere('a.scheda = :scheda')
            ->setParameter('nome', $nome)
            ->setParameter('scheda', $scheda)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}