<?php

namespace App\Foundation\Persistence;

use App\Entity\Allenatore;
use App\Entity\Attivita;
use App\Entity\Palestra;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAllenatoreRepository implements AllenatoreRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findById(int $id): ?Allenatore
    {
        return $this->entityManager->find(Allenatore::class, $id);
    }

    public function findByEmail(string $email): ?Allenatore
    {
        return $this->entityManager->getRepository(Allenatore::class)->findOneBy(['email' => $email]);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Allenatore::class)->findAll();
    }

    public function findByPalestra(Palestra $palestra): array
    {
        return $this->entityManager->getRepository(Allenatore::class)->findBy(['palestra' => $palestra]);
    }

    public function findAbilitatiPerAttivita(Attivita $attivita): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('a')
            ->from(Allenatore::class, 'a')
            ->join('a.attivitaAbilitate', 'att')
            ->where('att.id = :attivitaId')
            ->setParameter('attivitaId', $attivita->getId());

        return $qb->getQuery()->getResult();
    }

    public function existsByEmail(string $email): bool
    {
        $count = $this->entityManager->getRepository(Allenatore::class)
            ->count(['email' => $email]);
        return $count > 0;
    }

    public function existsByCF(string $CF): bool
    {
        $count = $this->entityManager->getRepository(Allenatore::class)
            ->count(['CF' => $CF]);
        return $count > 0;
    }

    public function save(\App\Entity\Utente $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete(\App\Entity\Utente $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}