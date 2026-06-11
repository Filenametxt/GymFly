<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\AttivitaPianificata;
use App\Entity\Cliente;
use App\Entity\Palestra;
use App\Entity\Repository\ClienteRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineClienteRepository implements ClienteRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    // --- CRUD base ---

    public function findById(int $id): ?Cliente
    {
        return $this->em->find(Cliente::class, $id);
    }

    public function save(Cliente $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(Cliente $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return Cliente[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(Cliente::class)
            ->findAll();
    }

    // --- Lookup anagrafico ---

    public function findByEmail(string $email): ?Cliente
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->where('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByCF(string $CF): ?Cliente
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->where('c.CF = :cf')
            ->setParameter('cf', $CF)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByEmail(string $email): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(Cliente::class, 'c')
            ->where('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function existsByCF(string $CF): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(Cliente::class, 'c')
            ->where('c.CF = :cf')
            ->setParameter('cf', $CF)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    // --- Filtro per palestra ---

    /** @return Cliente[] */
    public function findByPalestra(Palestra $palestra): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->where('c.palestra = :palestra')
            ->setParameter('palestra', $palestra)
            ->orderBy('c.cognome', 'ASC')
            ->addOrderBy('c.nome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // --- Stato abbonamento ---

    /** @return Cliente[] */
    public function findConAbbonamentoAttivo(): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->join('c.abbonamento', 'aa')
            ->where('aa.dataFine >= :oggi')
            ->setParameter('oggi', new \DateTimeImmutable())
            ->orderBy('c.cognome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Cliente[] */
    public function findSenzaAbbonamentoAttivo(): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->leftJoin('c.abbonamento', 'aa')
            ->where('aa.id IS NULL OR aa.dataFine < :oggi')
            ->setParameter('oggi', new \DateTimeImmutable())
            ->orderBy('c.cognome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // --- Stato certificato medico ---

    /** @return Cliente[] */
    public function findConCertificatoScadutoOAssente(): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->leftJoin('c.certificatoMedico', 'cm')
            ->where('cm.id IS NULL OR cm.dataScadenza < :oggi')
            ->setParameter('oggi', new \DateTimeImmutable())
            ->orderBy('c.cognome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Cliente[] */
    public function findConCertificatoInScadenzaEntro(int $giorni): array
    {
        $oggi   = new \DateTimeImmutable();
        $limite = $oggi->modify("+{$giorni} days");

        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->join('c.certificatoMedico', 'cm')
            ->where('cm.dataScadenza >= :oggi')
            ->andWhere('cm.dataScadenza <= :limite')
            ->setParameter('oggi',   $oggi)
            ->setParameter('limite', $limite)
            ->orderBy('cm.dataScadenza', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // --- Attività pianificate ---

    /** @return Cliente[] */
    public function findByAttivitaPianificata(AttivitaPianificata $attivita): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->join('c.attivitaPianificate', 'ap')
            ->where('ap = :attivita')
            ->setParameter('attivita', $attivita)
            ->orderBy('c.cognome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function isIscrittoAAttivita(Cliente $cliente, AttivitaPianificata $attivita): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(Cliente::class, 'c')
            ->join('c.attivitaPianificate', 'ap')
            ->where('c = :cliente')
            ->andWhere('ap = :attivita')
            ->setParameter('cliente',  $cliente)
            ->setParameter('attivita', $attivita)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}