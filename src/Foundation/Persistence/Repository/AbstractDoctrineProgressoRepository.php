<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\Cliente;
use App\Entity\Esercizio;
use App\Entity\Progresso;
use App\Entity\Repository\ProgressoRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractDoctrineProgressoRepository implements ProgressoRepositoryInterface
{
    public function __construct(protected readonly EntityManagerInterface $em) {} //il costruttore accetta un'istanza di EntityManagerInterface, che viene utilizzata per interagire con il database tramite Doctrine ORM. L'EntityManager è responsabile della gestione delle entità e delle operazioni di persistenza. Protected readonly indica che la proprietà $em è di sola lettura e non può essere modificata dopo l'inizializzazione.
    //protected/public/private creano automaticamente l'attributo e lo assegna ad $em. Grazie ad $em possiamo inetrfacciarci con gli altri oggetti di Doctrine

    /**
     * Restituisce il FQCN della sottoclasse concreta di Progresso gestita //FQCN = Fully Qualified Class Name ed è il percorso completo della classe, incluso il namespace. In questo caso, indica la classe concreta di Progresso che viene gestita
     * da questo repository (es. ProgressoCarico::class).
     */
    abstract protected function getEntityClass(): string; //restituisce il nome completo della classe dell'entità Progresso gestita dal repository. 
    // -------------------------------------------------------------------------
    // CRUD base //Create Read Update Delete
    // -------------------------------------------------------------------------

    public function findById(int $id): ?Progresso //prende l'ID, applica find e ritorna l'entità Progresso corrispondente o null se non esiste. 
    {
        return $this->em->find($this->getEntityClass(), $id);
    }

    public function delete(Progresso $entity): void //prende il progresso, non restituisce nulla, ma rimuove l'entità dal database. 
    {
        $this->em->remove($entity); //L'EntityManager gestisce la rimozione
        $this->em->flush(); //L'EntityManager gestisce il flush per applicare le modifiche al database. Il flush() è necessario per rendere effettive le modifiche nel database.
    }

    /** 
     * Restituisce un array di tutti i progressi presenti nel database. Utilizza il repository dell'entità Progresso per eseguire la query findAll() e ottenere tutti i record corrispondenti.
     * @return Progresso[] */ 
    public function findAll(): array 
    {
        return $this->em //EntityManager gestisce l'interazione con il database. Viene utilizzato per ottenere il repository dell'entità Progresso e chiamare il metodo findAll() per recuperare tutti i record corrispondenti.
            ->getRepository($this->getEntityClass()) //prende il repository (elemento che recupera gli elementi dalla tabella) corrispondente all'entità Progresso e restituisce il repository associato.
            ->findAll();
    }

    // -------------------------------------------------------------------------
    // Query per cliente
    // -------------------------------------------------------------------------

    /** 
     * Trova tutti i progressi relativi ad un cliente, restituendo un array
     *  @return Progresso[] */
    public function findByCliente(Cliente $cliente): array 
    {
        return $this->em->createQueryBuilder() //ci ridà un oggetto QueryBuilder che ci permette di definire le query di Doctrine. Viene utilizzato per creare una query che seleziona tutti i progressi associati a un determinato cliente.
            ->select('p') //'p' è la tabella Progresso
            ->from($this->getEntityClass(), 'p') //specifica la tabella da cui selezionare i dati
            ->where('p.cliente = :cliente')
            ->setParameter('cliente', $cliente) //imposta il parametro 'cliente' con il valore del cliente passato
            ->orderBy('p.data', 'DESC') //ordina i risultati in base alla data in ordine decrescente
            ->getQuery() //ottiene la query risultante
            ->getResult();
    }

    /** 
     * Trova tutti i progressi relativi ad un cliente e ad un esercizio, restituendo un array
     * @return Progresso[] */
    public function findByClienteAndEsercizio(Cliente $cliente, Esercizio $esercizio): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->where('p.cliente = :cliente')
            ->andWhere('p.esercizio = :esercizio') //Join = è la chiave estera del progresso che punta all'esercizio
            ->setParameter('cliente', $cliente)
            ->setParameter('esercizio', $esercizio)
            ->orderBy('p.data', 'ASC') //ordina i risultati in base alla data in ordine crescente
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Query per esercizio
    // -------------------------------------------------------------------------

    /** 
     * Trova tutti i progressi relativi ad un esercizio, restituendo un array
     * @return Progresso[] */
    public function findByEsercizio(Esercizio $esercizio): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->where('p.esercizio = :esercizio') 
            ->setParameter('esercizio', $esercizio)
            ->orderBy('p.data', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Query per intervallo di date
    // -------------------------------------------------------------------------

    /** 
     * Trova tutti i progressi relativi a un cliente in un intervallo di date, restituendo un array
     * @return Progresso[] */
    public function findByClienteInPeriodo(
        Cliente $cliente,
        \DateTimeImmutable $dal,
        \DateTimeImmutable $al
    ): array {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->where('p.cliente = :cliente')
            ->andWhere('p.data >= :dal')
            ->andWhere('p.data <= :al')
            ->setParameter('cliente', $cliente)
            ->setParameter('dal', $dal)
            ->setParameter('al', $al)
            ->orderBy('p.data', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // -------------------------------------------------------------------------
    // Ultimo progresso registrato
    // -------------------------------------------------------------------------
    
    /** 
     * Trova l'ultimo progresso registrato per un cliente e un esercizio, restituendo un singolo oggetto Progresso o null se non esiste
     * @return Progresso */
    public function findUltimoByClienteAndEsercizio(
        Cliente $cliente,
        Esercizio $esercizio
    ): ?Progresso {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from($this->getEntityClass(), 'p')
            ->where('p.cliente = :cliente')
            ->andWhere('p.esercizio = :esercizio')
            ->setParameter('cliente', $cliente)
            ->setParameter('esercizio', $esercizio)
            ->orderBy('p.data', 'DESC')
            ->setMaxResults(1) //prende al massimo 1 risultato: il più recente, dato che è ordinato in ordine decrescente per data
            ->getQuery()
            ->getOneOrNullResult();
    }
}