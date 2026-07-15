<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\SessionePrivata;
use App\Entity\Repository\SessionePrivataRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineSessionePrivataRepository implements SessionePrivataRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findByChiave(Allenatore $allenatore, \DateTimeImmutable $oraInizio, \DateTimeImmutable $oraFine): ?SessionePrivata {
        return $this->em->createQueryBuilder()
            ->select('sp')
            ->from(SessionePrivata::class, 'sp')
            ->where('sp.allenatore = :allenatore')
            ->andWhere('sp.oraInizio = :oraInizio')
            ->andWhere('sp.oraFine = :oraFine')
            ->setParameter('allenatore', $allenatore)
            ->setParameter('oraInizio', $oraInizio)
            ->setParameter('oraFine', $oraFine)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(SessionePrivata $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(SessionePrivata $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return SessionePrivata[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(SessionePrivata::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Query per allenatore
    // -------------------------------------------------------------------------

    /** @return SessionePrivata[] */
    public function findByAllenatore(Allenatore $allenatore): array
    {
        return $this->em->createQueryBuilder()
            ->select('sp')
            ->from(SessionePrivata::class, 'sp')
            ->where('sp.allenatore = :allenatore')
            ->setParameter('allenatore', $allenatore)
            ->orderBy('sp.data', 'ASC')
            ->addOrderBy('sp.oraInizio', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Query per cliente
    // -------------------------------------------------------------------------

    /** @return SessionePrivata[] */
    public function findByCliente(Cliente $cliente): array
    {
        return $this->em->createQueryBuilder()
            ->select('sp')
            ->from(SessionePrivata::class, 'sp')
            ->where('sp.atleta = :cliente')
            ->setParameter('cliente', $cliente)
            ->orderBy('sp.data', 'ASC')
            ->addOrderBy('sp.oraInizio', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Query per data
    // -------------------------------------------------------------------------

    /** @return SessionePrivata[] */
    public function findByData(\DateTimeImmutable $data): array
    {
        return $this->em->createQueryBuilder()
            ->select('sp')
            ->from(SessionePrivata::class, 'sp')
            ->where('sp.data = :data')
            ->setParameter('data', $data)
            ->orderBy('sp.oraInizio', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Controllo sovrapposizioni
    // -------------------------------------------------------------------------

    public function existsSovrapposizioneAllenatore(Allenatore $allenatore, \DateTimeImmutable $data, \DateTimeImmutable $oraInizio, \DateTimeImmutable $oraFine): bool {
        // Due intervalli [A,B] e [C,D] si sovrappongono se A < D && C < B
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(sp.oraInizio)')
            ->from(SessionePrivata::class, 'sp')
            ->where('sp.allenatore = :allenatore')
            ->andWhere('sp.data = :data')
            ->andWhere('sp.oraInizio < :oraFine')
            ->andWhere('sp.oraFine > :oraInizio')
            ->setParameter('allenatore', $allenatore)
            ->setParameter('data', $data)
            ->setParameter('oraInizio', $oraInizio)
            ->setParameter('oraFine', $oraFine)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function existsSovrapposizioneCliente(Cliente $cliente, \DateTimeImmutable $data, \DateTimeImmutable $oraInizio, \DateTimeImmutable $oraFine): bool {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(sp.oraInizio)')
            ->from(SessionePrivata::class, 'sp')
            ->where('sp.atleta = :cliente')
            ->andWhere('sp.data = :data')
            ->andWhere('sp.oraInizio < :oraFine')
            ->andWhere('sp.oraFine > :oraInizio')
            ->setParameter('cliente', $cliente)
            ->setParameter('data', $data)
            ->setParameter('oraInizio', $oraInizio)
            ->setParameter('oraFine', $oraFine)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}