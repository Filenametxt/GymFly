<?php

namespace App\Foundation\Persistence;

use App\Entity\Cliente;
use App\Entity\Parametri;
use App\Entity\Repository\ParametriRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineParametriRepository implements ParametriRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findById(int $id): ?Parametri
    {
        return $this->entityManager->find(Parametri::class, $id);
    }

    public function save(Parametri $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete(Parametri $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Parametri::class)->findAll();
    }

    public function findByCliente(Cliente $cliente): array
    {
        return $this->entityManager->getRepository(Parametri::class)->findBy(['cliente' => $cliente], ['data' => 'DESC']);
    }

    public function findUltimaByCliente(Cliente $cliente): ?Parametri
    {
        return $this->entityManager->getRepository(Parametri::class)->findOneBy(['cliente' => $cliente], ['data' => 'DESC']);
    }

    public function findPrimaByCliente(Cliente $cliente): ?Parametri
    {
        return $this->entityManager->getRepository(Parametri::class)->findOneBy(['cliente' => $cliente], ['data' => 'ASC']);
    }

    public function findByClienteInPeriodo(Cliente $cliente, \DateTimeImmutable $dal, \DateTimeImmutable $al): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
            ->from(Parametri::class, 'p')
            ->where('p.cliente = :cliente')
            ->andWhere('p.data BETWEEN :dal AND :al')
            ->setParameter('cliente', $cliente)
            ->setParameter('dal', $dal)
            ->setParameter('al', $al);
        return $qb->getQuery()->getResult();
    }

    public function existsByClienteAndData(Cliente $cliente, \DateTimeImmutable $data): bool
    {
        return $this->entityManager->getRepository(Parametri::class)->count(['cliente' => $cliente, 'data' => $data]) > 0;
    }

    public function countByCliente(Cliente $cliente): int
    {
        return $this->entityManager->getRepository(Parametri::class)->count(['cliente' => $cliente]);
    }

    public function salvaMisure(Parametri $parametri): void
    {
        $this->save($parametri);
    }
}