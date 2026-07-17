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

    public function compilaDatiEsercizio(): void
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
        $erroreFile = $this->verificaErroreImmagine($idProv);

        if ($esisteDuplicato) {
            $this->view->mostraStatoOperazione(false, "Esiste già un esercizio con questo nome.", "crea-esercizio");
            return;
        }
        if ($erroreFile) {
            $this->view->mostraStatoOperazione(false, $erroreFile, "crea-esercizio");
            return;
        }

        $this->view->mostraStatoOperazione(true, "Dati validati con successo.", "crea-esercizio");
    }

    private function verificaErroreImmagine(string $idProvvisorio): ?string
    {
        if (!isset($_FILES['immagine']) || $_FILES['immagine']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $ext = strtolower(pathinfo($_FILES['immagine']['name'], PATHINFO_EXTENSION));
        $type = $_FILES['immagine']['type'];
        if (!in_array($ext, ['gif', 'png', 'jpg', 'jpeg']) || !in_array($type, ['image/gif', 'image/png', 'image/jpeg', 'image/pjpeg'])) {
            return "Formato multimediale non valido. Consentite solo immagini e GIF.";
        }
        if ($_FILES['immagine']['size'] > 5 * 1024 * 1024) {
            return "Dimensione file eccessiva (limite 5 MB).";
        }
        $content = file_get_contents($_FILES['immagine']['tmp_name']);
        if ($content !== false && $idProvvisorio !== '') {
            $_SESSION['bozze_esercizi'][$idProvvisorio]['immagine_bin'] = $content;
        }
        return null;
    }

    // =========================================================================
    // 3. SALVA ESERCIZIO (/salva-esercizio)
    // =========================================================================

    public function salvaEsercizio(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: dashboard-allenatore');
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
        $att = $this->recuperaAttrezzatura($_POST['attrezzatura_id'] ?? '', trim($_POST['nuova_attrezzatura_nome'] ?? ''));
        $tip = $this->recuperaTipologia(isset($_POST['tracciamento_carico']) ? (int)$_POST['tracciamento_carico'] : 1);
        $all = $this->entityManager->find(Allenatore::class, $idAllenatore);
        $es = new Esercizio($nome, trim($_POST['descrizione'] ?? ''), $tip, $att, $all, $this->recuperaImmagine($idProv));
        $this->associaGruppiMuscolari($es, $_POST['gruppi_muscolari'] ?? [], trim($_POST['nuovo_gruppo_nome'] ?? ''));
        try {
            $this->esercizioRepo->save($es);
            if ($idProv !== '') unset($_SESSION['bozze_esercizi'][$idProv]);
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
        if ($idAttrPost === 'nuova_attrezzatura') {
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
        return (is_numeric($idAttrPost) && $idAttrPost !== '') ? $this->attrezzaturaRepo->findById((int)$idAttrPost) : null;
    }

    private function recuperaImmagine(string $idProvvisorio): ?string
    {
        if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === UPLOAD_ERR_OK) {
            return file_get_contents($_FILES['immagine']['tmp_name']);
        }
        if ($idProvvisorio !== '' && isset($_SESSION['bozze_esercizi'][$idProvvisorio]['immagine_bin'])) {
            return $_SESSION['bozze_esercizi'][$idProvvisorio]['immagine_bin'];
        }
        return null;
    }

    private function associaGruppiMuscolari(Esercizio $esercizio, array $gruppiSelezionati, string $nuovoGruppoNome): void
    {
        foreach ($gruppiSelezionati as $idGm) {
            if ($idGm === 'nuovo_gruppo') {
                if ($nuovoGruppoNome === '') continue;
                $gm = $this->gruppoMuscolareRepo->findByNome($nuovoGruppoNome);
                if (!$gm) {
                    $gm = new GruppoMuscolare($nuovoGruppoNome);
                    $this->gruppoMuscolareRepo->save($gm);
                }
                $esercizio->aggiungiGruppoMuscolare($gm);
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

    public function copiaEsercizio(): void
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
            $this->view->mostraStatoOperazione(false, "Esercizio sorgente non trovato.", "dashboard-allenatore");
            return;
        }
        $this->eseguiCopiaEsercizio($sorgente);
    }

    private function eseguiCopiaEsercizio(Esercizio $sorgente): void
    {
        $idProv = 'es_bozza_' . bin2hex(random_bytes(8));
        $nomeTipo = strtolower($sorgente->getTipologia()->getNomeTipologia());
        $carico = ($nomeTipo === 'durata' || $nomeTipo === 'tempo/ripetizioni') ? 0 : 1;
        $selectedGruppi = array_map(fn($gm) => $gm->getId(), $sorgente->getGruppiMuscolari()->toArray());
        $attrId = $sorgente->getAttrezzaturaNecessaria() ? $sorgente->getAttrezzaturaNecessaria()->getId() : null;

        $_SESSION['bozze_esercizi'][$idProv] = [
            'stato' => 'copiato', 'nome' => $sorgente->getNomeEsercizio() . ' (Copia)',
            'descrizione' => $sorgente->getDescrizione(), 'tracciamento_carico' => $carico,
            'gruppi_muscolari' => $selectedGruppi, 'attrezzatura' => $attrId, 'immagine_bin' => $sorgente->getImmagine()
        ];
        $this->view->mostraFormEsercizio([
            'id_provvisorio' => $idProv, 'gruppi_muscolari' => $this->gruppoMuscolareRepo->findAll(),
            'attrezzature' => $this->attrezzaturaRepo->findAll(), 'esercizi_esistenti' => $this->esercizioRepo->findAll(),
            'is_copia' => true, 'nome_esercizio' => $sorgente->getNomeEsercizio() . ' (Copia)',
            'descrizione' => $sorgente->getDescrizione(), 'tracciamento_carico' => $carico,
            'selected_gruppi' => $selectedGruppi, 'selected_attrezzatura' => $attrId,
            'immagine_preview' => $sorgente->getImmagine() ? base64_encode($sorgente->getImmagine()) : null
        ]);
    }

    // =========================================================================
    // 5. LISTA ESERCIZI (/esercizi)
    // =========================================================================

    public function listaEsercizi(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa funzionalità è riservata agli allenatori.", "login");
            return;
        }
        $esercizi = $this->esercizioRepo->findAll();
        $query = trim($_POST['search_query'] ?? $_GET['search_query'] ?? '');
        if ($query !== '') {
            $search = strtolower($query);
            $esercizi = array_filter($esercizi, function($e) use ($search) {
                return str_contains(strtolower($e->getNomeEsercizio()), $search) || str_contains(strtolower($e->getDescrizione()), $search);
            });
        }
        $this->view->mostraListaEsercizi($this->mappaEserciziPerView($esercizi));
    }

    private function mappaEserciziPerView(array $esercizi): array
    {
        $eserciziData = [];
        foreach ($esercizi as $e) {
            $gruppiNomi = array_map(fn($gm) => $gm->getNomeGruppoMuscolare(), $e->getGruppiMuscolari()->toArray());
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

    public function visualizzaEsercizio(): void
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
