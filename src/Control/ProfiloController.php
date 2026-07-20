<?php

namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\ParametriRepositoryInterface;
use App\Entity\Repository\CertificatoMedicoRepositoryInterface;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Entity\Repository\ProgressoRepositoryInterface;
use App\Entity\Repository\AttivitaRepositoryInterface;
use App\Entity\Repository\UtenteRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Entity\Repository\AmministratoreRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineParametriRepository;
use App\Foundation\Persistence\Repository\DoctrineCertificatoMedicoRepository;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaRepository;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Repository\DoctrineAmministratoreRepository;
use App\View\Interface\ProfiloView;
use App\View\ProfiloViewSmarty;
use App\View\VisualizzazioneViewSmarty;
use App\Control\AttivitaPianificataController;
use App\Foundation\Session;
use App\Entity\Parametri;
use App\Entity\CertificatoMedico;
use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Utente;
use App\Entity\Cliente;
use App\Entity\Attivita;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Palestra;
class ProfiloController 
{
    private ClienteRepositoryInterface $clienteRepo;
    private ParametriRepositoryInterface $parametriRepo;
    private CertificatoMedicoRepositoryInterface $certificatoRepo;
    private PalestraRepositoryInterface $palestraRepo;
    private ProgressoRepositoryInterface $progressoRepo;
    private AttivitaRepositoryInterface $attivitaRepo;
    private UtenteRepositoryInterface $utenteRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private AmministratoreRepositoryInterface $amministratoreRepo;
    private ProfiloView $view;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->entityManager = $entityManager;
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->parametriRepo = new DoctrineParametriRepository($this->entityManager);
        $this->certificatoRepo = new DoctrineCertificatoMedicoRepository($this->entityManager);
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->progressoRepo = new DoctrineProgressoRepository($this->entityManager);
        $this->attivitaRepo = new DoctrineAttivitaRepository($this->entityManager);
        $this->utenteRepo = new DoctrineUtenteRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->amministratoreRepo = new DoctrineAmministratoreRepository($this->entityManager);
        $this->view = new ProfiloViewSmarty();
    }

    // =========================================================================
    // 1. VISUALIZZA PROFILO (/profilo, /visualizza-profilo)
    // =========================================================================

    public function visualizzaProfilo(): void     //gestisce la richiesta di visualizzazione del profilo dell'utente loggato o di un altro utente, recuperando i dati dell'utente e mostrando la view corrispondente
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt) {
            $this->mostraStatoOperazione(false, "Sessione non valida. Effettua il login.");
            return;
        }
        $isSelf = true;                                                // indica se l'utente sta visualizzando il proprio profilo o quello di un altro utente
        $ut = $this->determinaUtenteProfilo($idUt, $ruolo, $isSelf);
        if (!$ut) {
            $this->mostraStatoOperazione(false, "Profilo non trovato o accesso negato.");
            return;
        }
        $dati = [                                                          //inizializza un array associativo che conterrà i dati da passare alla view per la visualizzazione del profilo
            'utente' => $ut, 'isClient' => ($ut instanceof Cliente), 'isTrainer' => ($ut instanceof Allenatore),
            'isSelf' => $isSelf, 'nome' => $ut->getNome(), 'cognome' => $ut->getCognome(), 'email' => $ut->getEmail(),
            'cf' => $ut->getCF(), 'fotoProfilo' => $ut->getProfilePicture() ? base64_encode($ut->getProfilePicture()) : null,
            'tipoImmagine' => $ut->getTipoImmagine() ?? 'image/jpeg',
            'abbonamento' => null, 'abbonamento_attivo' => false, 'has_progress' => false, 'parametri' => null,
            'certificato' => null, 'attivitaAbilitate' => null, 'attivitaNonAbilitate' => [], 'tutteAttivita' => []
        ];
        $dati = ($ut instanceof Cliente) ? array_merge($dati, $this->caricaDatiClient($ut, $ruolo)) : (($ut instanceof Allenatore) ? array_merge($dati, $this->caricaDatiTrainer($ut)) : $dati);      //popola l'array dei dati con le informazioni specifiche del tipo di utente (cliente o allenatore)
        $this->view->mostraProfilo($dati);
    }

    private function determinaUtenteProfilo(int $idUt, string $ruolo, bool &$isSelf): ?Utente
    {
        $isSelf = !isset($_GET['id']);     //se c'è un parametro id nella richiesta, significa che l'utente sta visualizzando il profilo di un altro utente, altrimenti sta visualizzando il proprio profilo
        if ($isSelf) {
            return $this->recuperaUtenteLoggato($this->entityManager, $idUt, $ruolo);
        }
        $targetId = (int)$_GET['id'];
        $utente = $this->utenteRepo->findById($targetId);
        $targetRuolo=$utente->getRuolo();
        if ($ruolo === 'amministratore' || $ruolo === 'allenatore' && $targetRuolo === 'cliente') {
            $pal = AttivitaPianificataController::recuperaPalestraUtenteStatic(
                $this->session,
                $this->utenteRepo,
                $this->palestraRepo,
                $this->clienteRepo
            );
            $palTarget = $utente ? $this->recuperaPalestraUtente($utente) : null;
            if (!$utente || !$palTarget || !$pal || $palTarget->getId() !== $pal->getId()) {
                return null;
            }
            return $utente;
        }
        return null;
    }

    private function caricaDatiClient(Cliente $ut, string $ruolo): array               //recupera i dati specifici del cliente, come parametri corporei, certificato medico, abbonamento e progresso, e li restituisce in un array associativo
    {
        $params = $this->parametriRepo->findUltimaByCliente($ut);
        $cert = ($ruolo === 'allenatore') ? null : $this->certificatoRepo->findByCliente($ut);
        return [
            'parametri' => $params ? [
                'peso' => $params->getPeso(), 'altezza' => $params->getAltezza(), 'data' => $params->getData()->format('d/m/Y'),
                'bicipiteDestro' => $params->getBicipiteDestro(), 'bicipiteSinistro' => $params->getBicipiteSinistro(),
                'tricipiteDestro' => $params->getTricipiteDestro(), 'tricipiteSinistro' => $params->getTricipiteSinistro(),
                'cosciaDestra' => $params->getCosciaDestra(), 'cosciaSinistra' => $params->getCosciaSinistra(),
                'polpaccioDestro' => $params->getPolpaccioDestro(), 'polpaccioSinistro' => $params->getPolpaccioSinistro(),
                'misuraPetto' => $params->getMisuraPetto(), 'misuraVita' => $params->getMisuraVita(),
                'misuraSpalle' => $params->getMisuraSpalle(), 'misuraFianchi' => $params->getMisuraFianchi(),
            ] : null,
            'certificato' => $cert ? [
                'scadenza' => $cert->getDataScadenza()->format('d/m/Y'), 'medico' => $cert->getMedico(), 'valido' => $cert->isValido()
            ] : null,
            'abbonamento' => $ut->getAbbonamento(), 'abbonamento_attivo' => $ut->isAbbonamentoAttivo(),
            'has_progress' => count($this->progressoRepo->findByCliente($ut)) > 0                           // indica se il cliente ha registrato progressi nel tempo
        ];
    }

    private function caricaDatiTrainer(Allenatore $ut): array        //recupera i dati specifici dell'allenatore, come le attività abilitate e non abilitate, e li restituisce in un array associativo
    {
        $abilitate = $ut->getAttivitaAbilitate();
        $tutte = $this->attivitaRepo->findAll();
        $nonAbilitate = [];
        foreach ($tutte as $att) {
            if (!$abilitate->contains($att)) {
                $nonAbilitate[] = $att;
            }
        }
        return [
            'attivitaAbilitate' => $abilitate,
            'attivitaNonAbilitate' => $nonAbilitate,
            'tutteAttivita' => $tutte
        ];
    }

    // =========================================================================
    // 2. MODIFICA DATI (/modifica-anagrafica)
    // =========================================================================

    public function modificaAnagrafica(): void      //gestisce la richiesta di modifica dei dati anagrafici dell'utente loggato o di un altro utente, recuperando i dati dell'utente e mostrando la view corrispondente
    {
        $idUtente = $this->session->getLoggedUserId();
        if (!$idUtente) {
            $this->mostraStatoOperazione(false, "Sessione scaduta.");
            return;
        }
        $ruolo = $this->session->getLoggedUserRole();
        $targetId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : $idUtente);
        if ($ruolo !== 'amministratore' && ($idUtente  !==  $targetId)) {
            $this->mostraStatoOperazione(false, "Accesso negato. Non sei autorizzato a modificare l'anagrafica");
            return;
        }
        

        $isSelf = true;
        $utente = $this->determinaUtenteModifica($idUtente, $ruolo, $isSelf);
        if (!$utente) {
            $this->mostraStatoOperazione(false, "Profilo non trovato o accesso negato.");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormModifica(['utente' => $utente, 'isClient' => ($utente instanceof Cliente), 'isSelf' => $isSelf, 'ruolo' => $ruolo]);
            return;
        }
        $ritorno = $isSelf ? 'profilo' : 'visualizza-profilo?id=' . $utente->getId();
        $this->eseguiSalvataggioAnagrafica($utente, $ruolo, $ritorno);
    }

    private function determinaUtenteModifica(int $idUt, string $ruolo, bool &$isSelf): ?Utente       //determina quale utente deve essere modificato in base all'ID dell'utente loggaton enal ruolo
    {
        $targetId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : $idUt);
        $isSelf = ($targetId === $idUt);
        if ($isSelf) {
            return $this->recuperaUtenteLoggato($this->entityManager, $idUt, $ruolo);     //se devo vedere il mio profilo ho bisogno del mio id
        }
        if ($ruolo !== 'amministratore') {
            return null;
        }
        $utente = $this->utenteRepo->findById($targetId);
        $admin = $this->amministratoreRepo->findById($idUt);
        $pal = $this->palestraRepo->findByAmministratore($admin);
        $palTarget = $utente ? $this->recuperaPalestraUtente($utente) : null;
        if (!$utente || !$palTarget || !$pal || $palTarget->getId() !== $pal->getId()) {
            return null;
        }
        return $utente;
    }

    private function eseguiSalvataggioAnagrafica(Utente $ut, string $ruolo, string $rit): void
    {
        $nome = $_POST['nome'] ?? '';
        $cognome = $_POST['cognome'] ?? '';
        $res = $_POST['indirizzo'] ?? '';
        $pag = $_POST['metodo_pagamento'] ?? '';

        $cliente = $this->clienteRepo->findById($ut->getId());
        $isClient = ($cliente !== null);

        if (empty($nome) || empty($cognome) || empty($res) || ($isClient && $ruolo === 'amministratore' && empty($pag))) {      // controlla se i campi obbligatori sono stati compilati, se manca qualcosa mostra un errore e ritorna alla pagina di modifica
            $this->mostraStatoOperazione(false, "Campi obbligatori mancanti.", $rit, "Torna al Profilo");
            return;
        }
        try {
            if ($cliente) {
                $cliente->setNome($nome)->setCognome($cognome)->setIndirizzo($res);
                $cliente->setIndirizzoDiDomicilio($_POST['indirizzo_domicilio'] ?? '');
                if ($ruolo === 'amministratore') {
                    $cliente->setMetodoDiPagamento($pag);
                }
                $this->clienteRepo->save($cliente);
            } else {
                $ut->setNome($nome)->setCognome($cognome)->setIndirizzo($res);     // se l'utente non è un cliente, aggiorna solo i dati anagrafici di base
                $this->utenteRepo->save($ut);
            }
            header('Location: ' . $rit);
            exit();
        } catch (\InvalidArgumentException $e) {
            $this->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), $rit, "Torna al Profilo");
        }
    }

    // =========================================================================
    // 3. AGGIORNA MISURE CORPOREE (/aggiorna-misure)
    // =========================================================================

    public function aggiornaMisureCorporee(): void              //gestisce la richiesta di visualizzazione del form per aggiornare le misure corporee del cliente loggato o di un altro cliente, recuperando i dati del cliente e mostrando la view corrispondente
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $cliente = $idUt ? $this->recuperaClienteTarget($ruolo, $idUt) : null;    // recupera il cliente target in base al ruolo dell'utente loggato e al suo ID
        if (!$cliente || $ruolo!== 'cliente') {
            $this->mostraStatoOperazione(false, "Cliente non trovato o accesso non consentito.");
            return;
        }
        $storico = $this->parametriRepo->findByCliente($cliente);
        $this->view->mostraFormMisure([
            'utente' => $cliente,
            'ultimaMisure' => $this->parametriRepo->findUltimaByCliente($cliente),
            'storicoMisure' => $storico,
            'storicoMisureCronologico' => array_reverse($storico),
            'isSelf' => ($ruolo === 'cliente')
        ]);
    }

    // =========================================================================
    // 4. INSERISCI MISURE CORPOREE (/inserisci-misure)
    // =========================================================================

    public function inserisciMisureCorporee(): void     //gestisce la richiesta di inserimento di nuove misure corporee per il cliente loggato o per un altro cliente, recuperando i dati del cliente e mostrando la view corrispondente
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || $ruolo !== 'cliente') {
            $this->mostraStatoOperazione(false, "Azione non consentita.");
            return;
        }
        $cliente = $this->recuperaClienteTarget($ruolo, $idUt);
        if (!$cliente) {
            $this->mostraStatoOperazione(false, "Cliente non trovato o accesso non consentito.");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormInserimentoMisure(['utente' => $cliente, 'ultimaMisure' => $this->parametriRepo->findUltimaByCliente($cliente)]);
            return;
        }
        $this->salvaMisurePost($cliente, $ruolo);
    }

    private function salvaMisurePost(Cliente $cliente, string $ruolo): void
    {
        $peso = isset($_POST['peso']) ? (float)$_POST['peso'] : 0.0;
        $altezza = isset($_POST['altezza']) ? (float)$_POST['altezza'] : 0.0;
        if ($peso <= 0 || $altezza <= 0) {
            $this->mostraStatoOperazione(false, "Peso e altezza sono obbligatori.");
            return;
        }
        $f = fn($key) => !empty($_POST[$key]) ? (float)$_POST[$key] : null;      // funzione anonima per recuperare i valori delle misure corporee dal POST, restituendo null se il campo è vuoto
        try {
            $p = new Parametri(
                $peso, $altezza, new \DateTimeImmutable(), $cliente,
                $f('bicipite_destro'), $f('bicipite_sinistro'), $f('tricipite_destro'), $f('tricipite_sinistro'),
                $f('coscia_destra'), $f('coscia_sinistra'), $f('polpaccio_destro'), $f('polpaccio_sinistro'),
                $f('misura_petto'), $f('misura_vita'), $f('misura_spalle'), $f('misura_fianchi')
            );
            $this->parametriRepo->salvaMisure($p);
            header('Location: aggiorna-misure' . ($ruolo !== 'cliente' ? '?id=' . $cliente->getId() : ''));
            exit();
        } catch (\InvalidArgumentException $e) {
            $this->mostraStatoOperazione(false, "Dati non validi: " . $e->getMessage());
        }
    }

    // =========================================================================
    // 5. CARICA CERTIFICATO MEDICO (/carica-certificato)
    // =========================================================================

    public function caricaCertificato(): void     //gestisce la richiesta di caricamento del certificato medico per il cliente loggato o per un altro cliente, recuperando i dati del cliente e mostrando la view corrispondente
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || $ruolo === 'allenatore') {
            $this->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }
        $cliente = $this->recuperaClienteTarget($ruolo, $idUt);
        if (!$cliente) {
            $this->mostraStatoOperazione(false, "Cliente non trovato o accesso non consentito.");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCertificato(['utente' => $cliente]);
            return;
        }
        $rit = ($ruolo === 'cliente') ? 'profilo' : 'visualizza-profilo?id=' . $cliente->getId();
        $this->eseguiUploadCertificato($cliente, $rit);
    }

    private function eseguiUploadCertificato(Cliente $cliente, string $rit): void
    {
        if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {     // controlla se la richiesta POST è vuota e se la dimensione del contenuto supera il limite consentito, mostrando un errore se necessario
            $this->mostraStatoOperazione(false, "File troppo grande.", $rit, "Torna al Profilo");
            return;
        }
        $medico = $_POST['medico'] ?? null;
        $emissione = $_POST['data_emissione'] ?? null;
        if (empty($medico) || empty($emissione) || !isset($_FILES['file_certificato']) || $_FILES['file_certificato']['error'] !== UPLOAD_ERR_OK) {
            $this->mostraStatoOperazione(false, "Dati certificato incompleti o file non valido.", $rit, "Torna al Profilo");
            return;
        }
        try {
            $content = file_get_contents($_FILES['file_certificato']['tmp_name']);     //guarda se il file è stato caricato correttamente e legge il contenuto del file temporaneo
            $vecchio = $cliente->getCertificatoMedico();
            $cert = new CertificatoMedico(new \DateTimeImmutable($emissione), $medico, $cliente, $content);
            $this->certificatoRepo->save($cert);
            $cliente->setCertificatoMedico($cert);
            $this->clienteRepo->save($cliente);
            if ($vecchio) $this->certificatoRepo->delete($vecchio);
            $this->mostraStatoOperazione(true, "Certificato medico caricato correttamente.", $rit, "Torna al Profilo");
        } catch (\Exception $e) {
            $this->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), $rit, "Torna al Profilo");
        }
    }

    // =========================================================================
    // 6. CAMBIA PASSWORD (/cambia-password)
    // =========================================================================

    public function cambiaPassword(): void   //gestisce la richiesta di cambio password per l'utente loggato, mostrando il form di cambio password o eseguendo il salvataggio della nuova password
    {
        $idUt = $this->session->getLoggedUserId();
        if (!$idUt) {
            $this->mostraStatoOperazione(false, "Sessione scaduta o non valida.");
            return;
        }
        $targetId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : $idUt);
        if ($targetId !== $idUt) {
            $this->mostraStatoOperazione(false, "Accesso negato. Il cambio password è strettamente personale.");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCambioPassword();
            return;
        }
        $old = $_POST['vecchia_password'] ?? '';
        $new = $_POST['nuova_password'] ?? '';
        $conf = $_POST['conferma_password'] ?? '';
        if ($old === '' || $new === '' || $conf === '' || $new !== $conf) {
            $this->mostraStatoOperazione(false, "Campi vuoti o password non coincidenti.", "profilo", "Torna al Profilo");
            return;
        }
        $this->eseguiCambioPassword($idUt, $this->session->getLoggedUserRole(), $old, $new);
    }

    private function eseguiCambioPassword(int $idUt, string $ruolo, string $old, string $new): void
    {
        $ut = $this->recuperaUtenteLoggato($this->entityManager, $idUt, $ruolo);
        if (!$ut || !$ut->verificaPassword($old)) {
            $this->mostraStatoOperazione(false, "Utente non trovato o password errata.", "profilo", "Torna al Profilo");
            return;
        }
        try {
            $ut->setPassword($new);
            $this->utenteRepo->save($ut);
            $this->mostraStatoOperazione(true, "Password aggiornata con successo.", "profilo", "Torna al Profilo");
        } catch (\InvalidArgumentException $e) {
            $this->mostraStatoOperazione(false, "Errore: " . $e->getMessage(), "profilo", "Torna al Profilo");
        }
    }

    // =========================================================================
    // 7. VISUALIZZA GRAFICO (/visualizza-grafico)
    // =========================================================================

    public function visualizzaGrafico(): void   //gestisce la richiesta di visualizzazione del grafico delle misure corporee del cliente loggato o di un altro cliente, recuperando i dati del cliente e mostrando la view corrispondente
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $cliente = $idUt ? $this->recuperaClienteTarget($ruolo, $idUt) : null;
        if (!$cliente) {
            $this->mostraStatoOperazione(false, "Cliente non trovato o accesso non consentito.");
            return;
        }
        $tipo = $_GET['tipo'] ?? 'peso';                                             // indica il tipo di grafico da visualizzare (peso, superiore o inferiore), con un valore predefinito di "peso" se non specificato
        if (!in_array($tipo, ['peso', 'superiore', 'inferiore'])) $tipo = 'peso';
        $storico = array_reverse($this->parametriRepo->findByCliente($cliente));
        $valori = $this->mappaValoriGrafico($storico, $tipo);
        $titoli = ['peso' => 'Andamento Peso Corporeo', 'superiore' => 'Andamento Misure Parte Superiore (Media)', 'inferiore' => 'Andamento Misure Parte Inferiore (Media)'];
        $labels = [];
        foreach ($storico as $m) {
            $labels[] = $m->getData()->format('d/m');
        }
        $this->view->mostraGrafico([
            'utente' => $cliente, 'tipo' => $tipo,
            'titolo' => $titoli[$tipo] ?? 'Grafico',
            'labels' => $labels,
            'valori' => $valori
        ]);
    }

    private function mappaValoriGrafico(array $storico, string $tipo): array
    {
        $valori = [];
        foreach ($storico as $m) {
            if ($tipo === 'peso') {
                $valori[] = $m->getPeso();
            } else {
                $sub = ($tipo === 'superiore') ?
                    [$m->getBicipiteDestro(), $m->getBicipiteSinistro(), $m->getTricipiteDestro(), $m->getTricipiteSinistro(), $m->getMisuraPetto(), $m->getMisuraSpalle()] :
                    [$m->getCosciaDestra(), $m->getCosciaSinistra(), $m->getPolpaccioDestro(), $m->getPolpaccioSinistro(), $m->getMisuraVita(), $m->getMisuraFianchi()];
                $nonNull = array_filter($sub, fn($v) => $v !== null);                     // filtra i valori nulli dall'array delle misure corporee per calcolare la media solo sui valori presenti
                $media = count($nonNull) > 0 ? array_sum($nonNull) / count($nonNull) : 0.0;
                $valori[] = round($media, 2);     // calcola la media delle misure corporee e la arrotonda a due decimali
            }
        }
        return $valori;
    }



    // =========================================================================
    // 8. CARICA FOTO PROFILO (/carica-foto)
    // =========================================================================

    public function caricaFotoProfilo(): void                  //gestisce la richiesta di caricamento della foto del profilo per l'utente loggato, recuperando i dati dell'utente e mostrando la view corrispondente
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $ut = $idUt ? $this->recuperaUtenteLoggato($this->entityManager, $idUt, $ruolo) : null;       // recupera l'oggetto utente loggato in base al suo ID e al ruolo
        if (!$ut) {
            $this->mostraStatoOperazione(false, "Profilo non trovato.");
            return;
        }
        if (isset($_FILES['foto_profilo']) && in_array($_FILES['foto_profilo']['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE])) {   //gli errori UPLOAD_ERR_INI_SIZE e UPLOAD_ERR_FORM_SIZE indicano che la dimensione del file caricato supera il limite consentito dal server o dal form HTML, quindi viene mostrato un messaggio di errore appropriato
            $this->mostraStatoOperazione(false, "Dimensione file eccessiva.", "profilo", "Torna al Profilo");
            return;
        }
        if (!isset($_FILES['foto_profilo']) || $_FILES['foto_profilo']['error'] !== UPLOAD_ERR_OK) {
            $this->mostraStatoOperazione(false, "File non valido.", "profilo", "Torna al Profilo");
            return;
        }
        $this->eseguiCaricamentoFoto($ut);
    }

    private function eseguiCaricamentoFoto(Utente $ut): void
    {
        $tmp = $_FILES['foto_profilo']['tmp_name'];               // recupera il percorso del file temporaneo caricato sul server
        if ($_FILES['foto_profilo']['size'] > 16 * 1024 * 1024) {        
            $this->mostraStatoOperazione(false, "La dimensione supera i 16 MB.", "profilo", "Torna al Profilo");
            return;
        }
        $info = @getimagesize($tmp);                     // recupera le informazioni sull'immagine, come tipo e dimensioni, e restituisce false se il file non è un'immagine valida
        if ($info === false || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {     // controlla se il file è un'immagine valida e se il tipo di immagine è consentito (JPG o PNG), mostrando un messaggio di errore se non lo è
            $this->mostraStatoOperazione(false, "Formato non consentito (ammessi solo JPG/PNG).", "profilo", "Torna al Profilo");
            return;
        }
        $content = file_get_contents($tmp);       // legge il contenuto del file temporaneo e lo memorizza in una variabile, restituendo false se non riesce a leggere il file
        if ($content !== false) {
            $ut->setProfilePicture($content);
            $ut->setTipoImmagine($info['mime'] ?? 'image/jpeg');
            $this->utenteRepo->save($ut);
            $this->mostraStatoOperazione(true, "Foto profilo aggiornata con successo.", "profilo", "Torna al Profilo");
        }
    }



    // =========================================================================
    // 9. AGGIORNA ABILITAZIONI ALLENATORE IN BLOCCO (/aggiorna-abilitazioni-profilo)
    // =========================================================================

    public function aggiornaAbilitazioniAllenatore(): void     //gestisce la richiesta di aggiornamento delle abilitazioni di un allenatore, verificando i permessi dell'utente loggato e aggiornando le attività abilitate in blocco
    {
        $idLog = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idLog || ($ruolo !== 'amministratore' && $ruolo !== 'allenatore')) {
            $this->mostraStatoOperazione(false, "Azione non consentita.");
            return;
        }
        $idAll = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;
        if ($idAll <= 0 || ($ruolo !== 'amministratore' && $idAll !== $idLog)) {
            $this->mostraStatoOperazione(false, "Dati non validi o non autorizzato.");
            return;
        }
        $allenatore = $this->allenatoreRepo->findById($idAll);
        if (!$allenatore || !$this->validaPalestraAllenatore($idLog, $ruolo, $allenatore)) {
            $this->mostraStatoOperazione(false, "Allenatore non trovato o non appartenente alla palestra.");
            return;
        }
        $this->eseguiAggiornamentoAbilitazioniInBlocco($allenatore, $_POST['attivita'] ?? [], $idLog);
    }

    private function eseguiAggiornamentoAbilitazioniInBlocco(Allenatore $allenatore, array $sel, int $idLog): void
    {
        try {
            foreach ($allenatore->getAttivitaAbilitate() as $c) {    // rimuove tutte le abilitazioni esistenti dell'allenatore prima di aggiungere le nuove selezionate, in modo da aggiornare completamente le attività abilitate
                $allenatore->removeAbilitazione($c);
            }
            $this->utenteRepo->save($allenatore);
            foreach ($sel as $idAtt) {
                $att = $this->attivitaRepo->findById((int)$idAtt);
                if ($att) $allenatore->addAbilitazione($att);
            }
            $this->utenteRepo->save($allenatore);
            $loc = ($allenatore->getId() === $idLog) ? 'profilo' : 'visualizza-profilo?id=' . $allenatore->getId();
            header('Location: ' . $loc);
            exit();
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Impossibile aggiornare abilitazioni: " . $e->getMessage());
        }
    }

    // =========================================================================
    // HELPER GENERALI
    // =========================================================================

    private function validaPalestraAllenatore(int $idLog, string $ruolo, Allenatore $allenatore): bool
    {
        if ($ruolo === 'amministratore') {
            $admin = $this->amministratoreRepo->findById($idLog);
            $pal = $this->palestraRepo->findByAmministratore($admin);
            if (!$pal || $allenatore->getPalestra()->getId() !== $pal->getId()) {
                return false;
            }
        }
        return true;
    }

    private function recuperaClienteTarget(string $ruolo, ?int $idUtente): ?Cliente
    {
        $targetId = $idUtente;
        if ($ruolo === 'amministratore' || $ruolo === 'allenatore') {
            $targetId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : null);
            if (!$targetId) return null;
        }
        $cliente = $this->clienteRepo->findById($targetId);
        if ($cliente && ($ruolo === 'amministratore' || $ruolo === 'allenatore')) {
            $pal = AttivitaPianificataController::recuperaPalestraUtenteStatic(
                $this->session,
                $this->utenteRepo,
                $this->palestraRepo,
                $this->clienteRepo
            );
            $palCliente = $this->recuperaPalestraUtente($cliente);
            if (!$pal || !$palCliente || $palCliente->getId() !== $pal->getId()) {
                return null;
            }
        }
        return $cliente;
    }

    private function recuperaPalestraUtente(Utente $utente): ?Palestra       //recupera la palestra associata a un utente, se esiste, altrimenti restituisce null
    {
        if ($utente instanceof Cliente) {
            return $utente->getPalestra();
        }
        if ($utente instanceof Allenatore) {
            return $utente->getPalestra();
        }
        if ($utente instanceof Amministratore) {
            return $this->palestraRepo->findByAmministratore($utente);
        }
        return null;
    }

    private function recuperaUtenteLoggato(EntityManagerInterface $entityManager, int $idUtente, ?string $ruolo): ?Utente
    {
        if ($ruolo === 'cliente') {
            return $this->clienteRepo->findById($idUtente);
        } elseif ($ruolo === 'allenatore') {
            return $this->allenatoreRepo->findById($idUtente);
        } elseif ($ruolo === 'amministratore') {
            return $this->amministratoreRepo->findById($idUtente);
        }
        return $this->utenteRepo->findById($idUtente);
    }

    private function mostraStatoOperazione(bool $successo, string $messaggio, ?string $ritorno = null, ?string $testoBottone = null): void
    {
        $statusView = new VisualizzazioneViewSmarty();
        $statusView->mostraStatoOperazione($successo, $messaggio, $ritorno, $testoBottone);
    }
}