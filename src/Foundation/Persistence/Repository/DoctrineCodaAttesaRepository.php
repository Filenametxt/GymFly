<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\AttivitaPianificata;
use App\Entity\Cliente;
use App\Entity\CodaAttesa;
use App\Entity\Repository\CodaAttesaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCodaAttesaRepository implements CodaAttesaRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function findById(int $id): ?CodaAttesa
    {
        return $this->em->find(CodaAttesa::class, $id);
    }

    public function save(CodaAttesa $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(CodaAttesa $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * @return CodaAttesa[]
     */
    public function findByAttivitaPianificata(AttivitaPianificata $attivita): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(CodaAttesa::class, 'c')
            ->where('c.attivitaPianificata = :attivita')
            ->setParameter('attivita', $attivita)
            ->orderBy('c.dataInserimento', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return CodaAttesa[]
     */
    public function findByCliente(Cliente $cliente): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(CodaAttesa::class, 'c')
            ->where('c.cliente = :cliente')
            ->setParameter('cliente', $cliente)
            ->getQuery()
            ->getResult();
    }

    public function findOneByClienteAndAttivita(Cliente $cliente, AttivitaPianificata $attivita): ?CodaAttesa
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(CodaAttesa::class, 'c')
            ->where('c.cliente = :cliente')
            ->andWhere('c.attivitaPianificata = :attivita')
            ->setParameter('cliente', $cliente)
            ->setParameter('attivita', $attivita)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPrimoInCoda(AttivitaPianificata $attivita): ?CodaAttesa
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(CodaAttesa::class, 'c')
            ->where('c.attivitaPianificata = :attivita')
            ->setParameter('attivita', $attivita)
            ->orderBy('c.dataInserimento', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsInCoda(Cliente $cliente, AttivitaPianificata $attivita): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(CodaAttesa::class, 'c')
            ->where('c.cliente = :cliente')
            ->andWhere('c.attivitaPianificata = :attivita')
            ->setParameter('cliente', $cliente)
            ->setParameter('attivita', $attivita)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
