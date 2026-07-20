<?php
namespace App\Control;

use App\View\Interface\AbbonamentiView;
use App\View\AbbonamentiViewSmarty;
use App\View\VisualizzazioneViewSmarty;
use App\Foundation\Session;
use App\Foundation\Utility\HTTPMethods;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Entity\Repository\AbbonamentoRepositoryInterface;
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AmministratoreRepositoryInterface;
use App\Entity\Repository\AbbonamentoAttivoRepositoryInterface;
use App\Entity\Repository\IscrizioneRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\Foundation\Persistence\Repository\DoctrineAbbonamentoRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAmministratoreRepository;
use App\Foundation\Persistence\Repository\DoctrineAbbonamentoAttivoRepository;
use App\Foundation\Persistence\Repository\DoctrineIscrizioneRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AbbonamentoAttivo;
use App\Entity\Iscrizione;
use \App\Entity\Cliente;
use \App\Entity\AbbonamentoDurata;

class AbbonamentiController
{
    private AbbonamentiView $view;
    private PalestraRepositoryInterface $palestraRepo;  //richiama l'interfaccia e non direttamente i repository in foundation
    private AbbonamentoRepositoryInterface $abbonamentoRepo;
    private ClienteRepositoryInterface $clienteRepo;
    private AmministratoreRepositoryInterface $amministratoreRepo;
    private AbbonamentoAttivoRepositoryInterface $abbonamentoAttivoRepo;
    private IscrizioneRepositoryInterface $iscrizioneRepo;

    public function __construct(
        private EntityManagerInterface $entityManager,  //recupera la sessione: recupera tutto (entity manager e session) dal front controller
        private Session $session                    
    ) {
        $this->view = new AbbonamentiViewSmarty();   //instanzia l'ogetto AbbonamentiViewSmarty attraverso l'interfaccia AbbonamentiView  
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->abbonamentoRepo = new DoctrineAbbonamentoRepository($this->entityManager);
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->amministratoreRepo = new DoctrineAmministratoreRepository($this->entityManager);
        $this->abbonamentoAttivoRepo = new DoctrineAbbonamentoAttivoRepository($this->entityManager);
        $this->iscrizioneRepo = new DoctrineIscrizioneRepository($this->entityManager);
    }

    public function gestisciAbbonamento(): void         //permette di gestire l'abbonamento del cliente, la sua iscrizione ed eventaulmente inserire una nuova tipologia
    {
        $cliente = $this->verificaErecuperaCliente();
        if (!$cliente) {    //se non c'è il cliente il metodo termina
            return;
        }

        if (HTTPMethods::method() === 'POST') {    //se il metodo del server è post, è stata mandata una form e deve essere gestita l'azione del post
            $this->gestisciPostAzione($cliente);    //inserire nella form il cliente
            return;
        }

        $this->view->mostraGestioneAbbonamento([    //altrimenti mostra la gestione dell'abbonamento
            'cliente' => $cliente,
            'abbonamentoAttivo' => $cliente->getAbbonamento(),
            'iscrizione' => $cliente->getIscrizione(),
            'abbonamentiDisponibili' => $this->abbonamentoRepo->findAll()
        ]);
    }

    private function verificaErecuperaCliente(): ?Cliente           //verifica se il cliente è gestito da quella palestra e lo restituisce
    {
        $idAdmin = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAdmin || $ruolo !== 'amministratore') {  //se l'id non è stato definito o non appartiene ad un admin allora mostra errore    
            $this->mostraStatoOperazione(false, "Accesso negato. Questa operazione è riservata all'Amministratore.", "login", "Effettua il Login");
            return null;
        }
        $idCliente = HTTPMethods::get('id') ?? HTTPMethods::post('id_cliente');           //riprende l'id dall'URL, se non c'è lì lo prende dalla form, altrimenti è null
        $cliente = $idCliente ? $this->clienteRepo->findById((int)$idCliente) : null;       //Se abbiamo l'ID di un cliente, cercalo nel database tramite il suo Repository. Altrimenti è null
        if (!$cliente) {        //se è null
            $this->mostraStatoOperazione(false, "Cliente non trovato o non specificato.", "clienti", "Torna ai Clienti");
            return null;
        }
        $admin = $this->amministratoreRepo->findById($idAdmin);             //cerca l'admin nel repo tramite il suo id
        $palestra = $admin ? $this->palestraRepo->findByAmministratore($admin) : null;     //se l'amministratore esiste allora vai a trovare la palestra per admin, altrimenti è null
        if (!$palestra || !$cliente->getPalestra() || $cliente->getPalestra()->getId() !== $palestra->getId()) {   //se la palestra non esiste, oppure il cliente non è associato ad alcuna palestra, oppure il cliente è associato ad un altra palestra
            $this->mostraStatoOperazione(false, "Accesso negato. Il cliente non appartiene alla palestra gestita.", "dashboard-admin", "Torna alla Dashboard");
            return null;
        }
        return $cliente;
    }

    private function gestisciPostAzione(Cliente $cliente): void     //gestisce la form
    {
        $azione = HTTPMethods::post('azione', '');       //Se l'utente non ha cliccato nulla (è appena entrato sulla pagina), $azione diventa una stringa vuota
        if ($azione === 'abbonamento') {
            $this->sottoscriviAbbonamento($cliente);
        } elseif ($azione === 'crea_tipologia') {
            $this->creaTipologiaAbbonamento($cliente);
        } elseif ($azione === 'iscrizione') {
            $this->aggiornaIscrizioneCliente($cliente);
        } else {
            $this->mostraStatoOperazione(false, "Azione non riconosciuta.", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
        }
    }

    private function sottoscriviAbbonamento(Cliente $cliente): void     
    {
        $idPlan = HTTPMethods::post('abbonamento_id');     
        $plan = $idPlan ? $this->abbonamentoRepo->findById((int)$idPlan) : null;    
        if (!$plan) {   //se l'id non esiste mostra errore
            $this->mostraStatoOperazione(false, "Piano di abbonamento non valido.", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
            return;
        }
        $start = $this->parseDataInizio(HTTPMethods::post('data_inizio_abbonamento', ''), $cliente); //mette nella variabile start la data di inizio dell'abbonamento scritta in maniera corretta
        if (!$start) {
            return;
        }
        $this->rimuoviAbbonamentoEsistente($cliente);   // se sottoscrivi un nuovo abbonamneto rimuovi quello vecchio
        $newAbb = new AbbonamentoAttivo($start, $plan);
        $cliente->setAbbonamento($newAbb);          //viene associato al cliente un nuovo abbonamento
        $this->abbonamentoAttivoRepo->save($newAbb); //lo inserisce nel database
        $this->clienteRepo->save($cliente);         //le modifiche vengono confermate
        $this->mostraStatoOperazione(true, "Abbonamento registrato con successo!", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
    }

    private function parseDataInizio(string $dataStr, Cliente $cliente): ?\DateTimeImmutable
    {
        try {
            return $dataStr !== '' ? new \DateTimeImmutable($dataStr) : new \DateTimeImmutable();       //prova a trasformare questo testo in una data vera
        } catch (\Exception $e) {   //se l'utente ha inserito una data inventata o scritta male mostra un messaggio di errore e torna indietro
            $this->mostraStatoOperazione(false, "Formato data non valido.", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
            return null;
        }
    }

    private function rimuoviAbbonamentoEsistente(Cliente $cliente): void
    {
        $oldAbb = $cliente->getAbbonamento();    //viene recuperato il vecchio abbonamento
        if ($oldAbb !== null) {
            $cliente->setAbbonamento(null);
            $this->abbonamentoAttivoRepo->delete($oldAbb); //lo rimuove dal database
            $this->clienteRepo->save($cliente);           //conferma le modifiche
        }
    }

    private function creaTipologiaAbbonamento(Cliente $cliente): void
    {
        $tipologia = HTTPMethods::post('nuova_tipologia', '');       //se nella form clicco nuova tipologia
        $categoria = HTTPMethods::post('nuova_categoria', '');
        $durata = HTTPMethods::post('nuova_durata', '');
        if (empty($tipologia) || empty($categoria) || empty($durata)) {
            $this->mostraStatoOperazione(false, "Campi tipologia incompleti.", "gestione-abbonamento?id=" . $cliente->getId(), "Torna all'Abbonamento");
            return;
        }
        try {
            $nuovoPlan = new AbbonamentoDurata($tipologia, $categoria, (int)$durata);       //viene creato un nuovo abbonamento per durata
            $this->abbonamentoRepo->save($nuovoPlan);
            $this->view->redirect('gestione-abbonamento?id=' . $cliente->getId());
        } catch (\Exception $e) {
            $this->mostraStatoOperazione(false, "Errore creazione tipologia: " . $e->getMessage(), "gestione-abbonamento?id=" . $cliente->getId(), "Torna all'Abbonamento");
        }
    }

    private function aggiornaIscrizioneCliente(Cliente $cliente): void
    {
        $start = $this->parseDataInizio(HTTPMethods::post('data_inizio_iscrizione', ''), $cliente);
        if (!$start) {
            return;
        }
        try {
            $oldIscrizione = $cliente->getIscrizione();
            if ($oldIscrizione !== null) {          // se esisteva già un'iscrizione allora imposta una nuova data di inizio
                $oldIscrizione->setDataInizio($start);
                $this->iscrizioneRepo->save($oldIscrizione);
            } else {
                $nuovaIscrizione = new Iscrizione($start, $cliente);    //altrimenti creane una nuova e impostala sul database
                $this->iscrizioneRepo->save($nuovaIscrizione);
            }
            $this->clienteRepo->save($cliente);
            $this->mostraStatoOperazione(true, "Iscrizione annuale aggiornata con successo!", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
        } catch (\InvalidArgumentException $e) {
            $this->mostraStatoOperazione(false, "Errore di validazione: " . $e->getMessage(), "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
        }
    }

    private function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void
    {
        $statusView = new VisualizzazioneViewSmarty();
        $statusView->mostraStatoOperazione($successo, $messaggio, $ritorno, $testoBottone);
    }
}
