<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Amministratore;
use App\Entity\Palestra;
use App\Entity\Repository\AmministratoreRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAmministratoreRepository implements AmministratoreRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function findById(int $id): ?Amministratore
    {
        return $this->em->find(Amministratore::class, $id);
    }

    public function save(Amministratore $amministratore): void
    {
        $this->em->persist($amministratore);
        $this->em->flush();
    }

    public function delete(Amministratore $amministratore): void
    {
        $this->em->remove($amministratore);
        $this->em->flush();
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Amministratore::class)->findAll();
    }

    public function findByEmail(string $email): ?Amministratore
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Amministratore::class, 'a')
            ->where('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByPalestra(Palestra $palestra): ?Amministratore
    {
        return $this->em->createQueryBuilder()
            ->select('a')
            ->from(Amministratore::class, 'a')
            ->join('App\Entity\Palestra', 'p', 'WITH', 'p.amministratore = a')
            ->where('p = :palestra')
            ->setParameter('palestra', $palestra)
            ->getQuery()
            ->getOneOrNullResult();
    }
}