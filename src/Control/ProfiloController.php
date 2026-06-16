<?php
namespace App\Control;

// Repository e DTO rimangono puliti
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\ParametriRepositoryInterface;
use App\Entity\Repository\CertificatoMedicoRepositoryInterface;
use App\Control\DTO\ModificaAnagraficaDTO;
use App\Control\DTO\CaricaFotoProfiloDTO;
use App\Control\DTO\AggiornaMisureDTO;
use App\Control\DTO\CaricaCertificatoUtenteDTO;

// Entità ed Enum
use App\Enum\Sesso;
use App\Entity\Cliente;
use App\Entity\Parametri;
use App\Entity\CertificatoMedico;

class ProfiloController 
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepository,
        private ParametriRepositoryInterface $parametriRepository,
        private CertificatoMedicoRepositoryInterface $certificatoRepository
    ) {}

    public function modificaAnagrafica(int $idClienteLoggato, ModificaAnagraficaDTO $dto): bool 
    {
        // 1. Il repository recupera l'entità Cliente dal DB usando l'ID
        $cliente = $this->clienteRepository->findById($idClienteLoggato);

        // 2. Aggiorniamo i campi ereditati dal padre (Utente)
        $cliente->setNome($dto->nome);
        $cliente->setCognome($dto->cognome);
        $cliente->setEmail($dto->email);
        $cliente->setCF($dto->CF);
        $cliente->setIndirizzo($dto->indirizzo);
        $cliente->setTelefono($dto->telefono);
        
        // Convertiamo la stringa del DTO nell'Enum Sesso richiesto dall'architettura
        $cliente->setSesso(Sesso::from($dto->sesso)); 

        // 3. Aggiorniamo i campi specifici del figlio (Cliente)
        $cliente->setLuogoDiNascita($dto->luogoDiNascita);
        $cliente->setIndirizzoDiDomicilio($dto->indirizzoDiDomicilio);
        $cliente->setMetodoDiPagamento($dto->metodoDiPagamento);
        
        // Qui avviene la magia del DateTimeImmutable partendo dalla stringa del DTO!
        $cliente->setDataDiNascita(new \DateTimeImmutable($dto->dataDiNascita));

        // 4. Salvi l'entità aggiornata nel Database
        $this->clienteRepository->save($cliente);

        return true;
    }

    public function caricaFotoProfilo(int $idClienteLoggato, CaricaFotoProfiloDTO $dto): bool 
    {
        //TODO: Gestione del file per la foto
        
        /*
        $cliente = $this->clienteRepository->findById($idClienteLoggato);
        if (!$cliente) {
            return false;
        }
        $cliente->setFotoProfilo($dto->percorsoFileFoto);

        $this->clienteRepository->save($cliente);
        */
        return true;
    }

    /**
     * Registra lo storico delle nuove misure corporee
     */
    public function aggiornaMisureCorporee(int $idClienteLoggato, AggiornaMisureDTO $dto): bool 
    {
        // 1. Abbiamo bisogno dell'oggetto Cliente perché il costruttore di Parametro lo richiede
        $cliente = $this->clienteRepository->findById($idClienteLoggato);
        if (!$cliente) {
            return false; // Cliente non trovato
        }

        // 2. Creiamo l'Entity Parametro passando TUTTI i dati del DTO e l'oggetto $cliente
        $nuovoParametro = new Parametri(
            peso: $dto->peso,
            altezza: $dto->altezza,
            data: new \DateTimeImmutable(), // Data odierna automatica
            cliente: $cliente,               // L'oggetto Entity recuperato dal DB
            bicipiteDestro: $dto->bicipiteDestro,
            bicipiteSinistro: $dto->bicipiteSinistro,
            tricipiteDestro: $dto->tricipiteDestro,
            tricipiteSinistro: $dto->tricipiteSinistro,
            cosciaDestra: $dto->cosciaDestra,
            cosciaSinistra: $dto->cosciaSinistra,
            polpaccioDestro: $dto->polpaccioDestro,
            polpaccioSinistro: $dto->polpaccioSinistro,
            misuraPetto: $dto->misuraPetto,
            misuraVita: $dto->misuraVita,
            misuraSpalle: $dto->misuraSpalle,
            misuraFianchi: $dto->misuraFianchi
        );

        // 3. Salva l'entità nel DB tramite il repository
        // NOTE: visto che l'oggetto Parametro ha già il cliente dentro, 
        // al repository basta ricevere solo l'oggetto da salvare!
        $this->parametriRepository->salvaMisure($nuovoParametro);
        return true;
    }

    public function caricaCertificato(int $idClienteLoggato, CaricaCertificatoUtenteDTO $dto): bool 
    {
        //TODO:gestione del file per il certificato
        /*
        // Istanzia l'entità pura "CertificatoMedico"
        $certificato = new CertificatoMedico(
            percorsoFile: $dto->percorsoFileCertificato,
            dataScadenza: new \DateTimeImmutable($dto->dataScadenza),
            stato: "IN_ATTESA" // o la vostra gestione delle stringhe/enum
        );

        $this->certificatoRepository->salvaCertificato($idClienteLoggato, $certificato);
        */
        return true;
    }
}