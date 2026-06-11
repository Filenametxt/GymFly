<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Abbonamento;
use App\Entity\Repository\AbbonamentoRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAbbonamentoRepository implements AbbonamentoRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Abbonamento
    {
        return $this->em->find(Abbonamento::class, $id);
    }

    public function save(Abbonamento $abbonamento): void
    {
        $this->em->persist($abbonamento);
        $this->em->flush();
    }

    public function delete(Abbonamento $abbonamento): void
    {
        $this->em->remove($abbonamento);
        $this->em->flush();
    }

    public function findAll(): array
    {
        // restituisce tutte le sottoclassi concrete (AbbonamentoDurata, ecc.)
        return $this->em->getRepository(Abbonamento::class)->findAll();
    }


    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    public function findByTipologia(string $tipologia): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Abbonamento::class, 'a')
            ->where('a.tipologia = :tipologia')
            ->setParameter('tipologia', $tipologia)
            ->getQuery()
            ->getResult();
    }

    public function findByCategoria(string $categoria): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Abbonamento::class, 'a')
            ->where('a.categoria = :categoria')
            ->setParameter('categoria', $categoria)
            ->getQuery()
            ->getResult();
    }

    public function existsByTipologiaAndCategoria(string $tipologia, string $categoria): bool
    {
        return (int) $this->em->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(Abbonamento::class, 'a')
            ->where('a.tipologia = :tipologia')
            ->andWhere('a.categoria = :categoria')
            ->setParameter('tipologia', $tipologia)
            ->setParameter('categoria', $categoria)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}