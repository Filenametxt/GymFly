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
use App\Foundation\Utility\HTTPMethods;
use App\Entity\Parametri;
use App\Entity\CertificatoMedico;
use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Utente;
use App\Entity\Cliente;
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
        $targetId = HTTPMethods::get('id') !== null ? (int)HTTPMethods::get('id') : $idUt;
        $isSelf = ($targetId === $idUt);
        if ($isSelf) {
            return $this->recuperaUtenteLoggato($idUt, $ruolo);
        }
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
        $targetId = (int)HTTPMethods::request('id', $idUtente);
        if ($ruolo !== 'amministratore' && ($idUtente !== $targetId)) {
            $this->mostraStatoOperazione(false, "Accesso negato. Non sei autorizzato a modificare l'anagrafica");
            return;
        }

        $isSelf = true;
        $utente = $this->determinaUtenteModifica($idUtente, $ruolo, $isSelf);
        if (!$utente) {
            $this->mostraStatoOperazione(false, "Profilo non trovato o accesso negato.");
            return;
        }
        if (HTTPMethods::method() === 'GET') {
            $this->view->mostraFormModifica(['utente' => $utente, 'isClient' => ($utente instanceof Cliente), 'isSelf' => $isSelf, 'ruolo' => $ruolo]);
            return;
        }
        $ritorno = $isSelf ? 'profilo' : 'visualizza-profilo?id=' . $utente->getId();
        $this->eseguiSalvataggioAnagrafica($utente, $ruolo, $ritorno);
    }

    private function determinaUtenteModifica(int $idUt, string $ruolo, bool &$isSelf): ?Utente
    {
        $targetId = (int)HTTPMethods::request('id', $idUt);
        $isSelf = ($targetId === $idUt);
        if ($isSelf) {
            return $this->recuperaUtenteLoggato($idUt, $ruolo);
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
        $nome = HTTPMethods::post('nome', '');
        $cognome = HTTPMethods::post('cognome', '');
        $res = HTTPMethods::post('indirizzo', '');
        $pag = HTTPMethods::post('metodo_pagamento', '');

        if (trim($nome) === '' || trim($cognome) === '' || trim($res) === '') {
            $this->mostraStatoOperazione(false, "Nome, cognome e residenza sono obbligatori.", $rit, "Torna al Profilo");
            return;
        }
        try {
            $ut->setNome(trim($nome));
            $ut->setCognome(trim($cognome));
            $ut->setIndirizzo(trim($res));
            if ($ut instanceof Cliente) {
                if (trim($pag) === '') {
                    $this->mostraStatoOperazione(false, "Il metodo di pagamento è obbligatorio per i clienti.", $rit, "Torna al Profilo");
                    return;
                }
                $ut->setMetodoDiPagamento(trim($pag));
                $ut->setIndirizzoDiDomicilio(HTTPMethods::post('indirizzo_domicilio', ''));
                $this->clienteRepo->save($ut);
            } elseif ($ut instanceof Allenatore) {
                $this->allenatoreRepo->save($ut);
            } elseif ($ut instanceof Amministratore) {
                $this->amministratoreRepo->save($ut);
            }
            $this->view->redirect($rit);
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Errore salvataggio: " . $e->getMessage(), $rit, "Torna al Profilo");
        }
    }
    // =========================================================================
    // 3. AGGIORNA MISURE CORPOREE (/aggiorna-misure)
    // =========================================================================

    public function aggiornaMisureCorporee(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || $ruolo === 'amministratore') {
            $this->mostraStatoOperazione(false, "Accesso negato. L'amministratore non può aggiornare le misure del cliente.");
            return;
        }
        $cliente = $this->recuperaClienteTarget($ruolo, $idUt);
        if (!$cliente) {
            $this->mostraStatoOperazione(false, "Cliente non trovato o accesso negato.");
            return;
        }
        if (HTTPMethods::method() === 'GET') {
            $isSelf = ($ruolo === 'cliente' && $cliente->getId() === $idUt);
            $this->view->mostraFormMisure([
                'utente' => $cliente,
                'cliente' => $cliente,
                'isSelf' => $isSelf,
                'ultimaMisure' => $this->parametriRepo->findUltimaByCliente($cliente)
            ]);
            return;
        }
        $this->eseguiAggiornamentoMisure($cliente, $ruolo);
    }

    private function eseguiAggiornamentoMisure(Cliente $cliente, string $ruolo): void
    {
        $peso = HTTPMethods::post('peso') !== null ? (float)HTTPMethods::post('peso') : 0.0;
        $altezza = HTTPMethods::post('altezza') !== null ? (float)HTTPMethods::post('altezza') : 0.0;
        if ($peso <= 0 || $altezza <= 0) {
            $this->mostraStatoOperazione(false, "Peso e altezza devono essere maggiori di zero.");
            return;
        }

        $f = fn($key) => HTTPMethods::postFloat($key);
        try {
            $p = new Parametri($peso, $altezza, new \DateTimeImmutable(), $cliente, $f('bicipite_destro'), $f('bicipite_sinistro'), $f('tricipite_destro'), $f('tricipite_sinistro'), $f('coscia_destra'), $f('coscia_sinistra'), $f('polpaccio_destro'), $f('polpaccio_sinistro'), $f('misura_petto'), $f('misura_vita'), $f('misura_spalle'), $f('misura_fianchi'));
            $this->parametriRepo->save($p);
            $this->view->redirect('aggiorna-misure' . ($ruolo !== 'cliente' ? '?id=' . $cliente->getId() : ''));
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Errore salvataggio misure: " . $e->getMessage());
        }
    }

    // =========================================================================
    // 4. INSERISCI MISURE CORPOREE (/inserisci-misure)
    // =========================================================================

    public function inserisciMisureCorporee(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || $ruolo === 'amministratore') {
            $this->mostraStatoOperazione(false, "Accesso negato. L'amministratore non può inserire le misure del cliente.");
            return;
        }
        $cliente = $this->recuperaClienteTarget($ruolo, $idUt);
        if (!$cliente) {
            $this->mostraStatoOperazione(false, "Cliente non trovato o accesso non consentito.");
            return;
        }
        $isSelf = ($ruolo === 'cliente' && $cliente->getId() === $idUt);
        if (HTTPMethods::method() === 'GET') {
            $this->view->mostraFormInserimentoMisure([
                'utente' => $cliente,
                'cliente' => $cliente,
                'isSelf' => $isSelf,
                'ultimaMisure' => $this->parametriRepo->findUltimaByCliente($cliente)
            ]);
            return;
        }
        $this->salvaMisurePost($cliente, $ruolo);
    }

    private function salvaMisurePost(Cliente $cliente, string $ruolo): void
    {
        $peso = HTTPMethods::post('peso') !== null ? (float)HTTPMethods::post('peso') : 0.0;
        $altezza = HTTPMethods::post('altezza') !== null ? (float)HTTPMethods::post('altezza') : 0.0;
        if ($peso <= 0 || $altezza <= 0) {
            $this->mostraStatoOperazione(false, "Peso e altezza sono obbligatori.");
            return;
        }
        $f = fn($key) => HTTPMethods::postFloat($key);
        try {
            $p = new Parametri(
                $peso, $altezza, new \DateTimeImmutable(), $cliente,
                $f('bicipite_destro'), $f('bicipite_sinistro'), $f('tricipite_destro'), $f('tricipite_sinistro'),
                $f('coscia_destra'), $f('coscia_sinistra'), $f('polpaccio_destro'), $f('polpaccio_sinistro'),
                $f('misura_petto'), $f('misura_vita'), $f('misura_spalle'), $f('misura_fianchi')
            );
            $this->parametriRepo->save($p);
            $this->view->redirect('aggiorna-misure' . ($ruolo !== 'cliente' ? '?id=' . $cliente->getId() : ''));
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Dati non validi: " . $e->getMessage());
        }
    }

    // =========================================================================
    // 5. CARICA CERTIFICATO MEDICO (/carica-certificato)
    // =========================================================================

    public function caricaCertificato(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || ($ruolo !== 'cliente' && $ruolo !== 'amministratore')) {
            $this->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }
        $cliente = $this->recuperaClienteTarget($ruolo, $idUt);
        if (!$cliente) {
            $this->mostraStatoOperazione(false, "Cliente non trovato o accesso negato.");
            return;
        }
        if (HTTPMethods::method() === 'GET') {
            $this->view->mostraFormCertificato([
                'utente' => $cliente,
                'certificato' => $this->certificatoRepo->findByCliente($cliente)
            ]);
            return;
        }
        $this->eseguiCaricamentoCertificato($cliente, $ruolo);
    }

    private function eseguiCaricamentoCertificato(Cliente $cliente, string $ruolo): void
    {
        $medico = HTTPMethods::post('medico');
        $emissione = HTTPMethods::post('data_emissione');
        $file = HTTPMethods::files('file_certificato');
        if (empty($medico) || empty($emissione) || !$file || $file['error'] !== UPLOAD_ERR_OK) {
            $rit = ($ruolo === 'cliente') ? 'carica-certificato' : 'carica-certificato?id=' . $cliente->getId();
            $this->mostraStatoOperazione(false, "Tutti i campi e il file del certificato sono obbligatori.", $rit, "Torna al Form");
            return;
        }
        try {
            $content = file_get_contents($file['tmp_name']);
            $oldCert = $cliente->getCertificatoMedico();
            
            $cert = new CertificatoMedico(new \DateTimeImmutable($emissione), trim($medico), $cliente, $content);
            
            if ($oldCert) {
                $cliente->setCertificatoMedico(null);
                $this->clienteRepo->save($cliente);
                $this->certificatoRepo->delete($oldCert);
            }
            
            $cliente->setCertificatoMedico($cert);
            $this->certificatoRepo->save($cert);
            $this->clienteRepo->save($cliente);
            
            $ritorno = ($ruolo === 'cliente') ? 'profilo' : 'visualizza-profilo?id=' . $cliente->getId();
            $this->mostraStatoOperazione(true, "Certificato caricato con successo!", $ritorno, "Torna al Profilo");
        } catch (\Throwable $e) {
            $rit = ($ruolo === 'cliente') ? 'carica-certificato' : 'carica-certificato?id=' . $cliente->getId();
            $this->mostraStatoOperazione(false, "Errore caricamento certificato: " . $e->getMessage(), $rit, "Torna al Form");
        }
    }

    // =========================================================================
    // 6. CAMBIA PASSWORD (/cambia-password)
    // =========================================================================

    public function cambiaPassword(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt) {
            $this->mostraStatoOperazione(false, "Accesso negato.");
            return;
        }

        $targetId = HTTPMethods::get('id') !== null ? (int)HTTPMethods::get('id') : $idUt;
        if ($targetId !== $idUt) {
            $this->mostraStatoOperazione(false, "Accesso negato. Non è consentito modificare la password degli altri utenti.");
            return;
        }

        $ut = $this->recuperaUtenteLoggato($idUt, $ruolo);
        if (!$ut) {
            $this->mostraStatoOperazione(false, "Utente non trovato o accesso negato.");
            return;
        }
        if (HTTPMethods::method() === 'GET') {
            $this->view->mostraFormCambioPassword();
            return;
        }
        $this->eseguiCambioPassword($ut);
    }

    private function eseguiCambioPassword(Utente $ut): void
    {
        $old = HTTPMethods::post('vecchia_password', '');
        $new = HTTPMethods::post('nuova_password', '');
        $conf = HTTPMethods::post('conferma_password', '');
        if (empty($old) || empty($new) || empty($conf)) {
            $this->mostraStatoOperazione(false, "Tutti i campi password sono obbligatori.", "cambia-password", "Riprova");
            return;
        }
        if ($new !== $conf) {
            $this->mostraStatoOperazione(false, "La nuova password e la conferma non coincidono.", "cambia-password", "Riprova");
            return;
        }
        if (!$ut->verificaPassword($old)) {
            $this->mostraStatoOperazione(false, "La vecchia password non è corretta.", "cambia-password", "Riprova");
            return;
        }
        try {
            $ut->setPassword($new);
            $this->salvaUtenteGenerico($ut);
            $this->mostraStatoOperazione(true, "Password aggiornata con successo!", "profilo", "Torna al Profilo");
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Errore durante il cambio password: " . $e->getMessage(), "cambia-password", "Riprova");
        }
    }

    private function salvaUtenteGenerico(Utente $ut): void
    {
        if ($ut instanceof Cliente) {
            $this->clienteRepo->save($ut);
        } elseif ($ut instanceof Allenatore) {
            $this->allenatoreRepo->save($ut);
        } elseif ($ut instanceof Amministratore) {
            $this->amministratoreRepo->save($ut);
        } else {
            $this->utenteRepo->save($ut);
        }
    }

    // =========================================================================
    // 7. VISUALIZZA GRAFICO (/visualizza-grafico)
    // =========================================================================

    public function visualizzaGrafico(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $cliente = $this->recuperaClienteTarget($ruolo, $idUt);
        if (!$cliente) {
            $this->mostraStatoOperazione(false, "Cliente non trovato o accesso negato.");
            return;
        }
        $tipo = HTTPMethods::get('tipo', 'peso');
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



    public function caricaFotoProfilo(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $ut = ($idUt && $ruolo) ? $this->utenteRepo->findById($idUt) : null;
        if (!$ut) {
            $this->mostraStatoOperazione(false, "Utente non trovato.");
            return;
        }

        $file = HTTPMethods::files('foto_profilo');
        if ($file && in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE])) {
            $this->mostraStatoOperazione(false, "La foto profilo supera la dimensione massima consentita dal server PHP (limite 16MB).", "visualizza-profilo", "Torna al Profilo");
            return;
        }
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->mostraStatoOperazione(false, "Nessun file selezionato o errore durante il caricamento.", "visualizza-profilo", "Torna al Profilo");
            return;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $this->mostraStatoOperazione(false, "Formato file non supportato. Formati consentiti: JPG, PNG, GIF, WEBP.", "visualizza-profilo", "Torna al Profilo");
            return;
        }
        $tmp = $file['tmp_name'];
        if ($file['size'] > 16 * 1024 * 1024) {
            $this->mostraStatoOperazione(false, "La foto profilo non può superare i 16 MB.", "visualizza-profilo", "Torna al Profilo");
            return;
        }
        $mime = mime_content_type($tmp);
        if (!$mime || !str_starts_with($mime, 'image/')) {
            $this->mostraStatoOperazione(false, "Il file caricato non è un'immagine valida.", "visualizza-profilo", "Torna al Profilo");
            return;
        }
        try {
            $ut->setProfilePicture(file_get_contents($tmp));
            $ut->setTipoImmagine($mime);
            $this->salvaUtenteGenerico($ut);
            $this->mostraStatoOperazione(true, "Foto profilo aggiornata con successo!", "visualizza-profilo", "Torna al Profilo");
        } catch (\Throwable $e) {
            $this->mostraStatoOperazione(false, "Errore salvataggio foto: " . $e->getMessage(), "visualizza-profilo", "Torna al Profilo");
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
        $idAll = (int)HTTPMethods::post('id_allenatore', 0);
        if ($idAll <= 0 || ($ruolo !== 'amministratore' && $idAll !== $idLog)) {
            $this->mostraStatoOperazione(false, "Dati non validi o non autorizzato.");
            return;
        }
        $allenatore = $this->allenatoreRepo->findById($idAll);
        if (!$allenatore || !$this->validaPalestraAllenatore($idLog, $ruolo, $allenatore)) {
            $this->mostraStatoOperazione(false, "Allenatore non trovato o non appartenente alla palestra.");
            return;
        }
        $this->eseguiAggiornamentoAbilitazioniInBlocco($allenatore, HTTPMethods::postArray('attivita'), $idLog);
    }

    private function eseguiAggiornamentoAbilitazioniInBlocco(Allenatore $allenatore, array $sel, int $idLog): void
    {
        try {
            foreach ($allenatore->getAttivitaAbilitate() as $c) {
                $allenatore->removeAbilitazione($c);
            }
            $this->utenteRepo->save($allenatore);
            foreach ($sel as $idAtt) {
                $att = $this->attivitaRepo->findById((int)$idAtt);
                if ($att) $allenatore->addAbilitazione($att);
            }
            $this->utenteRepo->save($allenatore);
            $loc = ($allenatore->getId() === $idLog) ? 'profilo' : 'visualizza-profilo?id=' . $allenatore->getId();
            $this->view->redirect($loc);
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
            $targetId = (int)HTTPMethods::request('id', 0);
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

    private function recuperaUtenteLoggato(int $idUtente, ?string $ruolo): ?Utente
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