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
use App\Entity\Amministratore;
use App\Entity\Allenatore;
use App\Entity\Utente;
use App\Entity\Cliente;
use App\Entity\Attivita;
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

    // =========================================================================
    // 1. VISUALIZZA PROFILO (/profilo, /visualizza-profilo)
    // =========================================================================

    public function visualizzaProfilo(): void 
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt) {
            $this->view->mostraErrore("Sessione non valida. Effettua il login.");
            return;
        }
        $isSelf = true;
        $ut = $this->determinaUtenteProfilo($idUt, $ruolo, $isSelf);
        if (!$ut) {
            $this->view->mostraErrore("Profilo non trovato o accesso negato.");
            return;
        }
        $dati = [
            'utente' => $ut, 'isClient' => ($ut instanceof Cliente), 'isTrainer' => ($ut instanceof Allenatore),
            'isSelf' => $isSelf, 'nome' => $ut->getNome(), 'cognome' => $ut->getCognome(), 'email' => $ut->getEmail(),
            'cf' => $ut->getCF(), 'fotoProfilo' => $ut->getProfilePicture() ? base64_encode($ut->getProfilePicture()) : null,
            'abbonamento' => null, 'abbonamento_attivo' => false, 'has_progress' => false, 'parametri' => null,
            'certificato' => null, 'attivitaAbilitate' => null, 'attivitaNonAbilitate' => [], 'tutteAttivita' => []
        ];
        $dati = ($ut instanceof Cliente) ? array_merge($dati, $this->caricaDatiClient($ut, $ruolo)) : (($ut instanceof Allenatore) ? array_merge($dati, $this->caricaDatiTrainer($ut)) : $dati);
        $this->view->mostraProfilo($dati);
    }

    private function determinaUtenteProfilo(int $idUt, string $ruolo, bool &$isSelf): ?Utente
    {
        $isSelf = !isset($_GET['id']);
        if ($isSelf) {
            return $this->recuperaUtenteLoggato($this->entityManager, $idUt, $ruolo);
        }
        $targetId = (int)$_GET['id'];
        $utente = $this->entityManager->find(Utente::class, $targetId);
        if ($ruolo === 'amministratore' || $ruolo === 'allenatore') {
            $pal = ($ruolo === 'amministratore') ?
                $this->palestraRepo->findByAmministratore($this->entityManager->find(Amministratore::class, $idUt)) :
                ($this->entityManager->find(Allenatore::class, $idUt) ? $this->entityManager->find(Allenatore::class, $idUt)->getPalestra() : null);
            if (!$utente || !method_exists($utente, 'getPalestra') || !$utente->getPalestra() || !$pal || $utente->getPalestra()->getId() !== $pal->getId()) {
                return null;
            }
        }
        return $utente;
    }

    private function caricaDatiClient(Cliente $ut, string $ruolo): array
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
            'has_progress' => count($this->progressoRepo->findByCliente($ut)) > 0
        ];
    }

    private function caricaDatiTrainer(Allenatore $ut): array
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

    public function modificaAnagrafica(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        if (!$idUtente) {
            $this->view->mostraErrore("Sessione scaduta.");
            return;
        }
        $ruolo = $this->session->getLoggedUserRole();
        $isSelf = true;
        $utente = $this->determinaUtenteModifica($idUtente, $ruolo, $isSelf);
        if (!$utente) {
            $this->view->mostraErrore("Profilo non trovato o accesso negato.");
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormModifica(['utente' => $utente, 'isClient' => ($utente instanceof Cliente), 'isSelf' => $isSelf, 'ruolo' => $ruolo]);
            return;
        }
        $ritorno = $isSelf ? 'profilo' : 'visualizza-profilo?id=' . $utente->getId();
        $this->eseguiSalvataggioAnagrafica($utente, ($utente instanceof Cliente), $ruolo, $ritorno);
    }

    private function determinaUtenteModifica(int $idUt, string $ruolo, bool &$isSelf): ?Utente
    {
        $isSelf = !isset($_GET['id']) && !isset($_POST['id']);
        if ($isSelf) {
            return $this->recuperaUtenteLoggato($this->entityManager, $idUt, $ruolo);
        }
        if ($ruolo === 'allenatore') {
            return null;
        }
        $targetId = isset($_GET['id']) ? (int)$_GET['id'] : (int)$_POST['id'];
        $utente = $this->entityManager->find(Utente::class, $targetId);
        if ($ruolo === 'amministratore') {
            $admin = $this->entityManager->find(Amministratore::class, $idUt);
            $pal = $this->palestraRepo->findByAmministratore($admin);
            if (!$utente || !method_exists($utente, 'getPalestra') || !$utente->getPalestra() || !$pal || $utente->getPalestra()->getId() !== $pal->getId()) {
                return null;
            }
        }
        return $utente;
    }

    private function eseguiSalvataggioAnagrafica(Utente $ut, bool $isClient, string $ruolo, string $rit): void
    {
        $nome = $_POST['nome'] ?? '';
        $cognome = $_POST['cognome'] ?? '';
        $res = $_POST['indirizzo'] ?? '';
        $pag = $_POST['metodo_pagamento'] ?? '';
        if (empty($nome) || empty($cognome) || empty($res) || ($isClient && $ruolo === 'amministratore' && empty($pag))) {
            $this->view->mostraErrore("Campi obbligatori mancanti.", $rit, "Torna al Profilo");
            return;
        }
        try {
            $ut->setNome($nome); $ut->setCognome($cognome); $ut->setIndirizzo($res);
            if ($isClient && $ut instanceof Cliente) {
                if (method_exists($ut, 'setIndirizzoDiDomicilio')) $ut->setIndirizzoDiDomicilio($_POST['indirizzo_domicilio'] ?? '');
                if ($ruolo === 'amministratore') $ut->setMetodoDiPagamento($pag);
            }
            $this->utenteRepo->save($ut);
            header('Location: ' . $rit);
            exit();
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraErrore("Errore: " . $e->getMessage(), $rit, "Torna al Profilo");
        }
    }

    // =========================================================================
    // 3. AGGIORNA MISURE CORPOREE (/aggiorna-misure)
    // =========================================================================

    public function aggiornaMisureCorporee(): void 
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $cliente = $idUt ? $this->recuperaClienteTarget($ruolo, $idUt) : null;
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato o accesso non consentito.");
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

    public function inserisciMisureCorporee(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || $ruolo === 'allenatore') {
            $this->view->mostraErrore("Azione non consentita.");
            return;
        }
        $cliente = $this->recuperaClienteTarget($ruolo, $idUt);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato o accesso non consentito.");
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
            $this->view->mostraErrore("Peso e altezza sono obbligatori.");
            return;
        }
        $f = fn($key) => !empty($_POST[$key]) ? (float)$_POST[$key] : null;
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
            $this->view->mostraErrore("Dati non validi: " . $e->getMessage());
        }
    }

    // =========================================================================
    // 5. CARICA CERTIFICATO MEDICO (/carica-certificato)
    // =========================================================================

    public function caricaCertificato(): void 
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idUt || $ruolo === 'allenatore') {
            $this->view->mostraErrore("Accesso negato.");
            return;
        }
        $cliente = $this->recuperaClienteTarget($ruolo, $idUt);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato o accesso non consentito.");
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
        if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $this->view->mostraErrore("File troppo grande.", $rit, "Torna al Profilo");
            return;
        }
        $medico = $_POST['medico'] ?? null;
        $emissione = $_POST['data_emissione'] ?? null;
        if (empty($medico) || empty($emissione) || !isset($_FILES['file_certificato']) || $_FILES['file_certificato']['error'] !== UPLOAD_ERR_OK) {
            $this->view->mostraErrore("Dati certificato incompleti o file non valido.", $rit, "Torna al Profilo");
            return;
        }
        try {
            $content = file_get_contents($_FILES['file_certificato']['tmp_name']);
            $vecchio = $cliente->getCertificatoMedico();
            $cert = new CertificatoMedico(new \DateTimeImmutable($emissione), $medico, $cliente, $content);
            $this->certificatoRepo->save($cert);
            $cliente->setCertificatoMedico($cert);
            $this->clienteRepo->save($cliente);
            if ($vecchio) $this->certificatoRepo->delete($vecchio);
            $this->view->mostraConfermaModifica("Certificato medico caricato correttamente.", $rit, "Torna al Profilo");
        } catch (\Exception $e) {
            $this->view->mostraErrore("Errore: " . $e->getMessage(), $rit, "Torna al Profilo");
        }
    }

    // =========================================================================
    // 6. CAMBIA PASSWORD (/cambia-password)
    // =========================================================================

    public function cambiaPassword(): void
    {
        $idUt = $this->session->getLoggedUserId();
        if (!$idUt) {
            $this->view->mostraErrore("Sessione scaduta o non valida.");
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
            $this->view->mostraErrore("Campi vuoti o password non coincidenti.", "profilo", "Torna al Profilo");
            return;
        }
        $this->eseguiCambioPassword($idUt, $this->session->getLoggedUserRole(), $old, $new);
    }

    private function eseguiCambioPassword(int $idUt, string $ruolo, string $old, string $new): void
    {
        $ut = $this->recuperaUtenteLoggato($this->entityManager, $idUt, $ruolo);
        if (!$ut || !$ut->verificaPassword($old)) {
            $this->view->mostraErrore("Utente non trovato o password errata.", "profilo", "Torna al Profilo");
            return;
        }
        try {
            $ut->setPassword($new);
            $this->utenteRepo->save($ut);
            $this->view->mostraConfermaModifica("Password aggiornata con successo.", "profilo", "Torna al Profilo");
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraErrore("Errore: " . $e->getMessage(), "profilo", "Torna al Profilo");
        }
    }

    // =========================================================================
    // 7. VISUALIZZA GRAFICO (/visualizza-grafico)
    // =========================================================================

    public function visualizzaGrafico(): void
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $cliente = $idUt ? $this->recuperaClienteTarget($ruolo, $idUt) : null;
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato o accesso non consentito.");
            return;
        }
        $tipo = $_GET['tipo'] ?? 'peso';
        if (!in_array($tipo, ['peso', 'superiore', 'inferiore'])) $tipo = 'peso';
        $storico = array_reverse($this->parametriRepo->findByCliente($cliente));
        $valori = $this->mappaValoriGrafico($storico, $tipo);
        $titoli = ['peso' => 'Andamento Peso Corporeo', 'superiore' => 'Andamento Misure Parte Superiore (Media)', 'inferiore' => 'Andamento Misure Parte Inferiore (Media)'];
        $this->view->mostraGrafico([
            'utente' => $cliente, 'tipo' => $tipo,
            'titolo' => $titoli[$tipo] ?? 'Grafico',
            'punti' => $this->costruisciPuntiGrafico($storico, $valori)
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
                $nonNull = array_filter($sub, fn($v) => $v !== null);
                $media = count($nonNull) > 0 ? array_sum($nonNull) / count($nonNull) : 0.0;
                $valori[] = round($media, 2);
            }
        }
        return $valori;
    }

    private function costruisciPuntiGrafico(array $storico, array $valori): array
    {
        $punti = [];
        $minVal = count($valori) ? min($valori) - 2 : 0;
        $maxVal = count($valori) ? max($valori) + 2 : 10;
        $range = $maxVal - $minVal ?: 1;
        $count = count($storico);
        foreach ($storico as $i => $m) {
            $val = $valori[$i];
            $x = 40 + ($i * (390 / ($count - 1 ?: 1)));
            $y = 20 + 120 - (($val - $minVal) / $range * 120);
            $punti[] = [
                'x' => $x, 'y' => $y, 'valore' => $val, 'data' => $m->getData()->format('d/m')
            ];
        }
        return $punti;
    }

    // =========================================================================
    // 8. CARICA FOTO PROFILO (/carica-foto)
    // =========================================================================

    public function caricaFotoProfilo(): void 
    {
        $idUt = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        $ut = $idUt ? $this->recuperaUtenteLoggato($this->entityManager, $idUt, $ruolo) : null;
        if (!$ut) {
            $this->view->mostraErrore("Profilo non trovato.");
            return;
        }
        if (isset($_FILES['foto_profilo']) && in_array($_FILES['foto_profilo']['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE])) {
            $this->view->mostraErrore("Dimensione file eccessiva.", "profilo", "Torna al Profilo");
            return;
        }
        if (!isset($_FILES['foto_profilo']) || $_FILES['foto_profilo']['error'] !== UPLOAD_ERR_OK) {
            $this->view->mostraErrore("File non valido.", "profilo", "Torna al Profilo");
            return;
        }
        $this->eseguiCaricamentoFoto($ut);
    }

    private function eseguiCaricamentoFoto(Utente $ut): void
    {
        $tmp = $_FILES['foto_profilo']['tmp_name'];
        if ($_FILES['foto_profilo']['size'] > 60 * 1024) {
            $this->view->mostraErrore("La dimensione supera i 60 KB.", "profilo", "Torna al Profilo");
            return;
        }
        $info = @getimagesize($tmp);
        if ($info === false || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            $this->view->mostraErrore("Formato non consentito (ammessi solo JPG/PNG).", "profilo", "Torna al Profilo");
            return;
        }
        $content = file_get_contents($tmp);
        if ($content !== false) {
            $ut->setProfilePicture($content);
            $this->utenteRepo->save($ut);
            $this->view->mostraConfermaModifica("Foto profilo aggiornata con successo.", "profilo", "Torna al Profilo");
        }
    }

    // =========================================================================
    // 9. AGGIUNGI ABILITAZIONE ALLENATORE (/aggiungi-attivita-profilo)
    // =========================================================================

    public function aggiungiAttivitaAllenatore(): void
    {
        $idLog = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idLog || ($ruolo !== 'amministratore' && $ruolo !== 'allenatore')) {
            $this->view->mostraErrore("Azione non consentita.");
            return;
        }
        $idAll = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;
        $idAtt = isset($_POST['id_attivita']) ? (int)$_POST['id_attivita'] : 0;
        if ($idAll <= 0 || $idAtt <= 0 || ($ruolo !== 'amministratore' && $idAll !== $idLog)) {
            $this->view->mostraErrore("Dati non validi o non autorizzato.");
            return;
        }
        $allenatore = $this->entityManager->find(Allenatore::class, $idAll);
        $attivita = $this->entityManager->find(Attivita::class, $idAtt);
        if (!$allenatore || !$attivita || !$this->validaPalestraAllenatore($idLog, $ruolo, $allenatore)) {
            $this->view->mostraErrore("Risorsa non valida o non appartenente alla palestra.");
            return;
        }
        $this->eseguiAbilitazione($allenatore, $attivita, 'add', $idLog);
    }

    // =========================================================================
    // 10. RIMUOVI ABILITAZIONE ALLENATORE (/rimuovi-attivita-profilo)
    // =========================================================================

    public function rimuoviAttivitaAllenatore(): void
    {
        $idLog = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idLog || ($ruolo !== 'amministratore' && $ruolo !== 'allenatore')) {
            $this->view->mostraErrore("Azione non consentita.");
            return;
        }
        $idAll = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;
        $idAtt = isset($_POST['id_attivita']) ? (int)$_POST['id_attivita'] : 0;
        if ($idAll <= 0 || $idAtt <= 0 || ($ruolo !== 'amministratore' && $idAll !== $idLog)) {
            $this->view->mostraErrore("Dati non validi o non autorizzato.");
            return;
        }
        $allenatore = $this->entityManager->find(Allenatore::class, $idAll);
        $attivita = $this->entityManager->find(Attivita::class, $idAtt);
        if (!$allenatore || !$attivita || !$this->validaPalestraAllenatore($idLog, $ruolo, $allenatore)) {
            $this->view->mostraErrore("Risorsa non valida o non appartenente alla palestra.");
            return;
        }
        $this->eseguiAbilitazione($allenatore, $attivita, 'remove', $idLog);
    }

    // =========================================================================
    // 11. AGGIORNA ABILITAZIONI ALLENATORE IN BLOCCO (/aggiorna-abilitazioni-profilo)
    // =========================================================================

    public function aggiornaAbilitazioniAllenatore(): void
    {
        $idLog = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idLog || ($ruolo !== 'amministratore' && $ruolo !== 'allenatore')) {
            $this->view->mostraErrore("Azione non consentita.");
            return;
        }
        $idAll = isset($_POST['id_allenatore']) ? (int)$_POST['id_allenatore'] : 0;
        if ($idAll <= 0 || ($ruolo !== 'amministratore' && $idAll !== $idLog)) {
            $this->view->mostraErrore("Dati non validi o non autorizzato.");
            return;
        }
        $allenatore = $this->entityManager->find(Allenatore::class, $idAll);
        if (!$allenatore || !$this->validaPalestraAllenatore($idLog, $ruolo, $allenatore)) {
            $this->view->mostraErrore("Allenatore non trovato o non appartenente alla palestra.");
            return;
        }
        $this->eseguiAggiornamentoAbilitazioniInBlocco($allenatore, $_POST['attivita'] ?? [], $idLog);
    }

    private function eseguiAggiornamentoAbilitazioniInBlocco(Allenatore $allenatore, array $sel, int $idLog): void
    {
        try {
            foreach ($allenatore->getAttivitaAbilitate() as $c) {
                $allenatore->removeAbilitazione($c);
            }
            $this->utenteRepo->save($allenatore);
            foreach ($sel as $idAtt) {
                $att = $this->entityManager->find(Attivita::class, (int)$idAtt);
                if ($att) $allenatore->addAbilitazione($att);
            }
            $this->utenteRepo->save($allenatore);
            $loc = ($allenatore->getId() === $idLog) ? 'profilo' : 'visualizza-profilo?id=' . $allenatore->getId();
            header('Location: ' . $loc);
            exit();
        } catch (\Throwable $e) {
            $this->view->mostraErrore("Impossibile aggiornare abilitazioni: " . $e->getMessage());
        }
    }

    // =========================================================================
    // HELPER GENERALI
    // =========================================================================

    private function validaPalestraAllenatore(int $idLog, string $ruolo, Allenatore $allenatore): bool
    {
        if ($ruolo === 'amministratore') {
            $admin = $this->entityManager->find(Amministratore::class, $idLog);
            $pal = $this->palestraRepo->findByAmministratore($admin);
            if (!$pal || $allenatore->getPalestra()->getId() !== $pal->getId()) {
                return false;
            }
        }
        return true;
    }

    private function eseguiAbilitazione(Allenatore $allenatore, Attivita $attivita, string $azione, int $idLog): void
    {
        try {
            if ($azione === 'add') {
                $allenatore->addAbilitazione($attivita);
            } else {
                $allenatore->removeAbilitazione($attivita);
            }
            $this->utenteRepo->save($allenatore);
            $loc = ($allenatore->getId() === $idLog) ? 'profilo' : 'visualizza-profilo?id=' . $allenatore->getId();
            header('Location: ' . $loc);
            exit();
        } catch (\Throwable $e) {
            $this->view->mostraErrore("Impossibile modificare l'abilitazione: " . $e->getMessage());
        }
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
            $pal = ($ruolo === 'amministratore') ?
                $this->palestraRepo->findByAmministratore($this->entityManager->find(Amministratore::class, $idUtente)) :
                ($this->entityManager->find(Allenatore::class, $idUtente) ? $this->entityManager->find(Allenatore::class, $idUtente)->getPalestra() : null);
            if (!$pal || !$cliente->getPalestra() || $cliente->getPalestra()->getId() !== $pal->getId()) {
                return null;
            }
        }
        return $cliente;
    }

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
}