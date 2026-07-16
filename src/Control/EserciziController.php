<?php
namespace App\Control;

use App\Entity\Esercizio;
use App\Entity\GruppoMuscolare;
use App\Entity\Attrezzatura;
use App\Entity\Tipologia;
use App\Entity\Allenatore;
use App\Entity\Repository\EsercizioRepositoryInterface;
use App\Entity\Repository\GruppoMuscolareRepositoryInterface;
use App\Entity\Repository\AttrezzaturaRepositoryInterface;
use App\Entity\Repository\TipologiaRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineEsercizioRepository;
use App\Foundation\Persistence\Repository\DoctrineGruppoMuscolareRepository;
use App\Foundation\Persistence\Repository\DoctrineAttrezzaturaRepository;
use App\Foundation\Persistence\Repository\DoctrineTipologiaRepository;
use App\View\Interface\EserciziView;
use App\View\EserciziViewSmarty;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;

class EserciziController
{
    private EsercizioRepositoryInterface $esercizioRepo;
    private GruppoMuscolareRepositoryInterface $gruppoMuscolareRepo;
    private AttrezzaturaRepositoryInterface $attrezzaturaRepo;
    private TipologiaRepositoryInterface $tipologiaRepo;
    private EserciziView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->esercizioRepo = new DoctrineEsercizioRepository($this->entityManager);
        $this->gruppoMuscolareRepo = new DoctrineGruppoMuscolareRepository($this->entityManager);
        $this->attrezzaturaRepo = new DoctrineAttrezzaturaRepository($this->entityManager);
        $this->tipologiaRepo = new DoctrineTipologiaRepository($this->entityManager);
        $this->view = new EserciziViewSmarty();
    }

    /**
     * 1. Inizializzazione Nuovo Esercizio
     * Apre il modulo di inserimento vuoto e genera un ID provvisorio.
     */
    public function apriFormCreazioneEsercizio(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa funzionalità è riservata agli allenatori.", "login");
            return;
        }

        // Genera un ID provvisorio univoco per l'oggetto bozza
        $idProvvisorio = 'es_bozza_' . bin2hex(random_bytes(8));

        // Predispone lo stato della cache provvisoria in sessione
        $_SESSION['bozze_esercizi'][$idProvvisorio] = [
            'stato' => 'inizializzato',
            'nome' => '',
            'descrizione' => '',
            'tracciamento_carico' => 1,
            'gruppi_muscolari' => [],
            'attrezzatura' => null,
            'immagine_bin' => null
        ];

        // Carica dati per la classificazione
        $gruppiMuscolari = $this->gruppoMuscolareRepo->findAll();
        $attrezzature = $this->attrezzaturaRepo->findAll();
        $eserciziEsistenti = $this->esercizioRepo->findAll();

        $this->view->mostraFormEsercizio([
            'id_provvisorio' => $idProvvisorio,
            'gruppi_muscolari' => $gruppiMuscolari,
            'attrezzature' => $attrezzature,
            'esercizi_esistenti' => $eserciziEsistenti,
            'is_copia' => false,
            // Valori predefiniti vuoti
            'nome_esercizio' => '',
            'descrizione' => '',
            'tracciamento_carico' => 1,
            'selected_gruppi' => [],
            'selected_attrezzatura' => null,
            'immagine_preview' => null
        ]);
    }

    /**
     * 2. Inserimento Dati e Media (Validazione in tempo reale via AJAX)
     */
    public function compilaDatiEsercizio(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idAllenatore || $ruolo !== 'allenatore') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'errore' => 'Azione non autorizzata.']);
            return;
        }

        $nome = trim($_POST['nome'] ?? '');
        $idProvvisorio = trim($_POST['id_provvisorio'] ?? '');

        // 1. Verifica duplicato per il nome dell'esercizio
        $esisteDuplicato = false;
        if ($nome !== '') {
            $esisteDuplicato = $this->esercizioRepo->existsByNome($nome);
        }

        // 2. Conferma caricamento file multimediale (formato e dimensione)
        $erroreFile = null;
        if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === UPLOAD_ERR_OK) {
            $fileSize = $_FILES['immagine']['size'];
            $fileType = $_FILES['immagine']['type'];
            
            // Estrae estensione
            $fileName = $_FILES['immagine']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['gif', 'png', 'jpg', 'jpeg'];
            $allowedMimeTypes = ['image/gif', 'image/png', 'image/jpeg', 'image/pjpeg'];

            if (!in_array($fileExtension, $allowedExtensions) || !in_array($fileType, $allowedMimeTypes)) {
                $erroreFile = "Formato multimediale non valido. Sono consentite solo immagini e GIF (gif, png, jpg, jpeg).";
            } elseif ($fileSize > 5 * 1024 * 1024) {
                $erroreFile = "Dimensione file eccessiva. Il limite massimo è di 5 MB.";
            } else {
                // Se valido, salviamo provvisoriamente l'immagine in cache di sessione
                $content = file_get_contents($_FILES['immagine']['tmp_name']);
                if ($content !== false && $idProvvisorio !== '') {
                    $_SESSION['bozze_esercizi'][$idProvvisorio]['immagine_bin'] = $content;
                }
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => !$esisteDuplicato && !$erroreFile,
            'duplicato' => $esisteDuplicato,
            'errore_nome' => $esisteDuplicato ? 'Esiste già un esercizio con questo nome nel catalogo.' : null,
            'errore_file' => $erroreFile
        ]);
    }

    /**
     * 3a. Gestione e Opzioni di Salvataggio: SALVA
     * Scrive i dati finali nel DB.
     */
    public function salvaEsercizio(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa funzionalità è riservata agli allenatori.", "login");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: dashboard-allenatore');
            return;
        }

        $idProvvisorio = trim($_POST['id_provvisorio'] ?? '');
        $nome = trim($_POST['nome'] ?? '');
        $descrizione = trim($_POST['descrizione'] ?? '');
        $tracciamentoCarico = isset($_POST['tracciamento_carico']) ? (int)$_POST['tracciamento_carico'] : 1;
        $gruppiSelezionati = $_POST['gruppi_muscolari'] ?? [];
        $idAttrezzaturaPost = $_POST['attrezzatura_id'] ?? '';
        $nuovoGruppoNome = trim($_POST['nuovo_gruppo_nome'] ?? '');
        $nuovaAttrezzaturaNome = trim($_POST['nuova_attrezzatura_nome'] ?? '');

        // Validazione finale obbligatoria lato server
        if ($nome === '') {
            $this->view->mostraStatoOperazione(false, "Il nome dell'esercizio è obbligatorio.", "crea-esercizio");
            return;
        }

        if ($this->esercizioRepo->existsByNome($nome)) {
            $this->view->mostraStatoOperazione(false, "Esiste già un esercizio con questo nome nel catalogo.", "crea-esercizio");
            return;
        }

        // Recupera l'allenatore loggato
        $allenatore = $this->entityManager->find(Allenatore::class, $idAllenatore);
        if (!$allenatore) {
            $this->view->mostraStatoOperazione(false, "Allenatore non trovato nel database.", "login");
            return;
        }

        // Trova o crea la Tipologia adatta
        $nomeTipologia = ($tracciamentoCarico === 1) ? 'Ripetizioni' : 'Durata';
        $tipologia = $this->tipologiaRepo->findByNome($nomeTipologia);
        if (!$tipologia) {
            $tipologia = new Tipologia($nomeTipologia);
            $this->tipologiaRepo->save($tipologia);
        }

        // Trova l'attrezzatura necessaria se specificata o creata
        $attrezzatura = null;
        if ($idAttrezzaturaPost === 'nuova_attrezzatura') {
            if ($nuovaAttrezzaturaNome === '') {
                $this->view->mostraStatoOperazione(false, "Il nome della nuova attrezzatura è obbligatorio se hai selezionato di aggiungerne una nuova.", "crea-esercizio");
                return;
            }
            $attrezzatura = $this->attrezzaturaRepo->findByNome($nuovaAttrezzaturaNome);
            if (!$attrezzatura) {
                $attrezzatura = new Attrezzatura($nuovaAttrezzaturaNome);
                $this->attrezzaturaRepo->save($attrezzatura);
            }
        } elseif (is_numeric($idAttrezzaturaPost) && $idAttrezzaturaPost !== '') {
            $attrezzatura = $this->attrezzaturaRepo->findById((int)$idAttrezzaturaPost);
        }

        // Gestione immagine (GIF/Immagine)
        $immagineBin = null;
        
        // 1. Controlla se è stato caricato un nuovo file
        if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === UPLOAD_ERR_OK) {
            $immagineBin = file_get_contents($_FILES['immagine']['tmp_name']);
        }
        
        // 2. Se non caricato ora, controlla se c'è l'immagine nella bozza/sessione
        if ($immagineBin === null && $idProvvisorio !== '' && isset($_SESSION['bozze_esercizi'][$idProvvisorio]['immagine_bin'])) {
            $immagineBin = $_SESSION['bozze_esercizi'][$idProvvisorio]['immagine_bin'];
        }

        // Crea l'entità Esercizio
        $esercizio = new Esercizio(
            $nome,
            $descrizione,
            $tipologia,
            $attrezzatura,
            $idAllenatore ? $this->entityManager->find(Allenatore::class, $idAllenatore) : null,
            $immagineBin
        );

        // Associa i gruppi muscolari scelti o ne crea uno nuovo
        foreach ($gruppiSelezionati as $idGm) {
            if ($idGm === 'nuovo_gruppo') {
                if ($nuovoGruppoNome === '') {
                    $this->view->mostraStatoOperazione(false, "Il nome del nuovo gruppo muscolare è obbligatorio se hai selezionato di aggiungerne uno nuovo.", "crea-esercizio");
                    return;
                }
                $gruppoMuscolare = $this->gruppoMuscolareRepo->findByNome($nuovoGruppoNome);
                if (!$gruppoMuscolare) {
                    $gruppoMuscolare = new GruppoMuscolare($nuovoGruppoNome);
                    $this->gruppoMuscolareRepo->save($gruppoMuscolare);
                }
                $esercizio->aggiungiGruppoMuscolare($gruppoMuscolare);
            } else {
                $gruppoMuscolare = $this->gruppoMuscolareRepo->findById((int)$idGm);
                if ($gruppoMuscolare) {
                    $esercizio->aggiungiGruppoMuscolare($gruppoMuscolare);
                }
            }
        }

        try {
            $this->esercizioRepo->save($esercizio);

            // Rimuove la bozza dalla sessione (pulisce cache)
            if ($idProvvisorio !== '') {
                unset($_SESSION['bozze_esercizi'][$idProvvisorio]);
            }
            $this->view->mostraStatoOperazione(true, "Esercizio aggiunto alla libreria con successo.", "esercizi", "Torna a Gestione Esercizi");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore durante il salvataggio dell'esercizio: " . $e->getMessage(), "crea-esercizio");
        }
    }

    /**
     * 3b. Gestione e Opzioni di Salvataggio: COPIA DA ESISTENTE
     * Richiama i dati di un esercizio per pre-popolare il modulo.
     */
    public function copiaEsercizio(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa funzionalità è riservata agli allenatori.", "login");
            return;
        }

        $idSorgente = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $sorgente = $this->esercizioRepo->findById($idSorgente);

        if (!$sorgente) {
            $this->view->mostraStatoOperazione(false, "Esercizio sorgente non trovato.", "dashboard-allenatore");
            return;
        }

        // Genera un ID provvisorio univoco per la nuova bozza clonata
        $idProvvisorio = 'es_bozza_' . bin2hex(random_bytes(8));

        // Determina se il tracciamento del carico è attivo (0 per durata/tempo, 1 per ripetizioni)
        $nomeTipoLower = strtolower($sorgente->getTipologia()->getNomeTipologia());
        $tracciamentoCarico = ($nomeTipoLower === 'durata' || $nomeTipoLower === 'tempo/ripetizioni') ? 0 : 1;

        // Estrae gli ID dei gruppi muscolari associati
        $selectedGruppi = [];
        foreach ($sorgente->getGruppiMuscolari() as $gm) {
            $selectedGruppi[] = $gm->getId();
        }

        // Salva l'immagine della sorgente nella cache provvisoria
        $_SESSION['bozze_esercizi'][$idProvvisorio] = [
            'stato' => 'copiato',
            'nome' => $sorgente->getNomeEsercizio() . ' (Copia)',
            'descrizione' => $sorgente->getDescrizione(),
            'tracciamento_carico' => $tracciamentoCarico,
            'gruppi_muscolari' => $selectedGruppi,
            'attrezzatura' => $sorgente->getAttrezzaturaNecessaria() ? $sorgente->getAttrezzaturaNecessaria()->getId() : null,
            'immagine_bin' => $sorgente->getImmagine()
        ];

        // Carica dati per la classificazione
        $gruppiMuscolari = $this->gruppoMuscolareRepo->findAll();
        $attrezzature = $this->attrezzaturaRepo->findAll();
        $eserciziEsistenti = $this->esercizioRepo->findAll();

        $immaginePreview = $sorgente->getImmagine() ? base64_encode($sorgente->getImmagine()) : null;

        $this->view->mostraFormEsercizio([
            'id_provvisorio' => $idProvvisorio,
            'gruppi_muscolari' => $gruppiMuscolari,
            'attrezzature' => $attrezzature,
            'esercizi_esistenti' => $eserciziEsistenti,
            'is_copia' => true,
            // Valori sorgente pre-popolati per la modifica
            'nome_esercizio' => $sorgente->getNomeEsercizio() . ' (Copia)',
            'descrizione' => $sorgente->getDescrizione(),
            'tracciamento_carico' => $tracciamentoCarico,
            'selected_gruppi' => $selectedGruppi,
            'selected_attrezzatura' => $sorgente->getAttrezzaturaNecessaria() ? $sorgente->getAttrezzaturaNecessaria()->getId() : null,
            'immagine_preview' => $immaginePreview
        ]);
    }

    /**
     * Visualizza la lista di tutti gli esercizi creati.
     */
    public function listaEsercizi(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa funzionalità è riservata agli allenatori.", "login");
            return;
        }

        $esercizi = $this->esercizioRepo->findAll();

        $query = $_POST['search_query'] ?? $_GET['search_query'] ?? null;
        if ($query !== null && trim($query) !== '') {
            $search = strtolower(trim($query));
            $esercizi = array_filter($esercizi, function($e) use ($search) {
                return str_contains(strtolower($e->getNomeEsercizio()), $search) || 
                       str_contains(strtolower($e->getDescrizione()), $search);
            });
        }

        $eserciziData = [];
        foreach ($esercizi as $e) {
            $gruppiNomi = [];
            foreach ($e->getGruppiMuscolari() as $gm) {
                $gruppiNomi[] = $gm->getNomeGruppoMuscolare();
            }

            $eserciziData[] = [
                'id' => $e->getId(),
                'nome' => $e->getNomeEsercizio(),
                'descrizione' => $e->getDescrizione(),
                'attrezzatura' => $e->getAttrezzaturaNecessaria() ? $e->getAttrezzaturaNecessaria()->getNomeAttrezzatura() : 'Nessuna',
                'tipologia' => $e->getTipologia()->getNomeTipologia(),
                'gruppiMuscolari' => implode(', ', $gruppiNomi),
                'creatore' => $e->getCreatore() ? ($e->getCreatore()->getNome() . ' ' . $e->getCreatore()->getCognome()) : 'Sistema',
                'immagine' => $e->getImmagine() ? base64_encode($e->getImmagine()) : null
            ];
        }

        $this->view->mostraListaEsercizi($eserciziData);
    }

    /**
     * Visualizza i dettagli di un singolo esercizio.
     */
    public function visualizzaEsercizio(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa funzionalità è riservata agli allenatori.", "login");
            return;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $esercizio = $this->esercizioRepo->findById($id);

        if (!$esercizio) {
            $this->view->mostraStatoOperazione(false, "Esercizio non trovato.");
            return;
        }

        $gruppiNomi = [];
        foreach ($esercizio->getGruppiMuscolari() as $gm) {
            $gruppiNomi[] = $gm->getNomeGruppoMuscolare();
        }

        $dati = [
            'id' => $esercizio->getId(),
            'nome' => $esercizio->getNomeEsercizio(),
            'descrizione' => $esercizio->getDescrizione(),
            'attrezzatura' => $esercizio->getAttrezzaturaNecessaria() ? $esercizio->getAttrezzaturaNecessaria()->getNomeAttrezzatura() : 'Nessuna',
            'tipologia' => $esercizio->getTipologia()->getNomeTipologia(),
            'gruppiMuscolari' => implode(', ', $gruppiNomi),
            'creatore' => $esercizio->getCreatore() ? ($esercizio->getCreatore()->getNome() . ' ' . $esercizio->getCreatore()->getCognome()) : 'Sistema',
            'immagine' => $esercizio->getImmagine() ? base64_encode($esercizio->getImmagine()) : null
        ];

        $this->view->mostraDettaglioEsercizio($dati);
    }
}
