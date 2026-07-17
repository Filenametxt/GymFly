<?php
namespace App\Foundation\Persistence\Repository;

use App\Entity\Cliente;
use App\Entity\Palestra;
use App\Entity\AttivitaPianificata;
use App\Entity\Repository\ClienteRepositoryInterface;

class DoctrineClienteRepository extends AbstractDoctrineUtenteRepository implements ClienteRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return Cliente::class;
    }

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Cliente
    {
        return $this->em->find(Cliente::class, $id);
    }

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------
    public function findByEmail(string $email): ?Cliente
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->where('c.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByCF(string $CF): ?Cliente
    {
        return $this->em->getRepository(Cliente::class)->findOneBy(['CF' => $CF]);
    }

    public function findByStringa(string $query): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->where('c.nome LIKE :q OR c.cognome LIKE :q OR c.email LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByPalestra(Palestra $palestra): array
    {
        return $this->em->getRepository(Cliente::class)->findBy(['palestra' => $palestra]);
    }

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

    public function findConCertificatoInScadenzaEntro(int $giorni): array
    {
        $limite = (new \DateTimeImmutable())->modify('+' . $giorni . ' days'); //limite è una data che prende i giorni e li aggiunge al giorno di oggi
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->join('c.certificatoMedico', 'cm')
            ->where('cm.dataScadenza >= :oggi AND cm.dataScadenza <= :limite')
            ->setParameter('oggi', new \DateTimeImmutable())
            ->setParameter('limite', $limite)
            ->orderBy('c.cognome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByAttivitaPianificata(AttivitaPianificata $attivita): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->join('c.attivitaPianificate', 'ap')
            ->where('ap = :attivita')
            ->setParameter('attivita', $attivita)
            ->getQuery()
            ->getResult();
    }

    public function isIscrittoAAttivita(Cliente $cliente, AttivitaPianificata $attivita): bool
    {
        return $cliente->getAttivitaPianificate()->contains($attivita);
    }
}