<?php

namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\ParametriRepositoryInterface;
use App\Entity\Repository\CertificatoMedicoRepositoryInterface;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Entity\Repository\ProgressoRepositoryInterface;
use App\Entity\Repository\AttivitaRepositoryInterface;
use App\Entity\Repository\UtenteRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineParametriRepository;
use App\Foundation\Persistence\Repository\DoctrineCertificatoMedicoRepository;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\Foundation\Persistence\Repository\DoctrineProgressoRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaRepository;
use App\Foundation\Persistence\Repository\DoctrineUtenteRepository;
use App\View\Interface\ProfiloView;
use App\View\ProfiloViewSmarty;
use App\Foundation\Session;
use App\Entity\Parametri;
use App\Entity\CertificatoMedico;
use App\Infrastructure\Doctrine\EntityManagerFactory;
use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Utente;
use App\Entity\Cliente;
use Doctrine\ORM\EntityManagerInterface;

class ProfiloController 
{
    private ClienteRepositoryInterface $clienteRepo;
    private ParametriRepositoryInterface $parametriRepo;
    private CertificatoMedicoRepositoryInterface $certificatoRepo;
    private PalestraRepositoryInterface $palestraRepo;
    private ProgressoRepositoryInterface $progressoRepo;
    private AttivitaRepositoryInterface $attivitaRepo;
    private UtenteRepositoryInterface $utenteRepo;
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
        $this->view = new ProfiloViewSmarty();
    }

    /**
     * 1. VISUALIZZA PROFILO (Pagina 17 del mock-up UX)
     * Mostra anagrafica, dati abbonamento, storico parametri biometrici e certificato
     */
    public function visualizzaProfilo(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }

        $entityManager = EntityManagerFactory::create();
        $isSelf = !isset($_GET['id']);

        if ($isSelf) {
            $utente = $this->recuperaUtenteLoggato($entityManager, $idUtente, $ruolo);
            $isClient = ($utente instanceof Cliente);
        } else {
            $targetId = (int)$_GET['id'];
            $utente = $entityManager->find(Utente::class, $targetId);
            $isClient = ($utente instanceof Cliente);

            // Controllo di sicurezza (Anti-IDOR) per amministratore e allenatore
            if ($ruolo === 'amministratore' || $ruolo === 'allenatore') {
                $palestraUtente = null;
                if ($ruolo === 'amministratore') {
                    $adminObj = $entityManager->find(Amministratore::class, $idUtente);
                    $palestraUtente = $this->palestraRepo->findByAmministratore($adminObj);
                } else {
                    $allenatoreObj = $entityManager->find(Allenatore::class, $idUtente);
                    $palestraUtente = $allenatoreObj ? $allenatoreObj->getPalestra() : null;
                }

                // Verifica che il target sia presente, abbia una palestra e appartenga alla stessa palestra dell'utente loggato
                if (!$utente || !method_exists($utente, 'getPalestra') || !$utente->getPalestra() || !$palestraUtente || $utente->getPalestra()->getId() !== $palestraUtente->getId()) {
                    $this->view->mostraErrore("Accesso negato. Non sei autorizzato a visualizzare questo profilo.");
                    return;
                }
            }
        }

        if (!$utente) {
            $this->view->mostraErrore("Profilo non trovato.");
            return;
        }

        $isTrainer = ($utente instanceof Allenatore);

        if ($isClient) {
            /** @var Cliente $utente */
            $ultimiParametri = $this->parametriRepo->findUltimaByCliente($utente);
            $ultimoCertificato = ($ruolo === 'allenatore') ? null : $this->certificatoRepo->findByCliente($utente);
            $abbonamento = $utente->getAbbonamento();
            $abbonamentoAttivo = $utente->isAbbonamentoAttivo();
            
            // Verifica la presenza di progressi registrati
            $progressiCount = count($this->progressoRepo->findByCliente($utente));
            $hasProgress = ($progressiCount > 0);
        } else {
            $ultimiParametri = null;
            $ultimoCertificato = null;
            $abbonamento = null;
            $abbonamentoAttivo = false;
            $hasProgress = false;
        }

        $attivitaAbilitate = null;
        $attivitaNonAbilitate = [];
        $tutteAttivita = [];
        if ($isTrainer) {
            /** @var Allenatore $utente */
            $attivitaAbilitate = $utente->getAttivitaAbilitate();
            $tutteAttivita = $this->attivitaRepo->findAll();
            foreach ($tutteAttivita as $att) {
                if (!$attivitaAbilitate->contains($att)) {
                    $attivitaNonAbilitate[] = $att;
                }
            }
        }

        // Costruiamo l'array con i dati del profilo
        $datiProfilo = [
            'utente' => $utente,
            'isClient' => $isClient,
            'isTrainer' => $isTrainer,
            'has_progress' => $hasProgress,
            'attivitaAbilitate' => $attivitaAbilitate,
            'attivitaNonAbilitate' => $attivitaNonAbilitate,
            'tutteAttivita' => $tutteAttivita,
            'isSelf' => $isSelf,
            'nome' => $utente->getNome(),
            'cognome' => $utente->getCognome(),
            'email' => $utente->getEmail(),
            'cf' => $utente->getCF(),
            'fotoProfilo' => $utente->getProfilePicture() ? base64_encode($utente->getProfilePicture()) : null,
            'abbonamento' => $abbonamento,
            'abbonamento_attivo' => $abbonamentoAttivo,
            
            // Parametri Biometrici
            'parametri' => $ultimiParametri ? [
                'peso' => $ultimiParametri->getPeso(),
                'altezza' => $ultimiParametri->getAltezza(),
                'data' => $ultimiParametri->getData()->format('d/m/Y'),
                'bicipiteDestro' => $ultimiParametri->getBicipiteDestro(),
                'bicipiteSinistro' => $ultimiParametri->getBicipiteSinistro(),
                'tricipiteDestro' => $ultimiParametri->getTricipiteDestro(),
                'tricipiteSinistro' => $ultimiParametri->getTricipiteSinistro(),
                'cosciaDestra' => $ultimiParametri->getCosciaDestra(),
                'cosciaSinistra' => $ultimiParametri->getCosciaSinistra(),
                'polpaccioDestro' => $ultimiParametri->getPolpaccioDestro(),
                'polpaccioSinistro' => $ultimiParametri->getPolpaccioSinistro(),
                'misuraPetto' => $ultimiParametri->getMisuraPetto(),
                'misuraVita' => $ultimiParametri->getMisuraVita(),
                'misuraSpalle' => $ultimiParametri->getMisuraSpalle(),
                'misuraFianchi' => $ultimiParametri->getMisuraFianchi(),
            ] : null,

            // Certificato Medico
            'certificato' => $ultimoCertificato ? [
                'scadenza' => $ultimoCertificato->getDataScadenza()->format('d/m/Y'),
                'medico' => $ultimoCertificato->getMedico(),
                'valido' => $ultimoCertificato->isValido()
            ] : null
        ];

        $this->view->mostraProfilo($datiProfilo);
    }

    /**
     * 2. MODIFICA DATI (Pagina 18 del mock-up UX)
     * Aggiorna i recapiti: Domicilio, Residenza e Metodo di Pagamento
     */
    public function modificaAnagrafica(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        if (!$idUtente) {
            $this->view->mostraErrore("Sessione scaduta.");
            return;
        }

        $entityManager = EntityManagerFactory::create();
        $ruolo = $this->session->getLoggedUserRole();

        $isSelf = !isset($_GET['id']) && !isset($_POST['id']);
        if ($isSelf) {
            $utente = $this->recuperaUtenteLoggato($entityManager, $idUtente, $ruolo);
            $isClient = ($utente instanceof Cliente);
        } else {
            if ($ruolo === 'allenatore') {
                $this->view->mostraErrore("Accesso negato. Gli allenatori non possono modificare i dati degli altri utenti.");
                return;
            }

            $targetId = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];
            $utente = $entityManager->find(Utente::class, $targetId);
            $isClient = ($utente instanceof Cliente);

            // Security check (Anti-IDOR) for admin and trainer
            if ($ruolo === 'amministratore' || $ruolo === 'allenatore') {
                $palestraUtente = null;
                if ($ruolo === 'amministratore') {
                    $adminObj = $entityManager->find(Amministratore::class, $idUtente);
                    $palestraUtente = $this->palestraRepo->findByAmministratore($adminObj);
                } else {
                    $allenatoreObj = $entityManager->find(Allenatore::class, $idUtente);
                    $palestraUtente = $allenatoreObj ? $allenatoreObj->getPalestra() : null;
                }

                if (!$utente || !method_exists($utente, 'getPalestra') || !$utente->getPalestra() || !$palestraUtente || $utente->getPalestra()->getId() !== $palestraUtente->getId()) {
                    $this->view->mostraErrore("Accesso negato. Non sei autorizzato a modificare questo profilo.");
                    return;
                }
            }
        }

        if (!$utente) {
            $this->view->mostraErrore("Profilo inesistente.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormModifica([
                'utente' => $utente,
                'isClient' => $isClient,
                'isSelf' => $isSelf,
                'ruolo' => $ruolo
            ]);
            return;
        }

        // Recupero dei campi input text
        $nuovoNome = $_POST['nome'] ?? null;
        $nuovoCognome = $_POST['cognome'] ?? null;
        $nuovoIndirizzoResidenza = $_POST['indirizzo'] ?? null; // Ereditato da Utente
        $nuovoIndirizzoDomicilio = $_POST['indirizzo_domicilio'] ?? null; // Specifico di Cliente
        $nuovoMetodoPagamento = $_POST['metodo_pagamento'] ?? null;

        $ritorno = $isSelf ? 'profilo' : 'visualizza-profilo?id=' . $utente->getId();

        if (empty($nuovoNome) || empty($nuovoCognome) || empty($nuovoIndirizzoResidenza)) {
            $this->view->mostraErrore("I campi Nome, Cognome e Residenza sono obbligatori.", $ritorno, "Torna al Profilo");
            return;
        }

        if ($isClient && $ruolo === 'amministratore' && empty($nuovoMetodoPagamento)) {
            $this->view->mostraErrore("Il campo Metodo di Pagamento è obbligatorio per i clienti.", $ritorno, "Torna al Profilo");
            return;
        }

        try {
            // Aggiorniamo l'entità
            $utente->setNome($nuovoNome);
            $utente->setCognome($nuovoCognome);
            $utente->setIndirizzo($nuovoIndirizzoResidenza);
            if ($isClient) {
                /** @var Cliente $utente */
                if (method_exists($utente, 'setIndirizzoDiDomicilio')) {
                    $utente->setIndirizzoDiDomicilio($nuovoIndirizzoDomicilio);
                }
                if ($ruolo === 'amministratore') {
                    $utente->setMetodoDiPagamento($nuovoMetodoPagamento);
                }
            }

            $entityManager->flush();
            if ($isSelf) {
                header('Location: profilo');
            } else {
                header('Location: visualizza-profilo?id=' . $utente->getId());
            }
            exit();
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraErrore("Errore di validazione: " . $e->getMessage(), $ritorno, "Torna al Profilo");
        }
    }

    /**
     * 3. CARICA FOTO PROFILO
     */
    public function caricaFotoProfilo(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        if (!$idUtente) {
            $this->view->mostraErrore("Azione non consentita.");
            return;
        }

        $entityManager = EntityManagerFactory::create();
        $ruolo = $this->session->getLoggedUserRole();
        $utente = $this->recuperaUtenteLoggato($entityManager, $idUtente, $ruolo);
        if (!$utente) {
            $this->view->mostraErrore("Profilo non trovato.");
            return;
        }

        $ritorno = "profilo";

        if (isset($_FILES['foto_profilo'])) {
            $error = $_FILES['foto_profilo']['error'];
            if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                $this->view->mostraErrore("L'immagine caricata supera il limite massimo di dimensione consentito.", $ritorno, "Torna al Profilo");
                return;
            }
        }

        if (!isset($_FILES['foto_profilo']) || $_FILES['foto_profilo']['error'] !== UPLOAD_ERR_OK) {
            $this->view->mostraErrore("File immagine non valido.", $ritorno, "Torna al Profilo");
            return;
        }

        $fileTmpPath = $_FILES['foto_profilo']['tmp_name'];
        $fileSize = $_FILES['foto_profilo']['size'];

        // Limite massimo di 60 KB per stare all'interno del tipo BLOB del DB senza troncare l'immagine
        $maxSize = 60 * 1024;
        if ($fileSize > $maxSize) {
            $this->view->mostraErrore("L'immagine caricata supera il limite massimo di dimensione consentito (60 KB).", $ritorno, "Torna al Profilo");
            return;
        }

        // Validazione formato immagine (escludendo GIF ed altri formati non supportati)
        $imageInfo = @getimagesize($fileTmpPath);
        if ($imageInfo === false) {
            $this->view->mostraErrore("Il file caricato non è un'immagine valida.", $ritorno, "Torna al Profilo");
            return;
        }

        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imageInfo[2], $allowedTypes)) {
            $this->view->mostraErrore("Formato immagine non consentito. Sono ammessi solo JPG, JPEG e PNG.", $ritorno, "Torna al Profilo");
            return;
        }

        // Legge il contenuto binario del file temporaneo
        $fileContent = file_get_contents($fileTmpPath);

        if ($fileContent !== false) {
            $utente->setProfilePicture($fileContent);
            $entityManager->flush();
            $this->view->mostraConfermaModifica("Foto profilo aggiornata con successo.", $ritorno, "Torna al Profilo");
        } else {
            $this->view->mostraErrore("Impossibile leggere il contenuto del file immagine.", $ritorno, "Torna al Profilo");
        }
    }

    /**
     * 4. AGGIORNA MISURE CORPOREE (PARAMETRI BIOMETRICI)
     */
    public function aggiornaMisureCorporee(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente) {
            $this->view->mostraErrore("Effettua il login.");
            return;
        }

        $cliente = $this->recuperaClienteTarget($ruolo, $idUtente);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato o accesso non consentito.");
            return;
        }

        $ultimaMisure = $this->parametriRepo->findUltimaByCliente($cliente);
        $storicoMisure = $this->parametriRepo->findByCliente($cliente);
        $this->view->mostraFormMisure([
            'utente' => $cliente,
            'ultimaMisure' => $ultimaMisure,
            'storicoMisure' => $storicoMisure,
            'storicoMisureCronologico' => array_reverse($storicoMisure),
            'isSelf' => ($ruolo === 'cliente')
        ]);
    }

    /**
     * 4b. INSERISCI NUOVE MISURE CORPOREE
     */
    public function inserisciMisureCorporee(): void
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente) {
            $this->view->mostraErrore("Effettua il login.");
            return;
        }

        if ($ruolo === 'allenatore') {
            $this->view->mostraErrore("Accesso negato. Gli allenatori non possono modificare le misure dei clienti.");
            return;
        }

        $cliente = $this->recuperaClienteTarget($ruolo, $idUtente);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato o accesso non consentito.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $ultimaMisure = $this->parametriRepo->findUltimaByCliente($cliente);
            $this->view->mostraFormInserimentoMisure([
                'utente' => $cliente,
                'ultimaMisure' => $ultimaMisure
            ]);
            return;
        }

        $peso = isset($_POST['peso']) ? (float)$_POST['peso'] : 0.0;
        $altezza = isset($_POST['altezza']) ? (float)$_POST['altezza'] : 0.0;

        if ($peso <= 0 || $altezza <= 0) {
            $this->view->mostraErrore("Peso e altezza sono obbligatori.");
            return;
        }

        $bicipiteD = !empty($_POST['bicipite_destro']) ? (float)$_POST['bicipite_destro'] : null;
        $bicipiteS = !empty($_POST['bicipite_sinistro']) ? (float)$_POST['bicipite_sinistro'] : null;
        $tricipiteD = !empty($_POST['tricipite_destro']) ? (float)$_POST['tricipite_destro'] : null;
        $tricipiteS = !empty($_POST['tricipite_sinistro']) ? (float)$_POST['tricipite_sinistro'] : null;
        $cosciaD = !empty($_POST['coscia_destra']) ? (float)$_POST['coscia_destra'] : null;
        $cosciaS = !empty($_POST['coscia_sinistra']) ? (float)$_POST['coscia_sinistra'] : null;
        $polpaccioD = !empty($_POST['polpaccio_destro']) ? (float)$_POST['polpaccio_destro'] : null;
        $polpaccioS = !empty($_POST['polpaccio_sinistro']) ? (float)$_POST['polpaccio_sinistro'] : null;
        $petto = !empty($_POST['misura_petto']) ? (float)$_POST['misura_petto'] : null;
        $vita = !empty($_POST['misura_vita']) ? (float)$_POST['misura_vita'] : null;
        $spalle = !empty($_POST['misura_spalle']) ? (float)$_POST['misura_spalle'] : null;
        $fianchi = !empty($_POST['misura_fianchi']) ? (float)$_POST['misura_fianchi'] : null;

        try {
            $nuoviParametri = new Parametri(
                $peso,
                $altezza,
                new \DateTimeImmutable(), 
                $cliente,
                $bicipiteD,
                $bicipiteS,
                $tricipiteD,
                $tricipiteS,
                $cosciaD,
                $cosciaS,
                $polpaccioD,
                $polpaccioS,
                $petto,
                $vita,
                $spalle,
                $fianchi
            );

            $this->parametriRepo->salvaMisure($nuoviParametri);
            header('Location: aggiorna-misure' . ($ruolo !== 'cliente' ? '?id=' . $cliente->getId() : ''));
            exit();
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraErrore("Dati non validi: " . $e->getMessage());
        }
    }

    /**
     * 5. CARICA CERTIFICATO MEDICO (Inoltra il file PDF a Foundation)
     */
    public function caricaCertificato(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente || $ruolo === 'allenatore') {
            $this->view->mostraErrore("Accesso negato. Questa funzionalità è riservata ad Amministratori e Clienti.");
            return;
        }

        $cliente = $this->recuperaClienteTarget($ruolo, $idUtente);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato o accesso non consentito.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCertificato([
                'utente' => $cliente
            ]);
            return;
        }

        $ritorno = ($ruolo === 'cliente') ? 'profilo' : 'visualizza-profilo?id=' . $cliente->getId();

        // Controlla se l'upload ha superato il limite di post_max_size del server
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $this->view->mostraErrore("La dimensione del file supera il limite massimo consentito dal server (post_max_size). Riduci le dimensioni del file PDF.", $ritorno, "Torna al Profilo");
            return;
        }

        $medico = $_POST['medico'] ?? null;
        $dataEmissioneStr = $_POST['data_emissione'] ?? null;

        if (empty($medico) || empty($dataEmissioneStr)) {
            $this->view->mostraErrore("Dati del certificato incompleti. Assicurati di aver inserito il nome del medico e la data di emissione.", $ritorno, "Torna al Profilo");
            return;
        }

        if (!isset($_FILES['file_certificato']) || $_FILES['file_certificato']['error'] !== UPLOAD_ERR_OK) {
            $this->view->mostraErrore("File PDF del certificato mancante o corrotto.", $ritorno, "Torna al Profilo");
            return;
        }

        try {
            // Legge il contenuto binario del file temporaneo
            $fileTmpPath = $_FILES['file_certificato']['tmp_name'];
            $fileContent = file_get_contents($fileTmpPath);

            if ($fileContent === false) {
                throw new \Exception("Impossibile leggere il file del certificato.");
            }

            $vecchioCertificato = $cliente->getCertificatoMedico();

            $dataEmissione = new \DateTimeImmutable($dataEmissioneStr);
            $certificato = new CertificatoMedico($dataEmissione, $medico, $cliente, $fileContent);
            
            // Salva il nuovo certificato
            $this->certificatoRepo->save($certificato);

            // Associa il nuovo certificato al cliente (owner lato relazione)
            $cliente->setCertificatoMedico($certificato);
            $this->clienteRepo->save($cliente);

            // Elimina il vecchio certificato se presente
            if ($vecchioCertificato) {
                $this->certificatoRepo->delete($vecchioCertificato);
            }
            
            $this->view->mostraConfermaModifica("Certificato medico caricato correttamente. La nuova scadenza è il " . $certificato->getDataScadenza()->format('d/m/Y'), $ritorno, "Torna al Profilo");
        } catch (\Exception $e) {
            $this->view->mostraErrore("Errore nell'elaborazione del certificato: " . $e->getMessage(), $ritorno, "Torna al Profilo");
        }
    }

    public function cambiaPassword(): void
    {
        $idUtente = $this->session->getLoggedUserId();
        if (!$idUtente) {
            $this->view->mostraErrore("Sessione scaduta o non valida.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCambioPassword();
            return;
        }

        $oldPassword = $_POST['vecchia_password'] ?? '';
        $newPassword = $_POST['nuova_password'] ?? '';
        $confirmPassword = $_POST['conferma_password'] ?? '';

        $ritorno = "profilo";

        if ($oldPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $this->view->mostraErrore("Tutti i campi password sono obbligatori.", $ritorno, "Torna al Profilo");
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->view->mostraErrore("La nuova password e la password di conferma non coincidono.", $ritorno, "Torna al Profilo");
            return;
        }

        $entityManager = EntityManagerFactory::create();
        $ruolo = $this->session->getLoggedUserRole();
        $utente = $this->recuperaUtenteLoggato($entityManager, $idUtente, $ruolo);
        if (!$utente) {
            $this->view->mostraErrore("Utente non trovato.", $ritorno, "Torna al Profilo");
            return;
        }

        if (!$utente->verificaPassword($oldPassword)) {
            $this->view->mostraErrore("La vecchia password inserita non è corretta.", $ritorno, "Torna al Profilo");
            return;
        }

        try {
            $utente->setPassword($newPassword);
            $entityManager->flush();
            $this->view->mostraConfermaModifica("Password aggiornata con successo.", $ritorno, "Torna al Profilo");
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraErrore("Errore di validazione: " . $e->getMessage(), $ritorno, "Torna al Profilo");
        }
    }

    public function visualizzaGrafico(): void
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente) {
            $this->view->mostraErrore("Effettua il login.");
            return;
        }

        $cliente = $this->recuperaClienteTarget($ruolo, $idUtente);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato o accesso non consentito.");
            return;
        }

        $tipo = $_GET['tipo'] ?? 'peso';
        if (!in_array($tipo, ['peso', 'superiore', 'inferiore'])) {
            $tipo = 'peso';
        }

        $storicoMisure = $this->parametriRepo->findByCliente($cliente);
        $storico = array_reverse($storicoMisure); // Chronological order

        $punti = [];
        $width = 390;
        $height = 120;
        $padX = 40;
        $padY = 20;

        $valori = [];
        foreach ($storico as $m) {
            if ($tipo === 'peso') {
                $valori[] = $m->getPeso();
            } elseif ($tipo === 'superiore') {
                $subVals = [];
                if ($m->getBicipiteDestro() !== null) $subVals[] = $m->getBicipiteDestro();
                if ($m->getBicipiteSinistro() !== null) $subVals[] = $m->getBicipiteSinistro();
                if ($m->getTricipiteDestro() !== null) $subVals[] = $m->getTricipiteDestro();
                if ($m->getTricipiteSinistro() !== null) $subVals[] = $m->getTricipiteSinistro();
                if ($m->getMisuraPetto() !== null) $subVals[] = $m->getMisuraPetto();
                if ($m->getMisuraSpalle() !== null) $subVals[] = $m->getMisuraSpalle();
                
                $media = count($subVals) > 0 ? array_sum($subVals) / count($subVals) : 0.0;
                $valori[] = round($media, 2);
            } else {
                $subVals = [];
                if ($m->getCosciaDestra() !== null) $subVals[] = $m->getCosciaDestra();
                if ($m->getCosciaSinistra() !== null) $subVals[] = $m->getCosciaSinistra();
                if ($m->getPolpaccioDestro() !== null) $subVals[] = $m->getPolpaccioDestro();
                if ($m->getPolpaccioSinistro() !== null) $subVals[] = $m->getPolpaccioSinistro();
                if ($m->getMisuraVita() !== null) $subVals[] = $m->getMisuraVita();
                if ($m->getMisuraFianchi() !== null) $subVals[] = $m->getMisuraFianchi();
                
                $media = count($subVals) > 0 ? array_sum($subVals) / count($subVals) : 0.0;
                $valori[] = round($media, 2);
            }
        }

        $minVal = count($valori) ? min($valori) - 2 : 0;
        $maxVal = count($valori) ? max($valori) + 2 : 10;
        $range = $maxVal - $minVal ?: 1;

        $count = count($storico);
        foreach ($storico as $i => $m) {
            $val = $valori[$i];

            $x = $padX + ($i * ($width / ($count - 1 ?: 1)));
            $y = $padY + $height - (($val - $minVal) / $range * $height);
            $punti[] = [
                'x' => $x,
                'y' => $y,
                'valore' => $val,
                'data' => $m->getData()->format('d/m')
            ];
        }

        $titolo = "Andamento Peso Corporeo";
        if ($tipo === 'superiore') {
            $titolo = "Andamento Misure Parte Superiore (Media)";
        } elseif ($tipo === 'inferiore') {
            $titolo = "Andamento Misure Parte Inferiore (Media)";
        }

        $this->view->mostraGrafico([
            'utente' => $cliente,
            'tipo' => $tipo,
            'titolo' => $titolo,
            'punti' => $punti
        ]);
    }

    /**
     * Recupera l'utente Cliente target della richiesta, verificando i permessi di sicurezza (Anti-IDOR)
     */
    private function recuperaClienteTarget(string $ruolo, ?int $idUtente): ?Cliente
    {
        $targetId = $idUtente;
        if ($ruolo === 'amministratore' || $ruolo === 'allenatore') {
            $targetId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : null);
            if (!$targetId) {
                return null;
            }
        }

        $cliente = $this->clienteRepo->findById($targetId);
        if (!$cliente) {
            return null;
        }

        // Controllo multitenant
        if ($ruolo === 'amministratore' || $ruolo === 'allenatore') {
            $entityManager = EntityManagerFactory::create();
            $palestraUtente = null;
            if ($ruolo === 'amministratore') {
                $adminObj = $entityManager->find(Amministratore::class, $idUtente);
                $palestraUtente = $this->palestraRepo->findByAmministratore($adminObj);
            } else {
                $allenatoreObj = $entityManager->find(Allenatore::class, $idUtente);
                $palestraUtente = $allenatoreObj ? $allenatoreObj->getPalestra() : null;
            }

            if (!$palestraUtente || !$cliente->getPalestra() || $cliente->getPalestra()->getId() !== $palestraUtente->getId()) {
                return null;
            }
        }

        return $cliente;
    }

    /**
     * Recupera l'utente loggato instanziando la sua classe concreta specifica per evitare proxy casting issue
     */
    private function recuperaUtenteLoggato(EntityManagerInterface $entityManager, int $idUtente, ?string $ruolo): ?Utente
    {
        if ($ruolo === 'cliente') {
            return $entityManager->find(Cliente::class, $idUtente);
        } elseif ($ruolo === 'allenatore') {
            return $entityManager->find(Allenatore::class, $idUtente);
        } elseif ($ruolo === 'amministratore') {
            return $entityManager->find(Amministratore::class, $idUtente);
        }
        return $entityManager->find(Utente::class, $idUtente);
    }

    /**
     * Aggiunge un'attività abilitata a un allenatore dal suo profilo
     */
    public function aggiungiAttivitaAllenatore(): void
    {
        $idLoggato = $this->session->getLoggedUserId();
        $ruoloLoggato = $this->session->getLoggedUserRole();

        if (!$idLoggato || ($ruoloLoggato !== 'amministratore' && $ruoloLoggato !== 'allenatore')) {
            $this->view->mostraErrore("Azione non consentita.");
            return;
        }

        $idAllenatore = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;
        $idAttivita = isset($_POST['id_attivita']) ? (int)$_POST['id_attivita'] : 0;

        if ($idAllenatore <= 0 || $idAttivita <= 0) {
            $this->view->mostraErrore("Dati non validi.");
            return;
        }

        $entityManager = EntityManagerFactory::create();

        // Controllo permessi (Se non è admin, l'allenatore può modificare solo se stesso)
        if ($ruoloLoggato !== 'amministratore' && $idAllenatore !== $idLoggato) {
            $this->view->mostraErrore("Non sei autorizzato ad aggiungere attività a questo profilo.");
            return;
        }

        $allenatore = $entityManager->find(Allenatore::class, $idAllenatore);
        $attivita = $entityManager->find(\App\Entity\Attivita::class, $idAttivita);

        if (!$allenatore || !$attivita) {
            $this->view->mostraErrore("Allenatore o Attività non validi.");
            return;
        }

        // Se l'utente loggato è l'admin, controlliamo che l'allenatore sia nella palestra dell'admin
        if ($ruoloLoggato === 'amministratore') {
            $adminObj = $entityManager->find(Amministratore::class, $idLoggato);
            $palestraAdmin = $this->palestraRepo->findByAmministratore($adminObj);
            if (!$palestraAdmin || $allenatore->getPalestra()->getId() !== $palestraAdmin->getId()) {
                $this->view->mostraErrore("L'allenatore non appartiene alla tua palestra.");
                return;
            }
        }

        try {
            $allenatore->addAbilitazione($attivita);
            $entityManager->flush();

            // Redirect back to profile page
            if ($idAllenatore === $idLoggato) {
                header('Location: profilo');
            } else {
                header('Location: visualizza-profilo?id=' . $idAllenatore);
            }
            exit();
        } catch (\Throwable $e) {
            $this->view->mostraErrore("Impossibile aggiungere l'attività: " . $e->getMessage());
        }
    }

    /**
     * Rimuove un'attività abilitata da un allenatore dal suo profilo
     */
    public function rimuoviAttivitaAllenatore(): void
    {
        $idLoggato = $this->session->getLoggedUserId();
        $ruoloLoggato = $this->session->getLoggedUserRole();

        if (!$idLoggato || ($ruoloLoggato !== 'amministratore' && $ruoloLoggato !== 'allenatore')) {
            $this->view->mostraErrore("Azione non consentita.");
            return;
        }

        $idAllenatore = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;
        $idAttivita = isset($_POST['id_attivita']) ? (int)$_POST['id_attivita'] : 0;

        if ($idAllenatore <= 0 || $idAttivita <= 0) {
            $this->view->mostraErrore("Dati non validi.");
            return;
        }

        $entityManager = EntityManagerFactory::create();

        // Controllo permessi (Se non è admin, l'allenatore può modificare solo se stesso)
        if ($ruoloLoggato !== 'amministratore' && $idAllenatore !== $idLoggato) {
            $this->view->mostraErrore("Non sei autorizzato a rimuovere attività da questo profilo.");
            return;
        }

        $allenatore = $entityManager->find(Allenatore::class, $idAllenatore);
        $attivita = $entityManager->find(\App\Entity\Attivita::class, $idAttivita);

        if (!$allenatore || !$attivita) {
            $this->view->mostraErrore("Allenatore o Attività non validi.");
            return;
        }

        // Se l'utente loggato è l'admin, controlliamo che l'allenatore sia nella palestra dell'admin
        if ($ruoloLoggato === 'amministratore') {
            $adminObj = $entityManager->find(Amministratore::class, $idLoggato);
            $palestraAdmin = $this->palestraRepo->findByAmministratore($adminObj);
            if (!$palestraAdmin || $allenatore->getPalestra()->getId() !== $palestraAdmin->getId()) {
                $this->view->mostraErrore("L'allenatore non appartiene alla tua palestra.");
                return;
            }
        }

        try {
            $allenatore->removeAbilitazione($attivita);
            $entityManager->flush();

            // Redirect back to profile page
            if ($idAllenatore === $idLoggato) {
                header('Location: profilo');
            } else {
                header('Location: visualizza-profilo?id=' . $idAllenatore);
            }
            exit();
        } catch (\Throwable $e) {
            $this->view->mostraErrore("Impossibile rimuovere l'attività: " . $e->getMessage());
        }
    }

    /**
     * Aggiorna in blocco le attività abilitate di un allenatore dal suo profilo
     */
    public function aggiornaAbilitazioniAllenatore(): void
    {
        $idLoggato = $this->session->getLoggedUserId();
        $ruoloLoggato = $this->session->getLoggedUserRole();

        if (!$idLoggato || ($ruoloLoggato !== 'amministratore' && $ruoloLoggato !== 'allenatore')) {
            $this->view->mostraErrore("Azione non consentita.");
            return;
        }

        $idAllenatore = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;
        $attivitaSelezionate = isset($_POST['attivita']) ? $_POST['attivita'] : []; // Array of IDs

        if ($idAllenatore <= 0) {
            $this->view->mostraErrore("Dati non validi.");
            return;
        }

        $entityManager = EntityManagerFactory::create();

        // Controllo permessi (Se non è admin, l'allenatore può modificare solo se stesso)
        if ($ruoloLoggato !== 'amministratore' && $idAllenatore !== $idLoggato) {
            $this->view->mostraErrore("Non sei autorizzato a modificare questo profilo.");
            return;
        }

        $allenatore = $entityManager->find(Allenatore::class, $idAllenatore);
        if (!$allenatore) {
            $this->view->mostraErrore("Allenatore non trovato.");
            return;
        }

        // Se l'utente loggato è l'admin, controlliamo che l'allenatore sia nella palestra dell'admin
        if ($ruoloLoggato === 'amministratore') {
            $adminObj = $entityManager->find(Amministratore::class, $idLoggato);
            $palestraAdmin = $this->palestraRepo->findByAmministratore($adminObj);
            if (!$palestraAdmin || $allenatore->getPalestra()->getId() !== $palestraAdmin->getId()) {
                $this->view->mostraErrore("L'allenatore non appartiene alla tua palestra.");
                return;
            }
        }

        try {
            // Rimuoviamo tutte le abilitazioni correnti
            $correnti = $allenatore->getAttivitaAbilitate();
            foreach ($correnti as $c) {
                $allenatore->removeAbilitazione($c);
            }
            $entityManager->flush();

            // Aggiungiamo le nuove abilitazioni selezionate
            foreach ($attivitaSelezionate as $idAtt) {
                $att = $entityManager->find(\App\Entity\Attivita::class, (int)$idAtt);
                if ($att) {
                    $allenatore->addAbilitazione($att);
                }
            }
            $entityManager->flush();

            // Redirect back to profile page
            if ($idAllenatore === $idLoggato) {
                header('Location: profilo');
            } else {
                header('Location: visualizza-profilo?id=' . $idAllenatore);
            }
            exit();
        } catch (\Throwable $e) {
            $this->view->mostraErrore("Impossibile aggiornare le abilitazioni: " . $e->getMessage());
        }
    }
}