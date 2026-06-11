<?php

namespace App\Foundation\Persistence\Repository;

// import da Entity — tipi del dominio
use App\Entity\Esercizio;
use App\Entity\GruppoMuscolare;
use App\Entity\Tipologia;
use App\Entity\Allenatore;
use App\Entity\Repository\EsercizioRepositoryInterface;

// import da Doctrine — solo qui, mai in Entity -> separazione delle responsabilità
use Doctrine\ORM\EntityManagerInterface;

/**
 * Implementazione concreta che usa Doctrine.
 * Vive in Foundation — Entity e Control non la conoscono mai direttamente.
 * Control riceve questa classe SOLO tramite dependency injection
 * come EsercizioRepositoryInterface, mai come DoctrineEsercizioRepository.
 */
class DoctrineEsercizioRepository implements EsercizioRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em  // ← Doctrine entra solo qui
    ) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Esercizio
    {
        return $this->em->find(Esercizio::class, $id);
    }

    public function save(Esercizio $esercizio): void
    {
        $this->em->persist($esercizio);
        $this->em->flush();
    }

    public function delete(Esercizio $esercizio): void
    {
        $this->em->remove($esercizio);
        $this->em->flush();
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Esercizio::class)->findAll();
    }

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    public function findByGruppoMuscolare(GruppoMuscolare $gruppo): array
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(Esercizio::class, 'e')
            ->join('e.gruppiMuscolari', 'g')
            ->where('g = :gruppo')
            ->setParameter('gruppo', $gruppo)
            ->getQuery()
            ->getResult();
    }

    public function findByTipologia(Tipologia $tipologia): array
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(Esercizio::class, 'e')
            ->where('e.tipologia = :tipologia')
            ->setParameter('tipologia', $tipologia)
            ->getQuery()
            ->getResult();
    }

    public function findByCreatore(Allenatore $allenatore): array
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(Esercizio::class, 'e')
            ->where('e.creatore = :creatore')
            ->setParameter('creatore', $allenatore)
            ->getQuery()
            ->getResult();
    }

    public function findSenzaCreatore(): array
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(Esercizio::class, 'e')
            ->where('e.creatore IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function existsByNome(string $nome): bool
    {
        return (int) $this->em->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from(Esercizio::class, 'e')
            ->where('e.nomeEsercizio = :nome')
            ->setParameter('nome', $nome)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }
}