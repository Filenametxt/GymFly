<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Allenatore;
use App\Entity\Cliente;
use App\Entity\Scheda;
use App\Entity\Palestra;
use App\Entity\Repository\SchedaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineSchedaRepository implements SchedaRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Scheda
    {
        return $this->em->find(Scheda::class, $id);
    }

    public function save(Scheda $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Scheda $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Scheda[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Scheda::class)
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Query per cliente
    // -------------------------------------------------------------------------

    /** @return Scheda[] */
    public function findByCliente(Cliente $cliente): array
    {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scheda::class, 's')
            ->where('s.cliente = :cliente')
            ->setParameter('cliente', $cliente)
            ->orderBy('s.data_inizio', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAttivaByCliente(Cliente $cliente): ?Scheda
    {
        $oggi = new \DateTimeImmutable('today');

        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scheda::class, 's')
            ->where('s.cliente = :cliente')
            ->andWhere('s.data_inizio <= :oggi')
            ->andWhere('s.data_fine >= :oggi')
            ->setParameter('cliente', $cliente)
            ->setParameter('oggi', $oggi)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return Scheda[] */
    public function findScaduteByCliente(Cliente $cliente): array
    {
        $oggi = new \DateTimeImmutable('today');

        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scheda::class, 's')
            ->where('s.cliente = :cliente')
            ->andWhere('s.data_fine < :oggi')
            ->setParameter('cliente', $cliente)
            ->setParameter('oggi', $oggi)
            ->orderBy('s.data_fine', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Query per allenatore
    // -------------------------------------------------------------------------

    /** @return Scheda[] */
    public function findByAllenatore(Allenatore $allenatore): array
    {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scheda::class, 's')
            ->where('s.allenatore = :allenatore')
            ->setParameter('allenatore', $allenatore)
            ->orderBy('s.data_inizio', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return Scheda[] */
    public function findAttiveByAllenatore(Allenatore $allenatore): array
    {
        $oggi = new \DateTimeImmutable('today');

        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scheda::class, 's')
            ->where('s.allenatore = :allenatore')
            ->andWhere('s.data_inizio <= :oggi')
            ->andWhere('s.data_fine >= :oggi')
            ->setParameter('allenatore', $allenatore)
            ->setParameter('oggi', $oggi)
            ->orderBy('s.data_fine', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Query per scadenza imminente
    // -------------------------------------------------------------------------

    /** @return Scheda[] */
    public function findInScadenza(int $giorni): array
    {
        $oggi    = new \DateTimeImmutable('today');
        $limite  = $oggi->modify("+{$giorni} days");

        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scheda::class, 's')
            ->where('s.data_fine >= :oggi')
            ->andWhere('s.data_fine <= :limite')
            ->setParameter('oggi', $oggi)
            ->setParameter('limite', $limite)
            ->orderBy('s.data_fine', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Scheda[] */
    public function findByPalestra(Palestra $palestra): array
    {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scheda::class, 's')
            ->join('s.cliente', 'c')
            ->where('c.palestra = :palestra')
            ->setParameter('palestra', $palestra)
            ->getQuery()
            ->getResult();
    }

    /** @return Scheda[] */
    public function findAltreByPalestra(Palestra $palestra, int $escludiSchedaId): array
    {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scheda::class, 's')
            ->join('s.cliente', 'c')
            ->where('c.palestra = :palestra')
            ->andWhere('s.id != :attualeId')
            ->setParameter('palestra', $palestra)
            ->setParameter('attualeId', $escludiSchedaId)
            ->getQuery()
            ->getResult();
    }

    public function findPendenteByCliente(Cliente $cliente): ?Scheda
    {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scheda::class, 's')
            ->where('s.cliente = :cliente')
            ->andWhere('s.nome_scheda = :nomeScheda')
            ->setParameter('cliente', $cliente)
            ->setParameter('nomeScheda', 'Richiesta Nuova Scheda')
            ->getQuery()
            ->getOneOrNullResult();
    }
}