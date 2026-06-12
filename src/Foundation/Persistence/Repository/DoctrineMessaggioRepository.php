<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Messaggio;
use App\Entity\Utente;
use App\Entity\Repository\MessaggioRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineMessaggioRepository implements MessaggioRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    // --- CRUD base ---

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

    // --- Posta in uscita ---

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

    public function countByMittente(Utente $mittente): int
    {
        return (int) $this->em->createQueryBuilder()
            ->select('COUNT(m.id)')
            ->from(Messaggio::class, 'm')
            ->where('m.mittente = :mittente')
            ->setParameter('mittente', $mittente)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // --- Posta in arrivo ---

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

    public function countByDestinatario(Utente $destinatario): int
    {
        return (int) $this->em->createQueryBuilder()
            ->select('COUNT(m.id)')
            ->from(Messaggio::class, 'm')
            ->join('m.destinatari', 'd')
            ->where('d = :destinatario')
            ->setParameter('destinatario', $destinatario)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // --- Ricerca contenuto ---

    /** @return Messaggio[] */
    public function findByOggettoContaining(string $partial): array
    {
        return $this->em->createQueryBuilder()
            ->select('m')
            ->from(Messaggio::class, 'm')
            ->where('LOWER(m.oggetto) LIKE LOWER(:partial)')
            ->setParameter('partial', '%' . $partial . '%')
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return Messaggio[] */
    public function findConversazione(Utente $mittente, Utente $destinatario): array
    {
        return $this->em->createQueryBuilder()
            ->select('m')
            ->from(Messaggio::class, 'm')
            ->join('m.destinatari', 'd')
            ->where('m.mittente = :mittente')
            ->andWhere('d = :destinatario')
            ->setParameter('mittente',    $mittente)
            ->setParameter('destinatario', $destinatario)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}