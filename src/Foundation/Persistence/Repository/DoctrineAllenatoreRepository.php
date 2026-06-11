<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Allenatore;
use App\Entity\Palestra;
use App\Entity\Attivita;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAllenatoreRepository implements AllenatoreRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function findById(int $id): ?Allenatore
    {
        return $this->em->find(Allenatore::class, $id);
    }

    public function save(Allenatore $allenatore): void
    {
        $this->em->persist($allenatore);
        $this->em->flush();
    }

    public function delete(Allenatore $allenatore): void
    {
        $this->em->remove($allenatore);
        $this->em->flush();
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Allenatore::class)->findAll();
    }

    public function findByEmail(string $email): ?Allenatore
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Allenatore::class, 'a')
            ->where('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByPalestra(Palestra $palestra): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Allenatore::class, 'a')
            ->where('a.palestra = :palestra')
            ->setParameter('palestra', $palestra)
            ->orderBy('a.cognome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAbilitatiPerAttivita(Attivita $attivita): array
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Allenatore::class, 'a')
            ->join('a.attivitaAbilitate', 'att')
            ->where('att = :attivita')
            ->setParameter('attivita', $attivita)
            ->getQuery()
            ->getResult();
    }
}