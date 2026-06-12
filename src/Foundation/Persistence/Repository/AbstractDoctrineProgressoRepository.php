<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Cliente;
use App\Entity\Esercizio;
use App\Entity\Progresso;
use App\Entity\Repository\ProgressoRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractDoctrineProgressoRepository implements ProgressoRepositoryInterface
{
    public function __construct(protected readonly EntityManagerInterface $em) {}

    /**
     * Restituisce il FQCN della sottoclasse concreta di Progresso gestita
     * da questo repository (es. ProgressoCarico::class).
     */
    abstract protected function getEntityClass(): string;

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Progresso
    {
        return $this->em->find($this->getEntityClass(), $id);
    }

    public function delete(Progresso $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Progresso[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository($this->getEntityClass())
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Query per cliente
    // -------------------------------------------------------------------------

    /** @return Progresso[] */
    public function findByCliente(Cliente $cliente): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->where('p.cliente = :cliente')
            ->setParameter('cliente', $cliente)
            ->orderBy('p.data', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return Progresso[] */
    public function findByClienteAndEsercizio(Cliente $cliente, Esercizio $esercizio): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->where('p.cliente = :cliente')
            ->andWhere('p.esercizio = :esercizio')
            ->setParameter('cliente', $cliente)
            ->setParameter('esercizio', $esercizio)
            ->orderBy('p.data', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Query per esercizio
    // -------------------------------------------------------------------------

    /** @return Progresso[] */
    public function findByEsercizio(Esercizio $esercizio): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->where('p.esercizio = :esercizio')
            ->setParameter('esercizio', $esercizio)
            ->orderBy('p.data', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Query per intervallo di date
    // -------------------------------------------------------------------------

    /** @return Progresso[] */
    public function findByClienteInPeriodo(
        Cliente $cliente,
        \DateTimeImmutable $dal,
        \DateTimeImmutable $al
    ): array {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->where('p.cliente = :cliente')
            ->andWhere('p.data >= :dal')
            ->andWhere('p.data <= :al')
            ->setParameter('cliente', $cliente)
            ->setParameter('dal', $dal)
            ->setParameter('al', $al)
            ->orderBy('p.data', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Ultimo progresso registrato
    // -------------------------------------------------------------------------

    public function findUltimoByClienteAndEsercizio(
        Cliente $cliente,
        Esercizio $esercizio
    ): ?Progresso {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->where('p.cliente = :cliente')
            ->andWhere('p.esercizio = :esercizio')
            ->setParameter('cliente', $cliente)
            ->setParameter('esercizio', $esercizio)
            ->orderBy('p.data', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}