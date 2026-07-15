<?php

namespace App\Control;

use App\Entity\Repository\AttivitaPianificataRepositoryInterface;
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\SessionePrivataRepositoryInterface;
use App\Entity\Repository\SalaRepositoryInterface;
use App\Entity\Repository\AttivitaRepositoryInterface;
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineAttivitaPianificataRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineSessionePrivataRepository;
use App\Foundation\Persistence\Repository\DoctrineSalaRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\Foundation\Persistence\Type\DateTimeImmutableStringable;
use App\Entity\AttivitaPianificata;
use App\Entity\SessionePrivata;
use App\Entity\Cliente;
use App\Entity\Allenatore;
use App\Entity\Amministratore;
use App\Entity\Palestra;
use App\Entity\Attivita;
use App\Entity\Sala;
use App\Entity\CodaAttesa;
use App\Entity\Messaggio;
use App\View\Interface\AttivitaPianificataView;
use App\View\AttivitaPianificataViewSmarty;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;

class AttivitaPianificataController
{
    private AttivitaPianificataRepositoryInterface $attivitaPianificataRepo;
    private ClienteRepositoryInterface $clienteRepo;
    private SessionePrivataRepositoryInterface $sessionePrivataRepo;
    private SalaRepositoryInterface $salaRepo;
    private AttivitaRepositoryInterface $attivitaRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private AttivitaPianificataView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->attivitaPianificataRepo = new DoctrineAttivitaPianificataRepository($this->entityManager);
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->sessionePrivataRepo = new DoctrineSessionePrivataRepository($this->entityManager);
        $this->salaRepo = new DoctrineSalaRepository($this->entityManager);
        $this->attivitaRepo = new DoctrineAttivitaRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
        $this->view = new AttivitaPianificataViewSmarty();
    }

    /**
     * Recupera la palestra associata all'utente correntemente loggato.
     */
    private function recuperaPalestraUtente(): ?Palestra
    {
        $idUtente = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUtente || !$ruolo) {
            return null;
        }

        if ($ruolo === 'amministratore') {
            $admin = $this->entityManager->find(Amministratore::class, $idUtente);
            if ($admin) {
                return $this->entityManager->getRepository(Palestra::class)->findOneBy(['amministratore' => $admin]);
            }
        } elseif ($ruolo === 'allenatore') {
            $allenatore = $this->entityManager->find(Allenatore::class, $idUtente);
            if ($allenatore) {
                return $allenatore->getPalestra();
            }
        } elseif ($ruolo === 'cliente') {
            $cliente = $this->entityManager->find(Cliente::class, $idUtente);
            if ($cliente) {
                return $cliente->getPalestra();
            }
        }

        return null;
    }



    /**
     * Visualizza il planner o calendario settimanale
     */
    public function visualizzaCalendario(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Nessuna palestra associata all'utente.");
            return;
        }

        $ruolo = $this->session->getLoggedUserRole();
        $idUtente = $this->session->getLoggedUserId();

        // 1. Calcola i giorni della settimana selezionata o corrente (da Lunedì a Domenica)
        $dataStr = isset($_GET['data']) ? trim($_GET['data']) : 'today';
        try {
            $oggi = new \DateTimeImmutable($dataStr);
        } catch (\Throwable $e) {
            $oggi = new \DateTimeImmutable('today');
        }
        $giornoSettimanaCorrente = (int)$oggi->format('N'); // 1 = Lun, ..., 7 = Dom
        $lunedi = $oggi->modify('-' . ($giornoSettimanaCorrente - 1) . ' days');
        
        $giorniSettimana = [];
        $dateSettimanaStr = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $lunedi->modify('+' . $i . ' days');
            $giorniSettimana[] = $d;
            $dateSettimanaStr[] = $d->format('Y-m-d');
        }

        // Calcola le date della settimana precedente e successiva per la navigazione
        $dataPrecedente = $lunedi->modify('-7 days')->format('Y-m-d');
        $dataSuccessiva = $lunedi->modify('+7 days')->format('Y-m-d');
        $dataCorrente = $lunedi->format('Y-m-d');

        // Calcola mese e anno in italiano della settimana
        $mesi = [
            1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
            5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
            9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre'
        ];
        $meseNum = (int)$lunedi->format('n');
        $anno = $lunedi->format('Y');
        $meseAnno = $mesi[$meseNum] . ' ' . $anno;

        // 2. Carica tutte le attività pianificate e filtra per la palestra dell'utente loggato
        $tutte = $this->attivitaPianificataRepo->findAll();
        $attivitaPianificate = array_filter($tutte, function(AttivitaPianificata $ap) use ($palestra) {
            return $ap->getSala()->getPalestra()->getId() === $palestra->getId();
        });

        // Filtro per la palestra dell'utente loggato completo

        // 3. Carica le sessioni private se l'utente è Cliente o Allenatore
        $sessioniPrivateSettimana = [];
        if ($ruolo === 'cliente') {
            $cliente = $this->clienteRepo->findById($idUtente);
            if ($cliente) {
                $privateRaw = $this->sessionePrivataRepo->findByCliente($cliente);
                foreach ($privateRaw as $sp) {
                    $spDateStr = $sp->getData()->format('Y-m-d');
                    if (in_array($spDateStr, $dateSettimanaStr)) {
                        $sessioniPrivateSettimana[] = $sp;
                    }
                }
            }
        } elseif ($ruolo === 'allenatore') {
            $allenatore = $this->entityManager->find(Allenatore::class, $idUtente);
            if ($allenatore) {
                $privateRaw = $this->sessionePrivataRepo->findByAllenatore($allenatore);
                foreach ($privateRaw as $sp) {
                    $spDateStr = $sp->getData()->format('Y-m-d');
                    if (in_array($spDateStr, $dateSettimanaStr)) {
                        $sessioniPrivateSettimana[] = $sp;
                    }
                }
            }
        }

        // 4. Raggruppa tutte le attività (pubbliche e private) per Giorno (1-7) e Orario (8-20)
        $grid = [];
        $fasceOrarie = range(8, 20); // Dalle 8:00 alle 20:00
        foreach ($fasceOrarie as $ora) {
            $grid[$ora] = array_fill(1, 7, []);
        }

        // Inserisci le attività pianificate nella griglia
        foreach ($attivitaPianificate as $ap) {
            $giornoDateStr = $ap->getGiorno()->format('Y-m-d');
            $dayIndex = array_search($giornoDateStr, $dateSettimanaStr);
            if ($dayIndex !== false) {
                $giornoN = $dayIndex + 1; // 1-7
                $ora = $ap->getOrario();
                if (isset($grid[$ora])) {
                    $grid[$ora][$giornoN][] = $ap;
                }
            }
        }

        // Inserisci le sessioni private nella griglia
        foreach ($sessioniPrivateSettimana as $sp) {
            $giornoDateStr = $sp->getData()->format('Y-m-d');
            $dayIndex = array_search($giornoDateStr, $dateSettimanaStr);
            if ($dayIndex !== false) {
                $giornoN = $dayIndex + 1; // 1-7
                $ora = (int)$sp->getOraInizio()->format('H');
                if (isset($grid[$ora])) {
                    $grid[$ora][$giornoN][] = $sp;
                }
            }
        }

        // 5. Gestione pannello di dettaglio/modifica (Split Panel)
        $selectedAp = null;
        $idAp = isset($_GET['id_ap']) ? (int)$_GET['id_ap'] : 0;
        if ($idAp > 0) {
            $selectedAp = $this->attivitaPianificataRepo->findById($idAp);
            if ($selectedAp && $selectedAp->getSala()->getPalestra()->getId() !== $palestra->getId()) {
                $selectedAp = null;
            }
        }

        $selectedSp = null;
        $selAllenatoreId = isset($_GET['sel_allenatore']) ? (int)$_GET['sel_allenatore'] : 0;
        $selOraInizio = isset($_GET['sel_ora_inizio']) ? trim($_GET['sel_ora_inizio']) : '';
        $selOraFine = isset($_GET['sel_ora_fine']) ? trim($_GET['sel_ora_fine']) : '';
        if ($selAllenatoreId > 0 && $selOraInizio !== '' && $selOraFine !== '') {
            $selAllenatore = $this->entityManager->find(Allenatore::class, $selAllenatoreId);
            if ($selAllenatore) {
                try {
                    $oraInizioObj = new DateTimeImmutableStringable($selOraInizio);
                    $oraFineObj = new DateTimeImmutableStringable($selOraFine);
                    $selectedSp = $this->sessionePrivataRepo->findByChiave($selAllenatore, $oraInizioObj, $oraFineObj);
                    
                    // Controllo accessi per la sessione privata caricata
                    if ($selectedSp) {
                        $isAuthorized = false;
                        if ($ruolo === 'allenatore' && $idUtente === $selectedSp->getAllenatore()->getId()) {
                            $isAuthorized = true;
                        } elseif ($ruolo === 'cliente' && $idUtente === $selectedSp->getAtleta()->getId()) {
                            $isAuthorized = true;
                        }
                        if (!$isAuthorized) {
                            $selectedSp = null;
                        }
                    }
                } catch (\Throwable $e) {
                    $selectedSp = null;
                }
            }
        }

        $codaAttesaAp = [];
        if ($selectedAp) {
            $codaAttesaAp = $this->entityManager->getRepository(CodaAttesa::class)->findBy(
                ['attivitaPianificata' => $selectedAp],
                ['dataInserimento' => 'ASC']
            );
        }

        $datiView = [
            'grid' => $grid,
            'fasceOrarie' => $fasceOrarie,
            'giorniSettimana' => $giorniSettimana,
            'dataPrecedente' => $dataPrecedente,
            'dataSuccessiva' => $dataSuccessiva,
            'dataCorrente' => $dataCorrente,
            'meseAnno' => $meseAnno,
            'ruolo_utente' => $ruolo,
            'sale' => $this->salaRepo->findByPalestra($palestra),
            'allenatori' => $this->allenatoreRepo->findByPalestra($palestra),
            'attivita' => $this->attivitaRepo->findAll(),
            'clienti' => $this->clienteRepo->findByPalestra($palestra),
            'selectedAp' => $selectedAp,
            'codaAttesa' => $codaAttesaAp,
            'selectedSp' => $selectedSp,
            'nuovo' => isset($_GET['nuovo']) ? 1 : 0,
            'nuova_sessione' => isset($_GET['nuova_sessione']) ? 1 : 0
        ];

        // Dettagli specifici per il Cliente
        if ($ruolo === 'cliente') {
            $cliente = $this->clienteRepo->findById($idUtente);
            if ($cliente) {
                $datiView['cliente'] = $cliente;
                $iscrittoMap = [];
                $inQueueMap = [];
                foreach ($attivitaPianificate as $ap) {
                    $iscrittoMap[$ap->getId()] = $this->clienteRepo->isIscrittoAAttivita($cliente, $ap);
                    $inQueueMap[$ap->getId()] = (bool)$this->entityManager->getRepository(CodaAttesa::class)->findOneBy([
                        'cliente' => $cliente,
                        'attivitaPianificata' => $ap
                    ]);
                }
                $datiView['iscrittoMap'] = $iscrittoMap;
                $datiView['inQueueMap'] = $inQueueMap;
                $datiView['puoPrenotare'] = $cliente->puoPrenotareAttivita();
            }
        }

        $this->view->mostraCalendario($datiView);
    }

    /**
     * Creazione / Pianificazione Attività Pianificata (con ripetizione)
     */
    public function creaAttivitaPianificata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $ruolo = $this->session->getLoggedUserRole();
        if ($ruolo !== 'amministratore') {
            $this->view->mostraStatoOperazione(false, "Solo l'amministratore può pianificare corsi.");
            return;
        }

        $idAttivita = isset($_POST['id_attivita']) ? (int)$_POST['id_attivita'] : 0;
        $nuovaAttivitaNome = !empty($_POST['nuova_attivita_nome']) ? trim($_POST['nuova_attivita_nome']) : '';
        $nuovaAttivitaDesc = !empty($_POST['nuova_attivita_desc']) ? trim($_POST['nuova_attivita_desc']) : '';
        $nuovaAttivitaMax = isset($_POST['nuova_attivita_max']) ? (int)$_POST['nuova_attivita_max'] : 0;

        $nuovaSalaNome = !empty($_POST['nuova_sala_nome']) ? trim($_POST['nuova_sala_nome']) : '';
        $nuovaSalaMax = isset($_POST['nuova_sala_max']) ? (int)$_POST['nuova_sala_max'] : 0;

        $dataStr = !empty($_POST['data']) ? trim($_POST['data']) : '';
        $orario = isset($_POST['orario']) ? (int)$_POST['orario'] : 0;
        $idSala = isset($_POST['id_sala']) ? (int)$_POST['id_sala'] : 0;
        $idAllenatore = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;

        $ripetizioni = isset($_POST['ripetizione']) ? $_POST['ripetizione'] : [];

        $ritorno = ($dataStr !== '') ? "calendario?data=" . $dataStr : "calendario";

        if ($dataStr === '' || $orario < 8 || $orario > 20 || $idAllenatore <= 0) {
            $this->view->mostraStatoOperazione(false, "Tutti i campi principali sono obbligatori e l'orario deve essere valido (8-20).", $ritorno, "Torna al Calendario");
            return;
        }

        $attivita = null;
        if ($idAttivita <= 0) {
            if ($nuovaAttivitaNome === '' || $nuovaAttivitaMax <= 0) {
                $this->view->mostraStatoOperazione(false, "Per inserire un nuovo corso scrivi il nome e indica un limite massimo di partecipanti valido.", $ritorno, "Torna al Calendario");
                return;
            }

            if ($this->attivitaRepo->existsByNome($nuovaAttivitaNome)) {
                $this->view->mostraStatoOperazione(false, "L'attività '" . $nuovaAttivitaNome . "' esiste già nel catalogo. Selezionala dal menu a discesa.", $ritorno, "Torna al Calendario");
                return;
            }

            try {
                $attivita = new Attivita($nuovaAttivitaNome, $nuovaAttivitaDesc, $nuovaAttivitaMax);
                $this->attivitaRepo->save($attivita);
            } catch (\Throwable $e) {
                $this->view->mostraStatoOperazione(false, "Errore durante la creazione del corso nel catalogo: " . $e->getMessage(), $ritorno, "Torna al Calendario");
                return;
            }
        } else {
            $attivita = $this->attivitaRepo->findById($idAttivita);
        }

        if (!$attivita) {
            $this->view->mostraStatoOperazione(false, "Corso non valido o non specificato.", $ritorno, "Torna al Calendario");
            return;
        }

        $sala = null;
        if ($idSala <= 0) {
            if ($nuovaSalaNome === '' || $nuovaSalaMax <= 0) {
                $this->view->mostraStatoOperazione(false, "Per inserire una nuova sala scrivi il nome e indica una capienza massima valida.", $ritorno, "Torna al Calendario");
                return;
            }

            if ($this->salaRepo->existsByNomeAndPalestra($nuovaSalaNome, $palestra)) {
                $this->view->mostraStatoOperazione(false, "La sala '" . $nuovaSalaNome . "' esiste già nella tua palestra. Selezionala dal menu a discesa.", $ritorno, "Torna al Calendario");
                return;
            }

            try {
                $sala = new Sala($nuovaSalaNome, $nuovaSalaMax, $palestra);
                $this->salaRepo->save($sala);
            } catch (\Throwable $e) {
                $this->view->mostraStatoOperazione(false, "Errore durante la creazione della sala: " . $e->getMessage(), $ritorno, "Torna al Calendario");
                return;
            }
        } else {
            $sala = $this->salaRepo->findById($idSala);
        }

        if (!$sala) {
            $this->view->mostraStatoOperazione(false, "Sala non valida o non specificata.", $ritorno, "Torna al Calendario");
            return;
        }

        $allenatore = $this->allenatoreRepo->findById($idAllenatore);

        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.");
            return;
        }

        if ($sala->getPalestra()->getId() !== $palestra->getId() || $allenatore->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Le risorse indicate non appartengono alla tua palestra.");
            return;
        }

        try {
            $startDate = new \DateTime($dataStr);

            if (!empty($ripetizioni)) {
                for ($i = 0; $i < 28; $i++) {
                    $currentDate = clone $startDate;
                    $currentDate->modify("+$i days");
                    $nGiorno = (int)$currentDate->format('N');

                    if (in_array((string)$nGiorno, $ripetizioni)) {
                        $giornoImmutable = \DateTimeImmutable::createFromMutable($currentDate);
                        
                        $esistente = $this->entityManager->getRepository(AttivitaPianificata::class)->findOneBy([
                            'giorno' => $giornoImmutable,
                            'orario' => $orario,
                            'sala' => $sala
                        ]);

                        if (!$esistente) {
                            $ap = new AttivitaPianificata($giornoImmutable, $orario, $sala, $allenatore, $attivita);
                            $this->entityManager->persist($ap);
                        }
                    }
                }
            } else {
                $giornoImmutable = \DateTimeImmutable::createFromMutable($startDate);
                $esistente = $this->entityManager->getRepository(AttivitaPianificata::class)->findOneBy([
                    'giorno' => $giornoImmutable,
                    'orario' => $orario,
                    'sala' => $sala
                ]);

                if ($esistente) {
                    $this->view->mostraStatoOperazione(false, "In questa sala è già pianificato un corso per il giorno e l'ora specificati.", $ritorno, "Torna al Calendario");
                    return;
                }

                $ap = new AttivitaPianificata($giornoImmutable, $orario, $sala, $allenatore, $attivita);
                $this->entityManager->persist($ap);
            }

            $this->entityManager->flush();
            $this->view->mostraStatoOperazione(true, "Corso pianificato con successo nel calendario.", $ritorno, "Torna al Calendario");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile completare la pianificazione: " . $e->getMessage(), $ritorno, "Torna al Calendario");
        }
    }

    /**
     * Rimozione di un'attività pianificata
     */
    public function rimuoviAttivitaPianificata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $ruolo = $this->session->getLoggedUserRole();
        if ($ruolo !== 'amministratore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Solo l'amministratore può eliminare corsi pianificati.");
            return;
        }

        $id = isset($_REQUEST['id_attivita_pianificata']) ? (int)$_REQUEST['id_attivita_pianificata'] : 0;
        $ap = $this->attivitaPianificataRepo->findById($id);

        if (!$ap) {
            $this->view->mostraStatoOperazione(false, "Attività pianificata non trovata.");
            return;
        }

        if ($ap->getSala()->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        try {
            foreach ($ap->getUtenti() as $cliente) {
                $cliente->cancellaIscrizioneAttivita($ap);
            }
            $this->attivitaPianificataRepo->delete($ap);
            $this->view->mostraStatoOperazione(true, "Attività pianificata rimossa con successo dal calendario.");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile rimuovere l'attività pianificata: " . $e->getMessage());
        }
    }

    /**
     * Prenota un'attività pianificata (Iscrizione)
     */
    public function prenotaAttivita(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $ruolo = $this->session->getLoggedUserRole();
        $idAttivita = isset($_REQUEST['id_attivita_pianificata']) ? (int)$_REQUEST['id_attivita_pianificata'] : 0;

        $attivita = $this->attivitaPianificataRepo->findById($idAttivita);
        if (!$attivita) {
            $this->view->mostraStatoOperazione(false, "Attività pianificata non trovata.");
            return;
        }

        if ($attivita->getSala()->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $cliente = null;

        if ($ruolo === 'cliente') {
            $idCliente = $this->session->getLoggedUserId();
            $cliente = $this->clienteRepo->findById($idCliente);
        } elseif ($ruolo === 'amministratore') {
            $idCliente = isset($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : 0;
            $cliente = $this->clienteRepo->findById($idCliente);
        } else {
            $this->view->mostraStatoOperazione(false, "Azione non consentita.");
            return;
        }

        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non valido.");
            return;
        }

        if ($cliente->getPalestra() === null || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Il cliente specificato non appartiene alla tua palestra.");
            return;
        }

        if ($this->clienteRepo->isIscrittoAAttivita($cliente, $attivita)) {
            $this->view->mostraStatoOperazione(false, "Il cliente risulta già iscritto a questa attività pianificata.");
            return;
        }

        if (!$cliente->puoPrenotareAttivita()) {
            $this->view->mostraStatoOperazione(false, "Impossibile completare la prenotazione: il cliente deve avere un abbonamento attivo e un certificato medico valido.");
            return;
        }

        if ($attivita->getPrenotati() >= $attivita->getMaxPartecipanti()) {
            // Se la capienza è al massimo, inseriamo nella coda di attesa
            $existingQueue = $this->entityManager->getRepository(CodaAttesa::class)->findOneBy([
                'cliente' => $cliente,
                'attivitaPianificata' => $attivita
            ]);
            if ($existingQueue) {
                $this->view->mostraStatoOperazione(false, "Sei già inserito nella coda di attesa per questa attività.");
                return;
            }

            try {
                $coda = new CodaAttesa($cliente, $attivita);
                $this->entityManager->persist($coda);
                $this->entityManager->flush();
                $this->view->mostraStatoOperazione(true, "L'attività ha raggiunto la capienza massima. Sei stato inserito nella coda di attesa.", "calendario?data=" . $attivita->getGiorno()->format('Y-m-d'));
            } catch (\Throwable $e) {
                $this->view->mostraStatoOperazione(false, "Impossibile inserire nella coda di attesa: " . $e->getMessage());
            }
            return;
        }

        try {
            $cliente->iscriviAAttivita($attivita);
            $attivita->setPrenotati($attivita->getPrenotati() + 1);

            $this->entityManager->flush();
            $this->view->mostraStatoOperazione(true, "Iscrizione registrata con successo.", "calendario?data=" . $attivita->getGiorno()->format('Y-m-d'));
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile salvare la prenotazione: " . $e->getMessage());
        }
    }

    /**
     * Cancella la prenotazione a un'attività pianificata
     */
    public function disdiciPrenotazione(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $ruolo = $this->session->getLoggedUserRole();
        $idAttivita = isset($_REQUEST['id_attivita_pianificata']) ? (int)$_REQUEST['id_attivita_pianificata'] : 0;

        $attivita = $this->attivitaPianificataRepo->findById($idAttivita);
        if (!$attivita) {
            $this->view->mostraStatoOperazione(false, "Attività pianificata non trovata.");
            return;
        }

        if ($attivita->getSala()->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $cliente = null;

        if ($ruolo === 'cliente') {
            $idCliente = $this->session->getLoggedUserId();
            $cliente = $this->clienteRepo->findById($idCliente);
        } elseif ($ruolo === 'amministratore') {
            $idCliente = isset($_REQUEST['id_cliente']) ? (int)$_REQUEST['id_cliente'] : 0;
            $cliente = $this->clienteRepo->findById($idCliente);
        } else {
            $this->view->mostraStatoOperazione(false, "Azione non consentita.");
            return;
        }

        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non valido.");
            return;
        }

        if ($cliente->getPalestra() === null || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $inQueue = $this->entityManager->getRepository(CodaAttesa::class)->findOneBy([
            'cliente' => $cliente,
            'attivitaPianificata' => $attivita
        ]);

        if (!$this->clienteRepo->isIscrittoAAttivita($cliente, $attivita)) {
            if ($inQueue) {
                try {
                    $this->entityManager->remove($inQueue);
                    $this->entityManager->flush();
                    $this->view->mostraStatoOperazione(true, "Sei stato rimosso dalla coda di attesa.", "calendario?data=" . $attivita->getGiorno()->format('Y-m-d'));
                } catch (\Throwable $e) {
                    $this->view->mostraStatoOperazione(false, "Impossibile rimuovere dalla coda di attesa: " . $e->getMessage());
                }
                return;
            }

            $this->view->mostraStatoOperazione(false, "Il cliente non risulta iscritto o in coda per questa attività.");
            return;
        }

        try {
            $cliente->cancellaIscrizioneAttivita($attivita);
            $attivita->setPrenotati(max(0, $attivita->getPrenotati() - 1));

            // SCORRIMENTO DELLA CODA:
            $codaRepo = $this->entityManager->getRepository(CodaAttesa::class);
            $codaPrimo = $codaRepo->findOneBy(
                ['attivitaPianificata' => $attivita],
                ['dataInserimento' => 'ASC']
            );

            if ($codaPrimo) {
                $clienteScelto = $codaPrimo->getCliente();
                $clienteScelto->iscriviAAttivita($attivita);
                $attivita->setPrenotati($attivita->getPrenotati() + 1);

                // Rimuovi dalla coda
                $this->entityManager->remove($codaPrimo);

                // Invia messaggio in bacheca
                $mittente = $attivita->getAllenatore();
                $oggettoMsg = "Iscrizione automatica all'attività";
                $contenutoMsg = "Ciao " . $clienteScelto->getNome() . ",\n\nti informiamo che si è liberato un posto e sei stato iscritto automaticamente all'attività: " . $attivita->getAttivita()->getNome() . " in data " . $attivita->getGiorno()->format('d/m/Y') . " alle ore " . $attivita->getOrario() . ":00.\n\nSaluti,\nLo staff di GymFly";
                
                $messaggio = new Messaggio($mittente, $oggettoMsg, $contenutoMsg);
                $messaggio->aggiungiDestinatario($clienteScelto);
                $this->entityManager->persist($messaggio);

                // Invia email di notifica (se SMTP non è configurato non fa nulla)
                $headers = "From: no-reply@gymfly.com\r\nReply-To: support@gymfly.com\r\nContent-Type: text/plain; charset=utf-8";
                @mail($clienteScelto->getEmail(), $oggettoMsg, $contenutoMsg, $headers);
            }

            $this->entityManager->flush();
            $this->view->mostraStatoOperazione(true, "Iscrizione cancellata con successo.", "calendario?data=" . $attivita->getGiorno()->format('Y-m-d'));
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile cancellare l'iscrizione: " . $e->getMessage());
        }
    }

    /**
     * Prenotazione di una sessione privata da parte di un allenatore
     */
    public function prenotaSessionePrivata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $ruolo = $this->session->getLoggedUserRole();
        if ($ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Solo l'allenatore può pianificare sessioni private.");
            return;
        }

        $idAllenatore = $this->session->getLoggedUserId();
        $allenatore = $this->entityManager->find(Allenatore::class, $idAllenatore);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $clienti = $this->clienteRepo->findByPalestra($palestra);
            $this->view->mostraFormPrenotaSessionePrivata([
                'clienti' => $clienti
            ]);
            return;
        }

        $idCliente = isset($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : 0;
        $dataStr = !empty($_POST['data']) ? trim($_POST['data']) : '';
        $oraInizioStr = !empty($_POST['ora_inizio']) ? trim($_POST['ora_inizio']) : '';
        $oraFineStr = !empty($_POST['ora_fine']) ? trim($_POST['ora_fine']) : '';

        if ($idCliente <= 0 || $dataStr === '' || $oraInizioStr === '' || $oraFineStr === '') {
            $this->view->mostraStatoOperazione(false, "Tutti i campi sono obbligatori per inserire una sessione privata.");
            return;
        }

        $cliente = $this->clienteRepo->findById($idCliente);
        if (!$cliente) {
            $this->view->mostraStatoOperazione(false, "Cliente non trovato.");
            return;
        }

        if ($cliente->getPalestra() === null || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraStatoOperazione(false, "Il cliente indicato non appartiene alla tua palestra.");
            return;
        }

        try {
            $dataObj = new \DateTimeImmutable($dataStr);
            $oraInizioObj = new DateTimeImmutableStringable($dataStr . ' ' . $oraInizioStr);
            $oraFineObj = new DateTimeImmutableStringable($dataStr . ' ' . $oraFineStr);

            if ($oraInizioObj >= $oraFineObj) {
                $this->view->mostraStatoOperazione(false, "L'ora di inizio deve essere precedente all'ora di fine.");
                return;
            }

            if ($this->sessionePrivataRepo->existsSovrapposizioneAllenatore($allenatore, $dataObj, $oraInizioObj, $oraFineObj)) {
                $this->view->mostraStatoOperazione(false, "L'allenatore ha già un impegno in questa fascia oraria.");
                return;
            }

            if ($this->sessionePrivataRepo->existsSovrapposizioneCliente($cliente, $dataObj, $oraInizioObj, $oraFineObj)) {
                $this->view->mostraStatoOperazione(false, "Il cliente ha già un impegno in questa fascia oraria.");
                return;
            }

            $sessione = new SessionePrivata($dataObj, $oraInizioObj, $oraFineObj, $cliente, $allenatore);
            $this->sessionePrivataRepo->save($sessione);

            // Invia un messaggio interno al cliente
            $oggetto = "Nuova Sessione Privata Pianificata";
            $giorno = $dataObj->format('d/m/Y');
            $inizio = $oraInizioObj->format('H:i');
            $fine = $oraFineObj->format('H:i');
            $contenuto = "Ciao " . $cliente->getNome() . ", ti comunico che ho pianificato una sessione privata con te per il giorno " . $giorno . " dalle ore " . $inizio . " alle " . $fine . ".";

            $messaggio = new Messaggio($allenatore, $oggetto, $contenuto);
            $messaggio->aggiungiDestinatario($cliente);
            $this->entityManager->persist($messaggio);
            $this->entityManager->flush();

            $this->view->mostraStatoOperazione(true, "Sessione privata pianificata con successo.", "calendario?data=" . $dataStr);
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile salvare la sessione: " . $e->getMessage());
        }
    }

    /**
     * Cancella la prenotazione di una sessione privata
     */
    public function disdiciSessionePrivata(): void
    {
        $palestra = $this->recuperaPalestraUtente();
        if (!$palestra) {
            $this->view->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $ruolo = $this->session->getLoggedUserRole();
        if ($ruolo !== 'cliente' && $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Solo i soggetti partecipanti possono annullare la sessione.");
            return;
        }

        $idAllenatore = isset($_REQUEST['id_allenatore']) ? (int)$_REQUEST['id_allenatore'] : 0;
        $oraInizioStr = !empty($_REQUEST['ora_inizio']) ? trim($_REQUEST['ora_inizio']) : '';
        $oraFineStr = !empty($_REQUEST['ora_fine']) ? trim($_REQUEST['ora_fine']) : '';

        if ($idAllenatore <= 0 || $oraInizioStr === '' || $oraFineStr === '') {
            $this->view->mostraStatoOperazione(false, "Dati identificativi della sessione non validi.");
            return;
        }

        $allenatore = $this->entityManager->find(Allenatore::class, $idAllenatore);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato.");
            return;
        }

        try {
            $oraInizioObj = new DateTimeImmutableStringable($oraInizioStr);
            $oraFineObj = new DateTimeImmutableStringable($oraFineStr);

            $sessione = $this->sessionePrivataRepo->findByChiave($allenatore, $oraInizioObj, $oraFineObj);
            if (!$sessione) {
                $this->view->mostraStatoOperazione(false, "Sessione privata non trovata.");
                return;
            }

            // Verifica accessi (Anti-IDOR)
            $idUtente = $this->session->getLoggedUserId();
            $isAuthorized = false;
            if ($ruolo === 'allenatore' && $idUtente === $sessione->getAllenatore()->getId()) {
                $isAuthorized = true;
            } elseif ($ruolo === 'cliente' && $idUtente === $sessione->getAtleta()->getId()) {
                $isAuthorized = true;
            }

            if (!$isAuthorized) {
                $this->view->mostraStatoOperazione(false, "Accesso negato. Non sei autorizzato a disdire questa sessione.");
                return;
            }

            // Se disdetta dall'allenatore, invia un messaggio interno al cliente
            if ($ruolo === 'allenatore') {
                $cliente = $sessione->getAtleta();
                $oggetto = "Sessione Privata Annullata";
                $giorno = $sessione->getData()->format('d/m/Y');
                $inizio = $sessione->getOraInizio()->format('H:i');
                $fine = $sessione->getOraFine()->format('H:i');
                $contenuto = "Ciao " . $cliente->getNome() . ", ti comunico che ho annullato la sessione privata pianificata per il giorno " . $giorno . " dalle ore " . $inizio . " alle " . $fine . ".";

                $messaggio = new Messaggio($allenatore, $oggetto, $contenuto);
                $messaggio->aggiungiDestinatario($cliente);
                $this->entityManager->persist($messaggio);
                $this->entityManager->flush();
            }

            $this->sessionePrivataRepo->delete($sessione);
            $this->view->mostraStatoOperazione(true, "Sessione privata annullata con successo.", "calendario?data=" . $sessione->getData()->format('Y-m-d'));
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Impossibile annullare la sessione privata: " . $e->getMessage());
        }
    }
}
