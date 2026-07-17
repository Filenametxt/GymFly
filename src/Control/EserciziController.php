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

    // =========================================================================
    // 1. INIZIALIZZAZIONE NUOVO ESERCIZIO (/crea-esercizio)
    // =========================================================================

    public function apriFormCreazioneEsercizio(): void    //gestisce la richiesta di apertura del form di creazione esercizio
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa funzionalità è riservata agli allenatori.", "login");
            return;
        }
        $this->eseguiAperturaForm();
    }

    private function eseguiAperturaForm(): void      //mostra il form di creazione esercizio con i dati iniziali
    {
        $idProvvisorio = 'es_bozza_' . bin2hex(random_bytes(8));
        $_SESSION['bozze_esercizi'][$idProvvisorio] = [                    //inizializza i dati provvisori dell'esercizio nella sessione
            'stato' => 'inizializzato', 'nome' => '', 'descrizione' => '',
            'tracciamento_carico' => 1,             //1 = Ripetizioni, 2 = Durata
            'gruppi_muscolari' => [],           
            'attrezzatura' => null, 
            'immagine_bin' => null
        ];
        $this->view->mostraFormEsercizio([      //la form viene popolata con i dati inizializzati nella sessione
            'id_provvisorio' => $idProvvisorio,
            'gruppi_muscolari' => $this->gruppoMuscolareRepo->findAll(),
            'attrezzature' => $this->attrezzaturaRepo->findAll(),
            'esercizi_esistenti' => $this->esercizioRepo->findAll(),
            'is_copia' => false, 'nome_esercizio' => '', 'descrizione' => '',
            'tracciamento_carico' => 1, 
            'selected_gruppi' => [],
            'selected_attrezzatura' => null, 
            'immagine_preview' => null
        ]);
    }

    // =========================================================================
    // 2. INSERIMENTO DATI E MEDIA (/valida-esercizio)
    // =========================================================================

    public function compilaDatiEsercizio(): void       //gestisce la richiesta di validazione dei dati dell'esercizio e della media caricata
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Azione non autorizzata.", "login");
            return;
        }
        $nome = trim($_POST['nome'] ?? '');
        $idProv = trim($_POST['id_provvisorio'] ?? '');
        $esisteDuplicato = ($nome !== '') ? $this->esercizioRepo->existsByNome($nome) : false;
        $erroreFile = $this->verificaErroreImmagine($idProv);     //verifica se il file caricato è un'immagine valida e non supera i limiti di dimensione

        if ($esisteDuplicato) {
            $this->view->mostraStatoOperazione(false, "Esiste già un esercizio con questo nome.", "crea-esercizio");
            return;
        }
        if ($erroreFile) {                                                                 //se c'è un errore nel file caricato, mostra il messaggio di errore
            $this->view->mostraStatoOperazione(false, $erroreFile, "crea-esercizio");
            return;
        }

        $this->view->mostraStatoOperazione(true, "Dati validati con successo.", "crea-esercizio");
    }

    private function verificaErroreImmagine(string $idProvvisorio): ?string
    {
        if (!isset($_FILES['immagine']) || $_FILES['immagine']['error'] !== UPLOAD_ERR_OK) {     //se non è stato caricato alcun file o c'è stato un errore nel caricamento, esci
            return null; 
        }
        $ext = strtolower(pathinfo($_FILES['immagine']['name'], PATHINFO_EXTENSION));            //ottiene l'estensione del file caricato e la mette in minuscolo
        $type = $_FILES['immagine']['type'];                                                     //ottiene il tipo MIME del file caricato (per far copiare al browser il file corretto)
        if (!in_array($ext, ['gif', 'png', 'jpg', 'jpeg']) || !in_array($type, ['image/gif', 'image/png', 'image/jpeg', 'image/pjpeg'])) {
            return "Formato multimediale non valido. Consentite solo immagini e GIF.";
        }
        if ($_FILES['immagine']['size'] > 5 * 1024 * 1024) {
            return "Dimensione file eccessiva (limite 5 MB).";
        }
        $content = file_get_contents($_FILES['immagine']['tmp_name']);           //salva il contenuto binario del file caricato nella sessione per poterlo recuperare in seguito
        if ($content !== false && $idProvvisorio !== '') {
            $_SESSION['bozze_esercizi'][$idProvvisorio]['immagine_bin'] = $content;
        }
        return null;
    }

    // =========================================================================
    // 3. SALVA ESERCIZIO (/salva-esercizio)
    // =========================================================================

    public function salvaEsercizio(): void            //gestisce la richiesta di salvataggio dell'esercizio, controllando i dati e salvandoli nel database
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: dashboard-allenatore');                 //se la richiesta non è POST, reindirizza alla dashboard dell'allenatore
            return;
        }
        $this->eseguiSalvataggioEsercizio($idAllenatore);
    }

    private function eseguiSalvataggioEsercizio(int $idAllenatore): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $idProv = trim($_POST['id_provvisorio'] ?? '');
        if ($nome === '' || $this->esercizioRepo->existsByNome($nome)) {
            $this->view->mostraStatoOperazione(false, "Nome non valido o duplicato.", "crea-esercizio");
            return;
        }
        $att = $this->recuperaAttrezzatura($_POST['attrezzatura_id'] ?? '', trim($_POST['nuova_attrezzatura_nome'] ?? ''));   //recupera l'attrezzatura selezionata o creane una nuova se necessario
        $tip = $this->recuperaTipologia(isset($_POST['tracciamento_carico']) ? (int)$_POST['tracciamento_carico'] : 1);       //recupera la tipologia in base al tracciamento del carico selezionato (1 = Ripetizioni, 2 = Durata)
        $all = $this->entityManager->find(Allenatore::class, $idAllenatore);
        $es = new Esercizio($nome, trim($_POST['descrizione'] ?? ''), $tip, $att, $all, $this->recuperaImmagine($idProv));
        $this->associaGruppiMuscolari($es, $_POST['gruppi_muscolari'] ?? [], trim($_POST['nuovo_gruppo_nome'] ?? ''));
        try {
            $this->esercizioRepo->save($es);
            if ($idProv !== '') unset($_SESSION['bozze_esercizi'][$idProv]);       //rimuove i dati provvisori dell'esercizio dalla sessione dopo il salvataggio
            $this->view->mostraStatoOperazione(true, "Esercizio aggiunto con successo.", "esercizi", "Torna a Gestione Esercizi");
        } catch (\Throwable $e) {
            $this->view->mostraStatoOperazione(false, "Errore salvataggio: " . $e->getMessage(), "crea-esercizio");
        }
    }

    private function recuperaTipologia(int $tracciamentoCarico): Tipologia
    {
        $nomeTipologia = ($tracciamentoCarico === 1) ? 'Ripetizioni' : 'Durata';
        $tipologia = $this->tipologiaRepo->findByNome($nomeTipologia);
        if (!$tipologia) {
            $tipologia = new Tipologia($nomeTipologia);
            $this->tipologiaRepo->save($tipologia);
        }
        return $tipologia;
    }

    private function recuperaAttrezzatura(string $idAttrPost, string $nuovaAttrNome): ?Attrezzatura
    {
        if ($idAttrPost === 'nuova_attrezzatura') {    //se l'utente ha scelto di creare una nuova attrezzatura, controlla se il nome è valido e crea l'attrezzatura se non esiste già
            if ($nuovaAttrNome === '') {
                return null;
            }
            $attrezzatura = $this->attrezzaturaRepo->findByNome($nuovaAttrNome);
            if (!$attrezzatura) {
                $attrezzatura = new Attrezzatura($nuovaAttrNome);
                $this->attrezzaturaRepo->save($attrezzatura);
            }
            return $attrezzatura;
        }
        return (is_numeric($idAttrPost) && $idAttrPost !== '') ? $this->attrezzaturaRepo->findById((int)$idAttrPost) : null;    //se l'utente ha selezionato un'attrezzatura esistente, la recupera dal repository, altrimenti ritorna null   (best practice: usare is_numeric per permettere a chi legge di capire che il valore può essere un numero o una stringa vuota)
    }

    private function recuperaImmagine(string $idProvvisorio): ?string
    {
        if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === UPLOAD_ERR_OK) {
            return file_get_contents($_FILES['immagine']['tmp_name']);        //se l'utente ha caricato un'immagine, la legge dal file temporaneo e la ritorna come stringa binaria
        }
        if ($idProvvisorio !== '' && isset($_SESSION['bozze_esercizi'][$idProvvisorio]['immagine_bin'])) {    //se l'utente non ha caricato un'immagine ma esiste un'immagine salvata nella sessione per questo esercizio provvisorio, la ritorna come stringa binaria
            return $_SESSION['bozze_esercizi'][$idProvvisorio]['immagine_bin'];
        }
        return null;
    }

    private function associaGruppiMuscolari(Esercizio $esercizio, array $gruppiSelezionati, string $nuovoGruppoNome): void
    {
        foreach ($gruppiSelezionati as $idGm) {
            if ($idGm === 'nuovo_gruppo') {
                if ($nuovoGruppoNome !== '') {                       //se l'utente ha scelto di creare un nuovo gruppo muscolare ed ha inserito il nome
                    $gm = $this->gruppoMuscolareRepo->findByNome($nuovoGruppoNome);
                    if (!$gm) {
                        $gm = new GruppoMuscolare($nuovoGruppoNome);
                        $this->gruppoMuscolareRepo->save($gm);
                    }
                    $esercizio->aggiungiGruppoMuscolare($gm);     //aggiunge il gruppo muscolare all'esercizio
                }
            } else {
                $gm = $this->gruppoMuscolareRepo->findById((int)$idGm);
                if ($gm) {
                    $esercizio->aggiungiGruppoMuscolare($gm);
                }
            }
        }
    }

    // =========================================================================
    // 4. COPIA DA ESISTENTE (/copia-esercizio)
    // =========================================================================

    public function copiaEsercizio(): void     //gestisce la richiesta di copia di un esercizio esistente, recuperando i dati dell'esercizio sorgente e mostrando il form di creazione con i dati precompilati
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $idSor = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $sorgente = $this->esercizioRepo->findById($idSor);
        if (!$sorgente) {
            $this->view->mostraStatoOperazione(false, "Esercizio sorgente non trovato.", "crea-esercizio", "Torna a Gestione Esercizi");
            return;
        }
        $this->eseguiCopiaEsercizio($sorgente);
    }

    private function eseguiCopiaEsercizio(Esercizio $sorgente): void
    {
        $idProv = 'es_bozza_' . bin2hex(random_bytes(8));
        $isDurata = strtolower($sorgente->getTipologia()->getNomeTipologia()) === 'durata';
        $attr = $sorgente->getAttrezzaturaNecessaria();

        $bozza = [
            'stato' => 'copiato',
            'nome' => $sorgente->getNomeEsercizio() . ' (Copia)',
            'descrizione' => $sorgente->getDescrizione(),
            'tracciamento_carico' => $isDurata ? 0 : 1,       //0 = Durata, 1 = Ripetizioni
            'gruppi_muscolari' => array_map(fn($gm) => $gm->getId(), $sorgente->getGruppiMuscolari()->toArray()),      //salva gli ID dei gruppi muscolari associati all'esercizio sorgente
            'attrezzatura' => $attr ? $attr->getId() : null,
            'immagine_bin' => $sorgente->getImmagine()
        ];
        $_SESSION['bozze_esercizi'][$idProv] = $bozza;      //salva i dati provvisori dell'esercizio copiato nella sessione per poterli recuperare in seguito

        $this->view->mostraFormEsercizio([
            'id_provvisorio' => $idProv,
            'gruppi_muscolari' => $this->gruppoMuscolareRepo->findAll(),
            'attrezzature' => $this->attrezzaturaRepo->findAll(),
            'esercizi_esistenti' => $this->esercizioRepo->findAll(),
            'is_copia' => true,
            'nome_esercizio' => $bozza['nome'],
            'descrizione' => $bozza['descrizione'],
            'tracciamento_carico' => $bozza['tracciamento_carico'],
            'selected_gruppi' => $bozza['gruppi_muscolari'],
            'selected_attrezzatura' => $bozza['attrezzatura'],
            'immagine_preview' => $bozza['immagine_bin'] ? base64_encode($bozza['immagine_bin']) : null     //se l'esercizio sorgente aveva un'immagine, la converte in base64 per poterla visualizzare nel form
        ]);
    }

    // =========================================================================
    // 5. LISTA ESERCIZI (/esercizi)
    // =========================================================================

    public function listaEsercizi(): void     //gestisce la richiesta di visualizzazione della lista degli esercizi, con eventuale filtro di ricerca
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa funzionalità è riservata agli allenatori.", "login");
            return;
        }
        $esercizi = $this->esercizioRepo->findAll();
        $query = trim($_POST['search_query'] ?? $_GET['search_query'] ?? '');       //recupera la query di ricerca dalla richiesta POST o GET, se presente
        if ($query !== '') {
            $search = strtolower($query);
            $esercizi = array_filter($esercizi, function($e) use ($search) {
                return str_contains(strtolower($e->getNomeEsercizio()), $search) || str_contains(strtolower($e->getDescrizione()), $search);
            });
        }
        $this->view->mostraListaEsercizi($this->mappaEserciziPerView($esercizi));
    }

    private function mappaEserciziPerView(array $esercizi): array     //mappa gli esercizi recuperati dal repository in un array di dati da passare alla view
    {
        $eserciziData = [];
        foreach ($esercizi as $e) {
            $gruppiNomi = array_map(fn($gm) => $gm->getNomeGruppoMuscolare(), $e->getGruppiMuscolari()->toArray());     //ottiene i nomi dei gruppi muscolari associati all'esercizio
            $eserciziData[] = [
                'id' => $e->getId(), 'nome' => $e->getNomeEsercizio(), 'descrizione' => $e->getDescrizione(),
                'attrezzatura' => $e->getAttrezzaturaNecessaria() ? $e->getAttrezzaturaNecessaria()->getNomeAttrezzatura() : 'Nessuna',
                'tipologia' => $e->getTipologia()->getNomeTipologia(), 'gruppiMuscolari' => implode(', ', $gruppiNomi),
                'creatore' => $e->getCreatore() ? ($e->getCreatore()->getNome() . ' ' . $e->getCreatore()->getCognome()) : 'Sistema',
                'immagine' => $e->getImmagine() ? base64_encode($e->getImmagine()) : null
            ];
        }
        return $eserciziData;
    }

    // =========================================================================
    // 6. DETTAGLIO ESERCIZIO (/visualizza-esercizio)
    // =========================================================================

    public function visualizzaEsercizio(): void      //gestisce la richiesta di visualizzazione del dettaglio di un esercizio, recuperando i dati dell'esercizio e mostrando la view corrispondente
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $esercizio = $this->esercizioRepo->findById(isset($_GET['id']) ? (int)$_GET['id'] : 0);
        if (!$esercizio) {
            $this->view->mostraStatoOperazione(false, "Esercizio non trovato.");
            return;
        }
        $gruppiNomi = array_map(fn($gm) => $gm->getNomeGruppoMuscolare(), $esercizio->getGruppiMuscolari()->toArray());
        $this->view->mostraDettaglioEsercizio([
            'id' => $esercizio->getId(), 'nome' => $esercizio->getNomeEsercizio(), 'descrizione' => $esercizio->getDescrizione(),
            'attrezzatura' => $esercizio->getAttrezzaturaNecessaria() ? $esercizio->getAttrezzaturaNecessaria()->getNomeAttrezzatura() : 'Nessuna',
            'tipologia' => $esercizio->getTipologia()->getNomeTipologia(), 'gruppiMuscolari' => implode(', ', $gruppiNomi),
            'creatore' => $esercizio->getCreatore() ? ($esercizio->getCreatore()->getNome() . ' ' . $esercizio->getCreatore()->getCognome()) : 'Sistema',
            'immagine' => $esercizio->getImmagine() ? base64_encode($esercizio->getImmagine()) : null
        ]);
    }
}
