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
use App\Foundation\Persistence\Repository\DoctrineSchedaRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineEsercizioRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoCaricoRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoRipetizioniRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoDurataRepository;
use App\Foundation\Persistence\Repository\DoctrineMessaggioRepository;
use App\View\Interface\SchedaAllenamentoView;
use App\View\SchedaAllenamentoViewSmarty;
use App\Foundation\Session;
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
        $this->view = new SchedaAllenamentoViewSmarty();
    }

    // =========================================================================
    // 1. RICHIESTA SCHEDA AD ALLENATORE (/richiedi-scheda)
    // =========================================================================

    public function apriFormRichiestaScheda(): void
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
        if ($this->schedaRepo->findPendenteByCliente($cliente)) {
            $this->view->mostraStatoOperazione(false, "Hai già una richiesta pendente.", "dashboard-cliente");
            return;
        }
        $this->gestisciAzioneRichiesta($cliente);
    }

    private function gestisciAzioneRichiesta(Cliente $cliente): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $pal = $cliente->getPalestra();
            $this->view->mostraTemplate('richiedi_scheda.tpl', [
                'utente' => $cliente, 'allenatori' => $pal ? $this->allenatoreRepo->findByPalestra($pal) : []
            ]);
            return;
        }
        $this->richiestaSchedaAllenatore(
            trim($_POST['obiettivo'] ?? ''),
            isset($_POST['n_allenamenti']) ? (int)$_POST['n_allenamenti'] : 3,
            trim($_POST['cf_allenatore'] ?? '')
        );
    }

    public function richiestaSchedaAllenatore(string $obiettivo, int $nAllenamenti, string $cfAllenatore): void
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
        foreach ($this->schedaRepo->findByCliente($cliente) as $vs) {
            $cliente->setScheda(null);
            $this->clienteRepo->save($cliente);
            $this->schedaRepo->delete($vs);
        }
    }

    private function creaRichiestaSchedaConAllenamenti(Cliente $cli, Allenatore $all, string $ob, int $num): void
    {
        try {
            $scheda = new Scheda("Richiesta Nuova Scheda", new \DateTimeImmutable('today'), new \DateTimeImmutable('+1 month'), $ob, $cli, $all);
            $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
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

    public function apriFormCreazioneScheda(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        if (!$idAllenatore || $this->session->getLoggedUserRole() !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        if (isset($_REQUEST['cf'])) {
            $this->selezionaUtentePerScheda(trim($_REQUEST['cf']));
            return;
        }
        header("Location: clienti");
        exit;
    }

    public function selezionaUtentePerScheda(string $cf): void
    {
        $all = $this->allenatoreRepo->findById($this->session->getLoggedUserId());
        $cli = $this->clienteRepo->findByCF($cf);
        if (!$cli || ($cli->getPalestra() && $cli->getPalestra()->getId() !== $all->getPalestra()->getId())) {
            $this->view->mostraStatoOperazione(false, "Cliente non valido.", "dashboard-allenatore");
            return;
        }
        $scheda = $this->schedaRepo->findPendenteByCliente($cli);
        if (!$scheda) {
            $this->eliminaVecchieSchede($cli);
            $scheda = new Scheda("Nuovo Protocollo", new \DateTimeImmutable('today'), new \DateTimeImmutable('+1 month'), "Inserisci obiettivo", $cli, $all);
            foreach (['A', 'B', 'C'] as $l) $scheda->addAllenamento(new Allenamento("Allenamento " . $l, "Informazioni " . $l));
        } else {
            $scheda->setNome_scheda("Nuovo Protocollo");
        }
        $this->schedaRepo->save($scheda);
        header("Location: modifica-scheda?id=" . $scheda->getId() . (isset($_REQUEST['azione_rapida']) ? "&azione_rapida=1" : ""));
        exit();
    }

    // =========================================================================
    // 3. MODIFICA SCHEDA (/modifica-scheda)
    // =========================================================================

    public function apriFormModificaScheda(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $allenatore = $idAllenatore ? $this->allenatoreRepo->findById($idAllenatore) : null;
        if (!$allenatore || $this->session->getLoggedUserRole() !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $idScheda = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id_scheda'] ?? 0);
        if ($idScheda <= 0) {
            $this->reindirizzaASchedaEsistente($allenatore);
            return;
        }
        $scheda = $this->schedaRepo->findById($idScheda);
        if (!$scheda || $scheda->getCliente()->getPalestra()->getId() !== $allenatore->getPalestra()->getId()) {
            $this->view->mostraStatoOperazione(false, "Scheda non trovata o accesso negato.", "dashboard-allenatore");
            return;
        }
        if (isset($_GET['copia_da'])) {
            $this->eseguiClonazioneScheda($scheda, (int)$_GET['copia_da']);
        }
        $this->mostraFormModificaSchedaConDati($allenatore, $scheda);
    }

    private function reindirizzaASchedaEsistente(Allenatore $allenatore): void
    {
        $palestra = $allenatore->getPalestra();
        $schede = $palestra ? $this->schedaRepo->findByPalestra($palestra) : [];
        if (!empty($schede)) {
            $url = "modifica-scheda?id=" . $schede[0]->getId() . (isset($_GET['azione_rapida']) ? "&azione_rapida=1" : "");
            header("Location: " . $url);
        } else {
            header("Location: dashboard-allenatore");
        }
        exit();
    }

    private function eseguiClonazioneScheda(Scheda $scheda, int $idSorgente): void
    {
        $sorgente = $this->schedaRepo->findById($idSorgente);
        if ($sorgente) {
            foreach ($scheda->getAllenamenti() as $all) {
                $scheda->removeAllenamento($all);
            }
            $this->schedaRepo->save($scheda);
            /** @var Allenamento $srcAll */
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

    private function mostraFormModificaSchedaConDati(Allenatore $all, Scheda $scheda): void
    {
        $this->view->mostraTemplate('gestione_scheda.tpl', [
            'utente' => $all, 'scheda' => $scheda, 'esercizi' => $this->esercizioRepo->findAll(),
            'altre_schede' => $this->schedaRepo->findAltreByPalestra($all->getPalestra(), $scheda->getId()),
            'azione_rapida' => isset($_GET['azione_rapida']) ? 1 : 0
        ]);
    }

    // =========================================================================
    // 4. SALVA SCHEDA (/salva-scheda)
    // =========================================================================

    public function salvaScheda(?int $idScheda = null): void
    {
        if ($idScheda === null) {
            $this->modificaScheda();
            return;
        }
        $scheda = $this->schedaRepo->findById($idScheda);
        if ($scheda) {
            $this->schedaRepo->save($scheda);
            $this->view->mostraStatoOperazione(true, "Scheda salvata come bozza.", "dashboard-allenatore");
        }
    }

    public function modificaScheda(): void
    {
        $all = $this->allenatoreRepo->findById($this->session->getLoggedUserId());
        $idScheda = (int)($_POST['id_scheda'] ?? 0);
        $scheda = $this->schedaRepo->findById($idScheda);
        if (!$scheda || $scheda->getCliente()->getPalestra()->getId() !== $all->getPalestra()->getId()) {
            $this->view->mostraStatoOperazione(false, "Scheda non trovata o accesso negato.", "dashboard-allenatore");
            return;
        }
        $nome = trim($_POST['nome_scheda'] ?? '');
        $ini = $_POST['data_inizio'] ?? '';
        $fine = $_POST['data_fine'] ?? '';
        if ($nome === '' || $ini === '' || $fine === '') {
            $this->view->mostraStatoOperazione(false, "Campi obbligatori mancanti.", "modifica-scheda?id=" . $idScheda);
            return;
        }
        $this->eseguiAggiornamentoScheda($scheda, $nome, $ini, $fine, $idScheda);
    }

    private function eseguiAggiornamentoScheda(Scheda $scheda, string $nome, string $ini, string $fine, int $idScheda): void
    {
        try {
            $scheda->setNome_scheda($nome);
            $scheda->setData_inizio(new \DateTimeImmutable($ini));
            $scheda->setData_fine(new \DateTimeImmutable($fine));
            $scheda->setObiettivo(trim($_POST['obiettivo'] ?? ''));
            foreach ($scheda->getAllenamenti() as $all) $scheda->removeAllenamento($all);
            $this->schedaRepo->save($scheda);
            $this->salvaWorkoutData($scheda, $_POST['workouts'] ?? []);
            $this->schedaRepo->save($scheda);
            (($_POST['azione'] ?? 'salva') === 'invia') ? $this->inviaScheda($idScheda) : $this->salvaScheda($idScheda);
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), "modifica-scheda?id=" . $idScheda);
        }
    }

    private function salvaWorkoutData(Scheda $scheda, array $workoutsData): void
    {
        foreach ($workoutsData as $wData) {
            $all = new Allenamento(trim($wData['nome'] ?? 'Allenamento'), trim($wData['descrizione'] ?? ''));
            $scheda->addAllenamento($all);
            foreach ($wData['dettagli'] ?? [] as $dData) {
                $es = $this->entityManager->find(Esercizio::class, (int)($dData['esercizio_id'] ?? 0));
                if ($es) {
                    $reps = isset($dData['ripetizioni']) && $dData['ripetizioni'] !== '' ? (int)$dData['ripetizioni'] : null;
                    $temp = isset($dData['tempo']) && trim($dData['tempo']) !== '' ? trim($dData['tempo']) : null;
                    $all->addDettaglio(new DettaglioAllenamento($es, $all, (int)($dData['serie'] ?? 1), $reps, (float)($dData['carico'] ?? 0.0), $temp));
                }
            }
        }
    }

    // =========================================================================
    // 5. INVIA SCHEDA (/invia-scheda)
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
    // 6. ELIMINA SCHEDA / RIMUOVI SCHEDA (/elimina-scheda, /rimuovi-scheda)
    // =========================================================================

    public function eliminaScheda(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || ($ruolo !== 'amministratore' && $ruolo !== 'allenatore')) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $scheda = $this->schedaRepo->findById(isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$scheda) {
            $this->view->mostraStatoOperazione(false, "Scheda non trovata.", "dashboard-" . $ruolo);
            return;
        }
        if ($ruolo === 'allenatore') {
            $all = $this->allenatoreRepo->findById($idUt);
            if ($scheda->getCliente()->getPalestra()->getId() !== $all->getPalestra()->getId()) {
                $this->view->mostraStatoOperazione(false, "Accesso negato. IDOR rilevato.", "dashboard-allenatore");
                return;
            }
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
    // 7. VISUALIZZA SCHEDA DA PARTE DEL CLIENTE (/visualizza-scheda)
    // =========================================================================

    public function visualizzaScheda(): void
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        $id = $this->session->getLoggedUserId();
        if (!$id || $this->session->getLoggedUserRole() !== 'cliente') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $cliente = $this->clienteRepo->findById($id);
        if (!$cliente || !$cliente->getScheda()) {
            $this->view->mostraStatoOperazione(false, "Scheda non trovata o non attiva.", "dashboard-cliente");
            return;
        }
        $this->view->mostraTemplate('visualizza_scheda.tpl', ['utente' => $cliente, 'scheda' => $cliente->getScheda()]);
    }

    // =========================================================================
    // 8. MODIFICA DETTAGLI SCHEDA DA PARTE DEL CLIENTE (/modifica-dettagli)
    // =========================================================================

    public function apriFormModificaSchedaCliente(): void
    {
        $id = $this->session->getLoggedUserId();
        $cliente = ($id && $this->session->getLoggedUserRole() === 'cliente') ? $this->clienteRepo->findById($id) : null;
        if (!$cliente || !$cliente->getScheda()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato o scheda non attiva.", "login");
            return;
        }
        $scheda = $cliente->getScheda();
        $all = $this->entityManager->find(Allenamento::class, isset($_REQUEST['id_allenamento']) ? (int)$_REQUEST['id_allenamento'] : 0);
        if (!$all || $all->getScheda()->getId() !== $scheda->getId()) {
            $this->view->mostraStatoOperazione(false, "Allenamento non trovato.", "visualizza-scheda");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraTemplate('modifica_dettagli.tpl', ['utente' => $cliente, 'scheda' => $scheda, 'allenamento' => $all]);
            return;
        }
        $this->eseguiSalvataggioDettagliCliente($cliente, $scheda);
    }

    private function eseguiSalvataggioDettagliCliente(Cliente $cliente, Scheda $scheda): void
    {
        $dettagliModificati = $_POST['dettagli'] ?? [];
        $oggi = new \DateTimeImmutable('today');
        foreach ($dettagliModificati as $idDet => $data) {
            $dettaglio = $this->entityManager->find(DettaglioAllenamento::class, (int)$idDet);
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
        $this->view->mostraStatoOperazione(true, "Dettagli aggiornati con successo.", "visualizza-scheda");
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
    // 9. VISUALIZZA PROGRESSI CLIENTE (/progressi-cliente)
    // =========================================================================

    public function visualizzaProgressiCliente(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $all = ($idUt && $this->session->getLoggedUserRole() === 'allenatore') ? $this->allenatoreRepo->findById($idUt) : null;
        if (!$all) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $cli = $this->clienteRepo->findById(isset($_GET['id_cliente']) ? (int)$_GET['id_cliente'] : 0);
        if (!$cli || ($cli->getPalestra() && $cli->getPalestra()->getId() !== $all->getPalestra()->getId())) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato o IDOR rilevato.", "clienti");
            return;
        }
        $schede = $this->schedaRepo->findByCliente($cli);
        $scheda = $cli->getScheda() ?: (!empty($schede) ? $schede[0] : null);
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
            /** @var DettaglioAllenamento $dettaglio */
            foreach ($allenamento->getDettagli() as $dettaglio) {
                $es = $dettaglio->getEsercizio();
                $d = $this->elaboraProgressiGrafici($this->progressoRepo->findByClienteAndEsercizio($cli, $es));
                $eserciziData[] = [
                    'dettaglio' => $dettaglio, 'esercizio' => $es,
                    'puntiCarico' => $this->calcolaCoordinateGrafico($d['carico']),
                    'puntiReps' => $this->calcolaCoordinateGrafico($d['reps']),
                    'puntiDurata' => $this->calcolaCoordinateGrafico($d['durata']),
                    'storico' => $d['storico'], 'hasCarico' => count($d['carico']) > 0,
                    'hasReps' => count($d['reps']) > 0, 'hasDurata' => count($d['durata']) > 0
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
            $dStr = $p->getData()->format('d/m/Y'); $dGraf = $p->getData()->format('d/m');
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

    // =========================================================================
    // HELPER GENERALI
    // =========================================================================

    private function calcolaCoordinateGrafico(array $puntiRaw): array
    {
        if (count($puntiRaw) === 0) return [];
        $valori = array_column($puntiRaw, 'valore');
        $minVal = max(0, min($valori) - 2);
        $maxVal = max($valori) + 2;
        $range = $maxVal - $minVal ?: 1;
        $puntiCoo = [];
        $count = count($puntiRaw);
        foreach ($puntiRaw as $i => $pt) {
            $val = $pt['valore'];
            $puntiCoo[] = [
                'x' => 35 + ($i * (380 / ($count - 1 ?: 1))),
                'y' => 15 + 80 - (($val - $minVal) / $range * 80),
                'valore' => $val, 'data' => $pt['data']
            ];
        }
        return $puntiCoo;
    }
}
