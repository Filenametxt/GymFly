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
    public function __construct(
        private EntityManagerInterface $em
    ) {}

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

    public function findByGiorno(\DateTimeImmutable $giorno): array
    {
        // confronta solo la data, ignorando l'orario
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
        // join sulla tabella ISCRITTO tramite la relazione N-N
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

    public function findDisponibiliPerGiorno(\DateTimeImmutable $giorno): array
    {
        $inizioGiorno = $giorno->setTime(0, 0, 0);
        $fineGiorno   = $giorno->setTime(23, 59, 59);

        // carica anche sala e attività per calcolare getMaxPartecipanti()
        // il filtro sui posti disponibili viene fatto in memoria
        // perché getMaxPartecipanti() confronta sala.maxPartecipanti e attivita.maxPartecipanti
        $tutte = $this->em->createQueryBuilder()
            ->select('ap', 's', 'att')
            ->from(AttivitaPianificata::class, 'ap')
            ->join('ap.sala', 's')
            ->join('ap.attivitaDiRiferimento', 'att')
            ->where('ap.giorno >= :inizio')
            ->andWhere('ap.giorno <= :fine')
            ->setParameter('inizio', $inizioGiorno)
            ->setParameter('fine', $fineGiorno)
            ->orderBy('ap.orario', 'ASC')
            ->getQuery()
            ->getResult();

        // delega il calcolo dei posti all'Entity — regola di dominio
        return array_values(
            array_filter($tutte, fn(AttivitaPianificata $ap) =>
                $ap->getPrenotati() < $ap->getMaxPartecipanti()
            )
        );
    }
}