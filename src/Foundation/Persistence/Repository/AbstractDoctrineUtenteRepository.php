<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Repository\UtenteRepositoryInterface;
use App\Entity\Utente;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractDoctrineUtenteRepository implements UtenteRepositoryInterface /*questa classe astratta implementa l'interfaccia UtenteRepositoryInterface e fornisce un'implementazione di base per le operazioni di persistenza degli utenti utilizzando Doctrine ORM. 
                                                                                       Le sottoclassi concrete dovranno implementare il metodo getEntityClass() per specificare la classe concreta dell'entità Utente gestita dal repository.*/
{
    public function __construct(protected readonly EntityManagerInterface $em) {}

    /**
     * Restituisce il FQCN della sottoclasse concreta di Utente gestita
     * da questo repository (es. Cliente::class).
     */
    abstract protected function getEntityClass(): string; //restituisce il nome completo della classe dell'entità Utente gestita dal repository. Le sottoclassi concrete dovranno implementare questo metodo per specificare la classe concreta dell'entità Utente che gestiscono.

    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Utente
    {
        return $this->em->find($this->getEntityClass(), $id);
    }

    public function save(Utente $entity): void //prende l'entità Utente, non restituisce nulla, ma salva l'entità nel database.
    {
        $this->em->persist($entity); //L'EntityManager gestisce la persistenza dell'entità. persist() prepara l'entità per essere salvata nel database.
        $this->em->flush();
    }

    public function delete(Utente $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /** 
     * Restituisce un array di tutti gli Utenti presenti nel database. Utilizza il repository dell'entità Utente per eseguire la query findAll() e ottenere tutti i record corrispondenti.
     * @return Utente[] */
    public function findAll(): array
    {
        return $this->em 
            ->getRepository($this->getEntityClass())
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Lookup anagrafico : cerchi in base ad uno dei dati anagrafici
    // -------------------------------------------------------------------------

    /** 
     * Cerca un utente in base all'email.
    */
    public function findByEmail(string $email): ?Utente
    {
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from($this->getEntityClass(), 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** 
     * Prende l'email e verifica se esiste un utente con quell'email nel database.
    */
    public function existsByEmail(string $email): bool 
    {
        $count = (int) $this->em->createQueryBuilder() 
            ->select('COUNT(u.id)') //conta quanti sono gli utenti con quell'email
            ->from($this->getEntityClass(), 'u') 
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult(); //ti restituisce il numero di utenti con quell'email, come un singolo valore scalare.

        return $count > 0; //ritorna true se esiste almeno un utente con quell'email, altrimenti false.
    }

    /** 
     * Prende il codice fiscale e verifica se esiste un utente con quel codice fiscale nel database.
    */
    public function existsByCF(string $CF): bool
    {
        $count = (int) $this->em->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from($this->getEntityClass(), 'u')
            ->where('u.CF = :cf')
            ->setParameter('cf', $CF)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}