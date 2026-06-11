<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\AbbonamentoAttivo;
use App\Entity\Abbonamento;
use App\Entity\Repository\AbbonamentoAttivoRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAbbonamentoAttivoRepository implements AbbonamentoAttivoRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?AbbonamentoAttivo
    {
        return $this->em->find(AbbonamentoAttivo::class, $id);
    }

    public function save(AbbonamentoAttivo $abbonamentoAttivo): void
    {
        $this->em->persist($abbonamentoAttivo);
        $this->em->flush();
    }

    public function delete(AbbonamentoAttivo $abbonamentoAttivo): void
    {
        $this->em->remove($abbonamentoAttivo);
        $this->em->flush();
    }

    public function findAll(): array
    {
        return $this->em->getRepository(AbbonamentoAttivo::class)->findAll();
    }

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    public function findByAbbonamento(Abbonamento $abbonamento): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(AbbonamentoAttivo::class, 'a')
            ->where('a.abbonamento = :abbonamento')
            ->setParameter('abbonamento', $abbonamento)
            ->getQuery()
            ->getResult();
    }

    public function findScaduti(): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(AbbonamentoAttivo::class, 'a')
            ->where('a.dataFine IS NOT NULL')
            ->andWhere('a.dataFine < :oggi')
            ->setParameter('oggi', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function findInScadenza(int $giorni): array
    {
        $oggi   = new \DateTimeImmutable();
        $limite = $oggi->modify("+{$giorni} days");

        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(AbbonamentoAttivo::class, 'a')
            ->where('a.dataFine IS NOT NULL')
            ->andWhere('a.dataFine >= :oggi')
            ->andWhere('a.dataFine <= :limite')
            ->setParameter('oggi', $oggi)
            ->setParameter('limite', $limite)
            ->getQuery()
            ->getResult();
    }

    public function findSenzaDataFine(): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(AbbonamentoAttivo::class, 'a')
            ->where('a.dataFine IS NULL')
            ->getQuery()
            ->getResult();
    }
}