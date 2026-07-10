<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\CertificatoMedico;
use App\Entity\Cliente;
use App\Entity\Repository\CertificatoMedicoRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCertificatoMedicoRepository implements CertificatoMedicoRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    // --- CRUD base ---

    public function findById(int $id): ?CertificatoMedico
    {
        return $this->em->find(CertificatoMedico::class, $id);
    }

    public function save(CertificatoMedico $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function delete(CertificatoMedico $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** @return CertificatoMedico[] */
    public function findAll(): array
    {
        return $this->em
            ->getRepository(CertificatoMedico::class)
            ->findAll();
    }

    // --- Metodi di dominio ---

    public function findByCliente(Cliente $cliente): ?CertificatoMedico
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(CertificatoMedico::class, 'c')
            ->join('c.cliente', 'cl')
            ->where('cl = :cliente')
            ->setParameter('cliente', $cliente)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return CertificatoMedico[] */
    public function findScaduti(): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(CertificatoMedico::class, 'c')
            ->where('c.dataScadenza < :oggi')
            ->setParameter('oggi', new \DateTimeImmutable())
            ->orderBy('c.dataScadenza', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return CertificatoMedico[] */
    public function findInScadenzaEntro(int $giorni): array
    {
        $oggi    = new \DateTimeImmutable();
        $limite  = $oggi->modify("+{$giorni} days");

        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(CertificatoMedico::class, 'c')
            ->where('c.dataScadenza >= :oggi')
            ->andWhere('c.dataScadenza <= :limite')
            ->setParameter('oggi',   $oggi)
            ->setParameter('limite', $limite)
            ->orderBy('c.dataScadenza', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return CertificatoMedico[] */
    public function findValidi(): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(CertificatoMedico::class, 'c')
            ->where('c.dataScadenza >= :oggi')
            ->setParameter('oggi', new \DateTimeImmutable())
            ->orderBy('c.dataScadenza', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function clienteHaCertificatoValido(Cliente $cliente): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(CertificatoMedico::class, 'c')
            ->join('c.cliente', 'cl')
            ->where('cl = :cliente')
            ->andWhere('c.dataScadenza >= :oggi')
            ->setParameter('cliente', $cliente)
            ->setParameter('oggi',    new \DateTimeImmutable())
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}