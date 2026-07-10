<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\AttivitaPianificata;
use App\Entity\Cliente;
use App\Entity\Palestra;
use App\Entity\Repository\ClienteRepositoryInterface;

class DoctrineClienteRepository extends AbstractDoctrineUtenteRepository
    implements ClienteRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return Cliente::class;
    }

    // -------------------------------------------------------------------------
    // CRUD tipizzato
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Cliente
    {
        return $this->em->find(Cliente::class, $id);
    }

    // -------------------------------------------------------------------------
    // Lookup anagrafico
    // -------------------------------------------------------------------------

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

    /**
     * @inheritDoc
     */
    public function findByStringa(string $query): array
    {
        return $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->where('LOWER(c.nome) LIKE LOWER(:query)')
            ->orWhere('LOWER(c.cognome) LIKE LOWER(:query)')
            ->orWhere('LOWER(c.email) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('c.cognome', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Filtro per palestra
    // -------------------------------------------------------------------------

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

    /**
     * @inheritDoc
     */
    public function findByPalestraAndFiltri(
        Palestra $palestra, 
        ?string $query, 
        ?string $filtroCertificato,
        ?string $filtroAbbonamento = null,
        ?string $ordine = null
    ): array {
        $qb = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Cliente::class, 'c')
            ->where('c.palestra = :palestra')
            ->setParameter('palestra', $palestra);

        if ($query !== null && trim($query) !== '') {
            $qb->andWhere('(LOWER(c.nome) LIKE LOWER(:search) OR LOWER(c.cognome) LIKE LOWER(:search) OR LOWER(c.email) LIKE LOWER(:search))')
               ->setParameter('search', '%' . trim($query) . '%');
        }

        if ($filtroCertificato !== null && trim($filtroCertificato) !== '') {
            $qb->leftJoin('c.certificatoMedico', 'cm');
            if ($filtroCertificato === 'scaduti') {
                $qb->andWhere('cm.id IS NULL OR cm.dataScadenza < :oggi')
                   ->setParameter('oggi', new \DateTimeImmutable('today'));
            } elseif ($filtroCertificato === 'in_scadenza') {
                $oggi = new \DateTimeImmutable('today');
                $limite = $oggi->modify('+30 days');
                $qb->andWhere('cm.id IS NOT NULL AND cm.dataScadenza >= :oggi AND cm.dataScadenza <= :limite')
                   ->setParameter('oggi', $oggi)
                   ->setParameter('limite', $limite);
            } elseif ($filtroCertificato === 'in_regola') {
                $limite = (new \DateTimeImmutable('today'))->modify('+30 days');
                $qb->andWhere('cm.id IS NOT NULL AND cm.dataScadenza > :limite')
                   ->setParameter('limite', $limite);
            }
        }

        if ($filtroAbbonamento !== null && trim($filtroAbbonamento) !== '') {
            if ($filtroAbbonamento === 'attivo') {
                $qb->join('c.abbonamento', 'aa')
                   ->andWhere('aa.dataFine >= :oggiAbb')
                   ->setParameter('oggiAbb', new \DateTimeImmutable('today'));
            } elseif ($filtroAbbonamento === 'scaduto') {
                $qb->leftJoin('c.abbonamento', 'aa')
                   ->andWhere('aa.id IS NULL OR aa.dataFine < :oggiAbb')
                   ->setParameter('oggiAbb', new \DateTimeImmutable('today'));
            }
        }

        if ($ordine === 'cognome_desc') {
            $qb->orderBy('c.cognome', 'DESC')->addOrderBy('c.nome', 'ASC');
        } elseif ($ordine === 'nome_asc') {
            $qb->orderBy('c.nome', 'ASC')->addOrderBy('c.cognome', 'ASC');
        } elseif ($ordine === 'nome_desc') {
            $qb->orderBy('c.nome', 'DESC')->addOrderBy('c.cognome', 'ASC');
        } else {
            // Default: cognome_asc
            $qb->orderBy('c.cognome', 'ASC')->addOrderBy('c.nome', 'ASC');
        }

        return $qb->getQuery()->getResult();
    }

    // -------------------------------------------------------------------------
    // Stato abbonamento
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Stato certificato medico
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Attività pianificate
    // -------------------------------------------------------------------------

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