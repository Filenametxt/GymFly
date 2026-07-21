<?php
namespace App\Control;

use App\Entity\Scheda;
use App\Entity\Allenamento;
use App\Entity\DettaglioAllenamento;
use App\Entity\Cliente;
use App\Entity\Allenatore;
use App\Entity\Esercizio;
use App\Entity\Messaggio;
use App\Entity\ProgressoCarico;
use App\Entity\ProgressoRipetizioni;
use App\Entity\ProgressoDurata;
use App\Entity\Repository\SchedaRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\EsercizioRepositoryInterface;
use App\Entity\Repository\ProgressoRepositoryInterface;
use App\Entity\Repository\ProgressoCaricoRepositoryInterface;
use App\Entity\Repository\ProgressoRipetizioniRepositoryInterface;
use App\Entity\Repository\ProgressoDurataRepositoryInterface;
use App\Entity\Repository\MessaggioRepositoryInterface;
use App\Entity\Repository\AllenamentoRepositoryInterface;
use App\Entity\Repository\DettaglioAllenamentoRepositoryInterface;
use App\Entity\Repository\AmministratoreRepositoryInterface;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineSchedaRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineEsercizioRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoCaricoRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoRipetizioniRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoDurataRepository;
use App\Foundation\Persistence\Repository\DoctrineMessaggioRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenamentoRepository;
use App\Foundation\Persistence\Repository\DoctrineDettaglioAllenamentoRepository;
use App\Foundation\Persistence\Repository\DoctrineAmministratoreRepository;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\View\Interface\SchedaAllenamentoView;
use App\View\SchedaAllenamentoViewSmarty;
use App\Foundation\Session;
use App\Foundation\Utility\HTTPMethods;
use Doctrine\ORM\EntityManagerInterface;

class SchedaAllenamentoController
{
    private SchedaRepositoryInterface $schedaRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private ClienteRepositoryInterface $clienteRepo;
    private EsercizioRepositoryInterface $esercizioRepo;
    private ProgressoRepositoryInterface $progressoRepo;
    private ProgressoCaricoRepositoryInterface $progressoCaricoRepo;
    private ProgressoRipetizioniRepositoryInterface $progressoRipetizioniRepo;
    private ProgressoDurataRepositoryInterface $progressoDurataRepo;
    private MessaggioRepositoryInterface $messaggioRepo;
    private AllenamentoRepositoryInterface $allenamentoRepo;
    private DettaglioAllenamentoRepositoryInterface $dettaglioAllenamentoRepo;
    private AmministratoreRepositoryInterface $amministratoreRepo;
    private PalestraRepositoryInterface $palestraRepo;
    private SchedaAllenamentoView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->schedaRepo = new DoctrineSchedaRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->esercizioRepo = new DoctrineEsercizioRepository($this->entityManager);
        $this->progressoRepo = new DoctrineProgressoRepository($this->entityManager);
        $this->progressoCaricoRepo = new DoctrineProgressoCaricoRepository($this->entityManager);
        $this->progressoRipetizioniRepo = new DoctrineProgressoRipetizioniRepository($this->entityManager);
        $this->progressoDurataRepo = new DoctrineProgressoDurataRepository($this->entityManager);
        $this->messaggioRepo = new DoctrineMessaggioRepository($this->entityManager);
        $this->allenamentoRepo = new DoctrineAllenamentoRepository($this->entityManager);
        $this->dettaglioAllenamentoRepo = new DoctrineDettaglioAllenamentoRepository($this->entityManager);
        $this->amministratoreRepo = new DoctrineAmministratoreRepository($this->entityManager);
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->view = new SchedaAllenamentoViewSmarty();
    }

    // =========================================================================
    // 1. RICHIESTA SCHEDA AD ALLENATORE (/richiedi-scheda)
    // =========================================================================

    public function apriFormRichiestaScheda(): void     //gestisce la richiesta di apertura del form per richiedere una scheda ad un allenatore, verificando i permessi dell'utente loggato e mostrando il form con gli allenatori disponibili
    {
        $id = $this->session->getLoggedUserId();
        if (!$id || $this->session->getLoggedUserRole() !== 'cliente') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $cliente = $this->clienteRepo->findById($id);
        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato.", "login");
            return;
        }
        if ($this->schedaRepo->findRichiestaByCliente($cliente)) {             // Controlla se esiste già una richiesta per il cliente  
            $this->view->mostraStatoOperazione(false, "Hai già effettuato una richiesta.", "dashboard-cliente");
            return;
        }
        $this->gestisciAzioneRichiesta($cliente);
    }

    private function gestisciAzioneRichiesta(Cliente $cliente): void
    {
        if (HTTPMethods::method() === 'GET') {
            $pal = $cliente->getPalestra();
            $this->view->mostraTemplate('richiedi_scheda.tpl', [
                'utente' => $cliente, 'allenatori' => $pal ? $this->allenatoreRepo->findByPalestra($pal) : []
            ]);
            return;
        }
        $this->richiestaSchedaAllenatore(
            trim(HTTPMethods::post('obiettivo', '')),
            HTTPMethods::post('n_allenamenti') !== null ? (int)HTTPMethods::post('n_allenamenti') : 3,
            trim(HTTPMethods::post('cf_allenatore', ''))
        );
    }

    public function richiestaSchedaAllenatore(string $obiettivo, int $nAllenamenti, string $cfAllenatore): void    //gestisce la richiesta di una scheda ad un allenatore, verificando i permessi dell'utente loggato, i dati del form e creando la richiesta di scheda con gli allenamenti specificati
    {
        $cliente = $this->clienteRepo->findById($this->session->getLoggedUserId());
        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato.", "login");
            return;
        }
        if ($obiettivo === '' || $cfAllenatore === '') {
            $this->view->mostraStatoOperazione(false, "Campi obbligatori mancanti.", "richiedi-scheda");
            return;
        }
        $allenatore = $this->allenatoreRepo->findByCF($cfAllenatore);
        if (!$allenatore || $nAllenamenti < 1 || $nAllenamenti > 7) {
            $this->view->mostraStatoOperazione(false, "Dati modulo non validi.", "richiedi-scheda");
            return;
        }
        $this->eliminaVecchieSchede($cliente);
        $this->creaRichiestaSchedaConAllenamenti($cliente, $allenatore, $obiettivo, $nAllenamenti);
    }

    private function eliminaVecchieSchede(Cliente $cliente): void
    {
        $vs = $this->schedaRepo->findByCliente($cliente);
        if ($vs) {
            $cliente->setScheda(null);
            $this->clienteRepo->save($cliente);
            $this->schedaRepo->delete($vs);
        }
    }

    private function creaRichiestaSchedaConAllenamenti(Cliente $cli, Allenatore $all, string $ob, int $num): void      //gestisce la creazione di una nuova richiesta di scheda con un numero specificato di allenamenti, associata a un cliente e a un allenatore, e salva la richiesta nel repository
    {
        try {
            $scheda = new Scheda("Richiesta Nuova Scheda", new \DateTimeImmutable('today'), new \DateTimeImmutable('+1 month'), $ob, $cli, $all);
            $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];   // Array di lettere per nominare gli allenamenti
            for ($i = 0; $i < $num; $i++) {
                $scheda->addAllenamento(new Allenamento("Allenamento " . $letters[$i], "Informazioni " . $letters[$i]));
            }
            $this->schedaRepo->save($scheda);
            $this->view->mostraStatoOperazione(true, "Richiesta inviata con successo. Vecchia scheda rimossa.", "dashboard-cliente");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), "richiedi-scheda");
        }
    }

    // =========================================================================
    // 2. CREAZIONE DELLA SCHEDA (/crea-scheda)
    // =========================================================================

    public function apriFormCreazioneScheda(): void       //gestisce la richiesta di apertura del form per creare una nuova scheda
    {
        $idAllenatore = $this->session->getLoggedUserId();
        if (!$idAllenatore || $this->session->getLoggedUserRole() !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $cf = HTTPMethods::request('cf');
        if ($cf !== null) {
            $this->selezionaUtentePerScheda(trim($cf));
            return;
        }
        $this->view->redirect("clienti");
    }

    public function selezionaUtentePerScheda(string $cf): void
    {
        $all = $this->allenatoreRepo->findById($this->session->getLoggedUserId());
        $cli = $this->clienteRepo->findByCF($cf);
        if (!$cli || ($cli->getPalestra() && $cli->getPalestra()->getId() !== $all->getPalestra()->getId())) {
            $this->view->mostraStatoOperazione(false, "Cliente non valido.", "dashboard-allenatore");
            return;
        }
        $scheda = $this->schedaRepo->findRichiestaByCliente($cli);
        if ($scheda && $scheda->getAllenatore()->getId() !== $all->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. La richiesta di scheda di questo cliente è indirizzata a un altro allenatore.", "clienti", "Torna a Gestione Clienti");
            return;
        }
        if (!$scheda) {                            // Se non esiste una richiesta, crea una nuova scheda con allenamenti predefiniti
            $this->eliminaVecchieSchede($cli);
            $scheda = new Scheda("Nuovo Protocollo", new \DateTimeImmutable('today'), new \DateTimeImmutable('+1 month'), "Inserisci obiettivo", $cli, $all);
            foreach (['A', 'B', 'C'] as $l) 
                $scheda->addAllenamento(new Allenamento("Allenamento " . $l, "Informazioni " . $l));
        } else {
            $scheda->setNome_scheda("Nuovo Protocollo");
        }
        $this->schedaRepo->save($scheda);
        $azioneRapidaPart = HTTPMethods::request('azione_rapida') !== null ? "&azione_rapida=1" : "";
        $this->view->redirect("modifica-scheda?id=" . $scheda->getId() . $azioneRapidaPart);
    }

    // =========================================================================
    // 3. MODIFICA SCHEDA (/modifica-scheda)
    // =========================================================================

    public function apriFormModificaScheda(): void
    {
        $idLog = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idLog || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $idScheda = HTTPMethods::get('id') ? (int)HTTPMethods::get('id') : (int)HTTPMethods::post('id_scheda', 0);
        $scheda = $this->schedaRepo->findById($idScheda);
        if (!$scheda || $scheda->getAllenatore()->getId() !== $idLog) {
            $this->view->mostraStatoOperazione(false, "Scheda non trovata o accesso negato.", "dashboard-allenatore");
            return;
        }
        if (HTTPMethods::method() === 'POST') {
            $this->salvaModificaScheda($scheda);
            return;
        }
        $copiaDa = HTTPMethods::get('copia_da');
        if ($copiaDa !== null) {
            $this->eseguiClonazioneScheda($scheda, (int)$copiaDa);
            return;
        }
        $this->view->mostraTemplate('gestione_scheda.tpl', [
            'scheda' => $scheda,
            'esercizi' => $this->esercizioRepo->findAll(),
            'eserciziDisponibili' => $this->esercizioRepo->findAll(),
            'azione_rapida' => HTTPMethods::get('azione_rapida') !== null ? 1 : 0,
            'schedeClonabili' => $this->schedaRepo->findByAllenatore($scheda->getAllenatore())
        ]);
    }

    private function eseguiClonazioneScheda(Scheda $scheda, int $idSorgente): void
    {
        $sorgente = $this->schedaRepo->findById($idSorgente);
        if ($sorgente) {
            foreach ($scheda->getAllenamenti() as $all) {
                $scheda->removeAllenamento($all);
            }
            $this->schedaRepo->save($scheda);
            foreach ($sorgente->getAllenamenti() as $srcAll) {
                $nuovoAll = new Allenamento($srcAll->getNome(), $srcAll->getDescrizione());
                $scheda->addAllenamento($nuovoAll);
                /** @var DettaglioAllenamento $srcDet */
                foreach ($srcAll->getDettagli() as $srcDet) {
                    $nuovoAll->addDettaglio(new DettaglioAllenamento(
                        $srcDet->getEsercizio(), $nuovoAll, $srcDet->getSerie(),
                        $srcDet->getRipetizioni(), $srcDet->getCarico(), $srcDet->getTempo()
                    ));
                }
            }
            $this->schedaRepo->save($scheda);
        }
    }

    private function salvaModificaScheda(Scheda $scheda): void
    {
        $idScheda = (int)HTTPMethods::post('id_scheda', 0);
        if ($idScheda <= 0 || $scheda->getId() !== $idScheda) {
            $this->view->mostraStatoOperazione(false, "ID scheda non valido.", "dashboard-allenatore");
            return;
        }

        $nome = trim(HTTPMethods::post('nome_scheda', ''));
        $ini = HTTPMethods::post('data_inizio', '');
        $fine = HTTPMethods::post('data_fine', '');
        if ($nome === '' || $ini === '' || $fine === '') {
            $azioneRapidaPart = HTTPMethods::request('azione_rapida') !== null ? "&azione_rapida=1" : "";
            $this->view->mostraStatoOperazione(false, "Campi dati scheda incompleti.", "modifica-scheda?id=" . $scheda->getId() . $azioneRapidaPart);
            return;
        }
        try {
            $scheda->setNome_scheda($nome);
            $scheda->setData_inizio(new \DateTimeImmutable($ini));
            $scheda->setData_fine(new \DateTimeImmutable($fine));
            $scheda->setObiettivo(trim(HTTPMethods::post('obiettivo', '')));
            foreach ($scheda->getAllenamenti() as $all) 
                $scheda->removeAllenamento($all);
            $this->schedaRepo->save($scheda);
            $this->salvaWorkoutData($scheda, HTTPMethods::postArray('workouts'));
            $this->schedaRepo->save($scheda);
            $this->inviaScheda($scheda->getId());
        } catch (\Throwable $e) {
            $azioneRapidaPart = HTTPMethods::request('azione_rapida') !== null ? "&azione_rapida=1" : "";
            $this->view->mostraStatoOperazione(false, "Errore salvataggio scheda: " . $e->getMessage(), "modifica-scheda?id=" . $scheda->getId() . $azioneRapidaPart);
        }
    }

    private function salvaWorkoutData(Scheda $scheda, array $workoutsData): void
    {
        foreach ($workoutsData as $wData) {
            $all = new Allenamento(trim($wData['nome'] ?? 'Allenamento'), trim($wData['descrizione'] ?? ''));
            $scheda->addAllenamento($all);
            foreach ($wData['dettagli'] ?? [] as $dData) {
                $es = $this->esercizioRepo->findById((int)($dData['esercizio_id'] ?? 0));
                if ($es) {
                    $reps = isset($dData['ripetizioni']) && $dData['ripetizioni'] !== '' ? (int)$dData['ripetizioni'] : null;
                    $temp = isset($dData['tempo']) && trim($dData['tempo']) !== '' ? trim($dData['tempo']) : null;
                    $all->addDettaglio(new DettaglioAllenamento($es, $all, (int)($dData['serie'] ?? 1), $reps, (float)($dData['carico'] ?? 0.0), $temp));
                }
            }
        }
    }

    // =========================================================================
    // 4. INVIA SCHEDA (/invia-scheda)
    // =========================================================================

    public function inviaScheda(int $idScheda): void
    {
        $scheda = $this->schedaRepo->findById($idScheda);
        if ($scheda) {
            $scheda->getCliente()->setScheda($scheda);
            $this->clienteRepo->save($scheda->getCliente());
            $this->schedaRepo->save($scheda);
            
            $msg = new Messaggio($scheda->getAllenatore(), "Nuova Scheda di Allenamento", "Ciao " . $scheda->getCliente()->getNome() . ", ho realizzato la tua nuova scheda: '" . $scheda->getNome_scheda() . "'. Puoi consultarla nella sezione dedicata.");
            $msg->aggiungiDestinatario($scheda->getCliente());
            $this->messaggioRepo->save($msg);
            $this->view->mostraStatoOperazione(true, "Scheda salvata e inviata al cliente con successo.", "clienti", "Torna a Gestione Clienti");
        }
    }

    // =========================================================================
    // 5. ELIMINA SCHEDA / RIMUOVI SCHEDA (/elimina-scheda, /rimuovi-scheda)
    // =========================================================================

    public function eliminaScheda(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || ($ruolo !== 'allenatore')) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $scheda = $this->schedaRepo->findById(HTTPMethods::get('id') ? (int)HTTPMethods::get('id') : 0);
        if (!$scheda) {
            $this->view->mostraStatoOperazione(false, "Scheda non trovata.", "dashboard-" . $ruolo);
            return;
        }
        $all = $this->allenatoreRepo->findById($idUt);
        if ($scheda->getAllenatore()->getId() !== $all->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "dashboard-allenatore");
            return;
        }
        $this->eseguiRimozioneScheda($scheda, $ruolo);
    }

    private function eseguiRimozioneScheda(Scheda $scheda, string $ruolo): void
    {
        try {
            $cliente = $scheda->getCliente();
            $cliente->setScheda(null);
            $this->clienteRepo->save($cliente);
            $this->schedaRepo->delete($scheda);
            $this->view->mostraStatoOperazione(true, "Scheda rimossa con successo.", "dashboard-" . $ruolo);
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), "dashboard-" . $ruolo);
        }
    }

    // =========================================================================
    // 6. VISUALIZZA SCHEDA DA PARTE DEL CLIENTE (/visualizza-scheda)
    // =========================================================================

    public function visualizzaScheda(): void
    {
        $idLog = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idLog) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }

        $idScheda = (int)HTTPMethods::request('id', HTTPMethods::request('id_scheda', 0));
        $scheda = null;
        if ($idScheda > 0) {
            $scheda = $this->schedaRepo->findById($idScheda);
        }
        if (!$scheda && $ruolo === 'cliente') {
            $cliente = $this->clienteRepo->findById($idLog);
            if ($cliente) {
                $scheda = $this->schedaRepo->findAttivaByCliente($cliente) ?? $this->schedaRepo->findByCliente($cliente) ?? $cliente->getScheda();
            }
        }

        if (!$scheda || !$this->validaAccessoScheda($scheda, $idLog, $ruolo)) {
            $this->view->mostraStatoOperazione(false, "Scheda non trovata o accesso negato.");
            return;
        }

        if ($ruolo === 'cliente' && str_starts_with(strtolower($scheda->getNome_scheda()), 'richiesta')) {
            $nomeAllenatore = $scheda->getAllenatore() ? $scheda->getAllenatore()->getNome() . ' ' . $scheda->getAllenatore()->getCognome() : 'allenatore';
            $this->view->mostraStatoOperazione(
                false,
                "La scheda non è ancora disponibile. La tua richiesta è in attesa di essere compilata e inviata dal tuo allenatore (" . $nomeAllenatore . ").",
                "dashboard-cliente",
                "Torna alla Dashboard"
            );
            return;
        }

        $this->view->mostraTemplate('visualizza_scheda.tpl', [
            'scheda' => $scheda,
            'ruolo_utente' => $ruolo,
            'isSelf' => ($ruolo === 'cliente' && $scheda->getCliente() && $scheda->getCliente()->getId() === $idLog)
        ]);
    }

    private function validaAccessoScheda(Scheda $scheda, int $idLog, string $ruolo): bool
    {
        if ($ruolo === 'cliente') {
            return $scheda->getCliente() && $scheda->getCliente()->getId() === $idLog;
        }
        if ($ruolo === 'allenatore') {
            $trainer = $this->allenatoreRepo->findById($idLog);
            $palTrainer = $trainer ? $trainer->getPalestra() : null;
            $palScheda = $scheda->getCliente() ? $scheda->getCliente()->getPalestra() : null;
            return ($scheda->getAllenatore() && $scheda->getAllenatore()->getId() === $idLog) ||
                   ($palTrainer && $palScheda && $palTrainer->getId() === $palScheda->getId());
        }
        if ($ruolo === 'amministratore') {
            $admin = $this->amministratoreRepo->findById($idLog);
            $palAdmin = $admin ? $this->palestraRepo->findByAmministratore($admin) : null;
            $palScheda = $scheda->getCliente() ? $scheda->getCliente()->getPalestra() : null;
            return $palAdmin && $palScheda && $palAdmin->getId() === $palScheda->getId();
        }
        return false;
    }

    // =========================================================================
    // 7. MODIFICA DETTAGLI SCHEDA DA PARTE DEL CLIENTE (/modifica-dettagli)
    // =========================================================================

    public function apriFormModificaSchedaCliente(): void
    {
        $idLog = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idLog || $ruolo !== 'cliente') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $all = $this->allenamentoRepo->findById(HTTPMethods::request('id_allenamento') ? (int)HTTPMethods::request('id_allenamento') : 0);
        if (!$all || $all->getScheda()->getCliente()->getId() !== $idLog) {
            $this->view->mostraStatoOperazione(false, "Allenamento non trovato o non autorizzato.");
            return;
        }
        if (HTTPMethods::method() === 'GET') {
            $this->view->mostraTemplate('modifica_dettagli.tpl', ['allenamento' => $all]);
            return;
        }
        $this->eseguiSalvataggioDettagliCliente($all, HTTPMethods::postArray('dettagli'));
    }

    private function eseguiSalvataggioDettagliCliente(Allenamento $all, array $dettagliModificati): void
    {
        $scheda = $all->getScheda();
        $cliente = $scheda->getCliente();
        $oggi = new \DateTimeImmutable('today');
        foreach ($dettagliModificati as $idDet => $data) {
            $dettaglio = $this->dettaglioAllenamentoRepo->findById((int)$idDet);
            if ($dettaglio && $dettaglio->getAllenamento()->getScheda()->getId() === $scheda->getId()) {
                $oldReps = $dettaglio->getRipetizioni(); $oldCarico = $dettaglio->getCarico(); $oldTempo = $dettaglio->getTempo();
                $reps = isset($data['ripetizioni']) && $data['ripetizioni'] !== '' ? (int)$data['ripetizioni'] : null;
                $carico = isset($data['carico']) ? (float)$data['carico'] : 0.0;
                $tempo = isset($data['tempo']) && trim($data['tempo']) !== '' ? trim($data['tempo']) : null;
                $dettaglio->setRipetizioni($reps);
                if ($carico >= 0) $dettaglio->setCarico($carico);
                $dettaglio->setTempo($tempo);
                $this->registraProgressiCliente($cliente, $dettaglio, $oggi, $carico, $oldCarico, $reps, $oldReps, $tempo, $oldTempo);
            }
        }
        $this->schedaRepo->save($scheda);
        $this->view->mostraStatoOperazione(true, "Dettagli aggiornati con successo.", "visualizza-scheda", "Torna alla Scheda");
    }

    private function registraProgressiCliente(Cliente $cli, DettaglioAllenamento $det, \DateTimeImmutable $oggi, float $carico, float $oldCarico, ?int $reps, ?int $oldReps, ?string $tempo, ?string $oldTempo): void
    {
        if ($carico !== $oldCarico && $carico > 0) {
            $this->progressoCaricoRepo->save(new ProgressoCarico($oggi, $cli, $det->getEsercizio(), $carico));
        }
        if ($reps !== $oldReps && $reps !== null && $reps > 0) {
            $this->progressoRipetizioniRepo->save(new ProgressoRipetizioni($oggi, $cli, $det->getEsercizio(), (float)$reps));
        }
        if ($tempo !== $oldTempo && $tempo !== null && (float)$tempo > 0) {
            $this->progressoDurataRepo->save(new ProgressoDurata($oggi, $cli, $det->getEsercizio(), (float)$tempo));
        }
    }

    // =========================================================================
    // 8. VISUALIZZA PROGRESSI CLIENTE (/progressi-cliente)
    // =========================================================================

    public function visualizzaProgressiCliente(): void    //gestisce la richiesta di visualizzazione dei progressi del cliente, verificando i permessi dell'utente loggato e mostrando i progressi della scheda di allenamento associata al cliente
    {
        $idUt = $this->session->getLoggedUserId();
        $all = ($idUt && $this->session->getLoggedUserRole() === 'allenatore') ? $this->allenatoreRepo->findById($idUt) : null;
        if (!$all) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $cli = $this->clienteRepo->findById(HTTPMethods::get('id_cliente') ? (int)HTTPMethods::get('id_cliente') : 0);
        if (!$cli ||  $cli->getPalestra()->getId() !== $all->getPalestra()->getId()) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato", "clienti");
            return;
        }
        $scheda = $cli->getScheda();
        if (!$scheda) {
            $this->view->mostraStatoOperazione(false, "Nessuna scheda di allenamento associata.", "clienti");
            return;
        }
        $this->view->mostraTemplate('progressi_cliente.tpl', [
            'utente' => $all, 'cliente' => $cli, 'scheda' => $scheda, 'workouts' => $this->caricaWorkoutsProgressi($cli, $scheda)
        ]);
    }

    private function caricaWorkoutsProgressi(Cliente $cli, Scheda $scheda): array
    {
        $workoutsData = [];
        foreach ($scheda->getAllenamenti() as $allenamento) {
            $eserciziData = [];
            $eserciziVisti = [];
            /** @var DettaglioAllenamento $dettaglio */
            foreach ($allenamento->getDettagli() as $dettaglio) {
                $es = $dettaglio->getEsercizio();
                $esId = $es->getId();
                if (in_array($esId, $eserciziVisti)) {
                    foreach ($eserciziData as &$eData) {
                        if ($eData['esercizio']->getId() === $esId) {
                            if ($dettaglio->getSerie() > $eData['serie_max']) {
                                $eData['serie_max'] = $dettaglio->getSerie();
                            }
                        }
                    }
                    unset($eData);
                    continue;
                }
                $eserciziVisti[] = $esId;
                $progressi = array_merge(
                    $this->progressoCaricoRepo->findByClienteAndEsercizio($cli, $es),
                    $this->progressoRipetizioniRepo->findByClienteAndEsercizio($cli, $es),
                    $this->progressoDurataRepo->findByClienteAndEsercizio($cli, $es)
                );
                $d = $this->elaboraProgressiGrafici($progressi);
                $eserciziData[] = [
                    'dettaglio' => $dettaglio, 'esercizio' => $es,
                    'carico' => $d['carico'],
                    'reps' => $d['reps'],
                    'durata' => $d['durata'],
                    'storico' => $d['storico'], 
                    'hasCarico' => count($d['carico']) > 0,
                    'hasReps' => count($d['reps']) > 0, 
                    'hasDurata' => count($d['durata']) > 0,
                    'serie_max' => $dettaglio->getSerie()
                ];
            }
            $workoutsData[] = ['allenamento' => $allenamento, 'esercizi' => $eserciziData];
        }
        return $workoutsData;
    }

    private function elaboraProgressiGrafici(array $progressi): array
    {
        $carico = []; $reps = []; $durata = []; $storico = [];
        foreach ($progressi as $p) {
            $dStr = $p->getData()->format('d/m/Y'); $dGraf = $p->getData()->format('d/m');              // Formato per il grafico (giorno/mese)
            if ($p instanceof ProgressoCarico) {
                $carico[] = ['data' => $dGraf, 'valore' => $val = $p->getNuovoCarico()];
                $storico[] = ['data' => $dStr, 'tipo' => 'Carico', 'valore' => $val . ' Kg'];
            } elseif ($p instanceof ProgressoRipetizioni) {
                $reps[] = ['data' => $dGraf, 'valore' => $val = $p->getNuovoNRipetizioni()];
                $storico[] = ['data' => $dStr, 'tipo' => 'Ripetizioni', 'valore' => $val . ' rip.'];
            } elseif ($p instanceof ProgressoDurata) {
                $durata[] = ['data' => $dGraf, 'valore' => $val = $p->getNuovaDurata()];
                $storico[] = ['data' => $dStr, 'tipo' => 'Durata', 'valore' => $val . ' sec'];
            }
        }
        usort($storico, fn($a, $b) => strcmp($b['data'], $a['data']));
        return ['carico' => $carico, 'reps' => $reps, 'durata' => $durata, 'storico' => $storico];
    }

}
