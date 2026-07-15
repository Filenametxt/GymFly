<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Allenamento;
use App\Entity\DettaglioAllenamento;
use App\Entity\Esercizio;
use App\Entity\Repository\DettaglioAllenamentoRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineDettaglioAllenamentoRepository implements DettaglioAllenamentoRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------


    public function findById(int $id): ?DettaglioAllenamento
    {
        return $this->em->find(DettaglioAllenamento::class, $id);
    }

    public function save(DettaglioAllenamento $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(DettaglioAllenamento $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return DettaglioAllenamento[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(DettaglioAllenamento::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Metodi di dominio
    // -------------------------------------------------------------------------

    /** @return DettaglioAllenamento[] */
    public function findByAllenamento(Allenamento $allenamento): array
    {
        return $this->em->createQueryBuilder()
            ->select('d')
            ->from(DettaglioAllenamento::class, 'd') //serie, carico, tempo, ripetizioni
            ->where('d.allenamento = :allenamento')
            ->setParameter('allenamento', $allenamento)
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByAllenamentoAndEsercizio(Allenamento $allenamento, Esercizio   $esercizio): ?DettaglioAllenamento {
        return $this->em->createQueryBuilder()
            ->select('d')
            ->from(DettaglioAllenamento::class, 'd')
            ->where('d.allenamento = :allenamento')
            ->andWhere('d.esercizio = :esercizio')
            ->setParameter('allenamento', $allenamento)
            ->setParameter('esercizio',   $esercizio)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countByAllenamento(Allenamento $allenamento): int
    {
        return (int) $this->em->createQueryBuilder()
            ->select('COUNT(d.id)')
            ->from(DettaglioAllenamento::class, 'd')
            ->where('d.allenamento = :allenamento')
            ->setParameter('allenamento', $allenamento)
            ->getQuery()
            ->getSingleScalarResult();
    }
}