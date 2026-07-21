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
use App\Entity\Repository\AllenatoreRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrineEsercizioRepository;
use App\Foundation\Persistence\Repository\DoctrineGruppoMuscolareRepository;
use App\Foundation\Persistence\Repository\DoctrineAttrezzaturaRepository;
use App\Foundation\Persistence\Repository\DoctrineTipologiaRepository;
use App\Foundation\Persistence\Repository\DoctrineAllenatoreRepository;
use App\View\Interface\EserciziView;
use App\View\EserciziViewSmarty;
use App\Foundation\Session;
use App\Foundation\Utility\HTTPMethods;
use Doctrine\ORM\EntityManagerInterface;

class EserciziController
{
    private EsercizioRepositoryInterface $esercizioRepo;
    private GruppoMuscolareRepositoryInterface $gruppoMuscolareRepo;
    private AttrezzaturaRepositoryInterface $attrezzaturaRepo;
    private TipologiaRepositoryInterface $tipologiaRepo;
    private AllenatoreRepositoryInterface $allenatoreRepo;
    private EserciziView $view;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->esercizioRepo = new DoctrineEsercizioRepository($this->entityManager);
        $this->gruppoMuscolareRepo = new DoctrineGruppoMuscolareRepository($this->entityManager);
        $this->attrezzaturaRepo = new DoctrineAttrezzaturaRepository($this->entityManager);
        $this->tipologiaRepo = new DoctrineTipologiaRepository($this->entityManager);
        $this->allenatoreRepo = new DoctrineAllenatoreRepository($this->entityManager);
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
        $this->session->set('bozze_esercizi_' . $idProvvisorio, [
            'stato' => 'inizializzato', 'nome' => '', 'descrizione' => '',
            'tracciamento_carico' => 1,
            'gruppi_muscolari' => [],           
            'attrezzatura' => null, 
            'immagine_bin' => null,
            'immagine_type' => null
        ]);
        $this->view->mostraFormEsercizio([      //la form viene popolata con i dati inizializzati nella sessione
            'id_provvisorio' => $idProvvisorio,
            'gruppi_muscolari' => $this->gruppoMuscolareRepo->findAll(),
            'attrezzature' => $this->attrezzaturaRepo->findAll(),
            'esercizi_esistenti' => $this->esercizioRepo->findAll(),
            'is_copia' => false, 'nome_esercizio' => '', 'descrizione' => '',
            'tracciamento_carico' => 1, 
            'selected_gruppi' => [],
            'selected_attrezzatura' => null, 
            'immagine_preview' => null,
            'immagine_type' => null
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
        $nome = trim(HTTPMethods::post('nome', ''));
        $idProv = trim(HTTPMethods::post('id_provvisorio', ''));
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
        $file = HTTPMethods::files('immagine');
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null; 
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $type = $file['type'];
        if (!in_array($ext, ['gif', 'png', 'jpg', 'jpeg']) || !in_array($type, ['image/gif', 'image/png', 'image/jpeg', 'image/pjpeg'])) {
            return "Formato multimediale non valido. Consentite solo immagini e GIF.";
        }
        if ($file['size'] > 16 * 1024 * 1024) {
            return "Dimensione file eccessiva (limite 16 MB).";
        }
        $content = file_get_contents($file['tmp_name']);
        if ($content !== false && $idProvvisorio !== '') {
            $bozza = $this->session->get('bozze_esercizi_' . $idProvvisorio) ?? [];
            $bozza['immagine_bin'] = $content;
            $bozza['immagine_type'] = $type;
            $this->session->set('bozze_esercizi_' . $idProvvisorio, $bozza);
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
        if (HTTPMethods::method() !== 'POST') {
            $this->view->redirect('dashboard-allenatore');
            return;
        }
        $this->eseguiSalvataggioEsercizio($idAllenatore);
    }

    private function eseguiSalvataggioEsercizio(int $idAllenatore): void
    {
        $nome = trim(HTTPMethods::post('nome', ''));
        $idProv = trim(HTTPMethods::post('id_provvisorio', ''));
        $erroreFile = $this->verificaErroreImmagine($idProv);
        if ($erroreFile) {
            $this->view->mostraStatoOperazione(false, $erroreFile, "crea-esercizio");
            return;
        }
        if ($nome === '' || $this->esercizioRepo->existsByNome($nome)) {
            $this->view->mostraStatoOperazione(false, "Nome non valido o duplicato.", "crea-esercizio");
            return;
        }
        $att = $this->recuperaAttrezzatura(HTTPMethods::post('attrezzatura_id', ''), trim(HTTPMethods::post('nuova_attrezzatura_nome', '')));
        $tracciamento = HTTPMethods::post('tracciamento_carico');
        $tip = $this->recuperaTipologia($tracciamento !== null ? (int)$tracciamento : 1);
        $all = $this->allenatoreRepo->findById($idAllenatore);
        $immagineType = null;
        $immagineBin = $this->recuperaImmagine($idProv, $immagineType);
        $es = new Esercizio($nome, trim(HTTPMethods::post('descrizione', '')), $tip, $att, $all, $immagineBin, $immagineType);
        $this->associaGruppiMuscolari($es, HTTPMethods::postArray('gruppi_muscolari'), trim(HTTPMethods::post('nuovo_gruppo_nome', '')));
        try {
            $this->esercizioRepo->save($es);
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

    private function recuperaImmagine(string $idProvvisorio, ?string &$type = null): ?string
    {
        $file = HTTPMethods::files('immagine');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $type = $file['type'];
            return file_get_contents($file['tmp_name']);
        }
        if ($idProvvisorio !== '') {
            $bozza = $this->session->get('bozze_esercizi_' . $idProvvisorio);
            if (isset($bozza['immagine_bin'])) {
                $type = $bozza['immagine_type'] ?? null;
                return $bozza['immagine_bin'];
            }
        }
        return null;
    }

    private function associaGruppiMuscolari(Esercizio $esercizio, array $gruppiSelezionati, string $nuovoGruppoNome): void
    {
        foreach ($gruppiSelezionati as $idGm) {
            if ($idGm === 'nuovo_gruppo') {
                if ($nuovoGruppoNome !== '') {
                    $gm = $this->gruppoMuscolareRepo->findByNome($nuovoGruppoNome);
                    if (!$gm) {
                        $gm = new GruppoMuscolare($nuovoGruppoNome);
                        $this->gruppoMuscolareRepo->save($gm);
                    }
                    $esercizio->aggiungiGruppoMuscolare($gm);
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

    public function copiaEsercizio(): void
    {
        $idAllenatore = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAllenatore || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato.", "login");
            return;
        }
        $idSor = HTTPMethods::get('id') ? (int)HTTPMethods::get('id') : 0;
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
            'tracciamento_carico' => $isDurata ? 0 : 1,
            'gruppi_muscolari' => array_map(fn($gm) => $gm->getId(), $sorgente->getGruppiMuscolari()->toArray()),
            'attrezzatura' => $attr ? $attr->getId() : null,
            'immagine_bin' => $sorgente->getImmagine(),
            'immagine_type' => $sorgente->getTipoImmagine()
        ];
        $this->session->set('bozze_esercizi_' . $idProv, $bozza);

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
            'immagine_preview' => $bozza['immagine_bin'] ? base64_encode($bozza['immagine_bin']) : null,
            'immagine_type' => $bozza['immagine_type'] ?? 'image/jpeg'
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
        $query = trim(HTTPMethods::request('search_query', ''));
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
                'immagine' => $e->getImmagine() ? base64_encode($e->getImmagine()) : null,
                'immagine_type' => $e->getTipoImmagine() ?? 'image/jpeg'
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
        $esercizio = $this->esercizioRepo->findById(HTTPMethods::get('id') ? (int)HTTPMethods::get('id') : 0);
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
            'immagine' => $esercizio->getImmagine() ? base64_encode($esercizio->getImmagine()) : null,
            'immagine_type' => $esercizio->getTipoImmagine() ?? 'image/jpeg'
        ]);
    }
}
