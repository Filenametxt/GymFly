<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\AttivitaPianificata;
use App\Entity\Allenatore;
use App\Entity\Sala;
use App\Entity\Cliente;
use App\Entity\Repository\AttivitaPianificataRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineAttivitaPianificataRepository implements AttivitaPianificataRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?AttivitaPianificata
    {
        return $this->em->find(AttivitaPianificata::class, $id);
    }

    public function save(AttivitaPianificata $attivita): void
    {
        $this->em->persist($attivita);
        $this->em->flush();
    }

    public function delete(AttivitaPianificata $attivita): void
    {
        $this->em->remove($attivita);
        $this->em->flush();
    }

    public function findAll(): array
    {
        return $this->em->getRepository(AttivitaPianificata::class)->findAll();
    }

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    public function findByGiorno(\DateTimeImmutable $giorno): array
    {
        // confronta solo la data, indipendentemente dall'orario specifico: prende l'intera giornata
        $inizioGiorno = $giorno->setTime(0, 0, 0);
        $fineGiorno   = $giorno->setTime(23, 59, 59);

        return $this->em->createQueryBuilder()
            ->select('ap')
            ->from(AttivitaPianificata::class, 'ap')
            ->where('ap.giorno >= :inizio')
            ->andWhere('ap.giorno <= :fine')
            ->setParameter('inizio', $inizioGiorno)
            ->setParameter('fine', $fineGiorno)
            ->orderBy('ap.orario', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByAllenatore(Allenatore $allenatore): array
    {
        return $this->em->createQueryBuilder()
            ->select('ap')
            ->from(AttivitaPianificata::class, 'ap')
            ->where('ap.allenatore = :allenatore')
            ->setParameter('allenatore', $allenatore)
            ->orderBy('ap.giorno', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySala(Sala $sala): array
    {
        return $this->em->createQueryBuilder()
            ->select('ap')
            ->from(AttivitaPianificata::class, 'ap')
            ->where('ap.sala = :sala')
            ->setParameter('sala', $sala)
            ->orderBy('ap.giorno', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCliente(Cliente $cliente): array
    {
        return $this->em->createQueryBuilder()
            ->select('ap')
            ->from(AttivitaPianificata::class, 'ap')
            ->join('ap.utenti', 'c')
            ->where('c = :cliente')
            ->setParameter('cliente', $cliente)
            ->orderBy('ap.giorno', 'ASC')
            ->getQuery()
            ->getResult();
    }

}