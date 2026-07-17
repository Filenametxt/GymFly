<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Cliente;
use App\Entity\Parametri;
use App\Entity\Repository\ParametriRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineParametriRepository implements ParametriRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Parametri
    {
        return $this->em->find(Parametri::class, $id);
    }

    public function save(Parametri $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Parametri $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Parametri[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Parametri::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    /** @return Parametri[] */
    public function findByCliente(Cliente $cliente): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from(Parametri::class, 'p')
            ->where('p.cliente = :cliente')
            ->setParameter('cliente', $cliente)
            ->orderBy('p.data', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUltimaByCliente(Cliente $cliente): ?Parametri
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from(Parametri::class, 'p')
            ->where('p.cliente = :cliente')
            ->setParameter('cliente', $cliente)
            ->orderBy('p.data', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPrimaByCliente(Cliente $cliente): ?Parametri
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from(Parametri::class, 'p')
            ->where('p.cliente = :cliente')
            ->setParameter('cliente', $cliente)
            ->orderBy('p.data', 'ASC')
            ->addOrderBy('p.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByClienteAndData(Cliente $cliente, \DateTimeImmutable $data): bool {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Parametri::class, 'p')
            ->where('p.cliente = :cliente')
            ->andWhere('p.data = :data')
            ->setParameter('cliente', $cliente)
            ->setParameter('data',    $data)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function countByCliente(Cliente $cliente): int
    {
        return (int) $this->em->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Parametri::class, 'p')
            ->where('p.cliente = :cliente')
            ->setParameter('cliente', $cliente)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function salvaMisure(Parametri $parametri): void{
        $this->em->persist($parametri);
        $this->em->flush();
    }

}