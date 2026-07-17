<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Messaggio;
use App\Entity\Utente;
use App\Entity\Repository\MessaggioRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineMessaggioRepository implements MessaggioRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Messaggio
    {
        return $this->em->find(Messaggio::class, $id);
    }

    public function save(Messaggio $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Messaggio $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Messaggio[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Messaggio::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    /** @return Messaggio[] */
    public function findByMittente(Utente $mittente): array
    {
        return $this->em->createQueryBuilder()
            ->select('m')
            ->from(Messaggio::class, 'm')
            ->where('m.mittente = :mittente')
            ->setParameter('mittente', $mittente)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return Messaggio[] */
    public function findByDestinatario(Utente $destinatario): array
    {
        return $this->em->createQueryBuilder()
            ->select('m')
            ->from(Messaggio::class, 'm')
            ->join('m.destinatari', 'd')
            ->where('d = :destinatario')
            ->setParameter('destinatario', $destinatario)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
}