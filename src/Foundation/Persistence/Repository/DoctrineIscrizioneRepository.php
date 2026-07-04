<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Cliente;
use App\Entity\Iscrizione;
use App\Entity\Repository\IscrizioneRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineIscrizioneRepository implements IscrizioneRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    // --- CRUD base ---

    public function findById(int $id): ?Iscrizione
    {
        return $this->em->find(Iscrizione::class, $id);
    }

    public function save(Iscrizione $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Iscrizione $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Iscrizione[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Iscrizione::class)
            ->findAll();
    }

    // --- Metodi di dominio ---

    public function findByCliente(Cliente $cliente): ?Iscrizione
    {
        return $this->em->createQueryBuilder()
            ->select('i')
            ->from(Iscrizione::class, 'i')
            ->join('i.cliente', 'cl')
            ->where('cl = :cliente')
            ->setParameter('cliente', $cliente)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function clienteHaIscrizioneAttiva(Cliente $cliente): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(i.id)')
            ->from(Iscrizione::class, 'i')
            ->join('i.cliente', 'cl')
            ->where('cl = :cliente')
            ->andWhere('i.dataFine >= :oggi')
            ->setParameter('cliente', $cliente)
            ->setParameter('oggi',    new \DateTimeImmutable())
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /** @return Iscrizione[] */
    public function findScadute(): array
    {
        return $this->em->createQueryBuilder()
            ->select('i')
            ->from(Iscrizione::class, 'i')
            ->where('i.dataFine < :oggi')
            ->setParameter('oggi', new \DateTimeImmutable())
            ->orderBy('i.dataFine', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Iscrizione[] */
    public function findInScadenzaEntro(int $giorni): array
    {
        $oggi   = new \DateTimeImmutable();
        $limite = $oggi->modify("+{$giorni} days");

        return $this->em->createQueryBuilder()
            ->select('i')
            ->from(Iscrizione::class, 'i')
            ->where('i.dataFine >= :oggi')
            ->andWhere('i.dataFine <= :limite')
            ->setParameter('oggi',   $oggi)
            ->setParameter('limite', $limite)
            ->orderBy('i.dataFine', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Iscrizione[] */
    public function findAttive(): array
    {
        return $this->em->createQueryBuilder()
            ->select('i')
            ->from(Iscrizione::class, 'i')
            ->where('i.dataFine >= :oggi')
            ->setParameter('oggi', new \DateTimeImmutable())
            ->orderBy('i.dataFine', 'ASC')
            ->getQuery()
            ->getResult();
    }
}