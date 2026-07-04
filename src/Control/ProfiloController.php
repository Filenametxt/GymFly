<?php

namespace App\Control;

use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\ParametriRepositoryInterface;
use App\Entity\Repository\CertificatoMedicoRepositoryInterface;
use App\View\Interface\ProfiloView;
use App\Foundation\Session;
use App\Entity\Parametri;
use App\Entity\CertificatoMedico;
use App\Infrastructure\Doctrine\EntityManagerFactory;
use App\Entity\Amministratore;
use App\Entity\Palestra;
use App\Entity\Allenatore;

class ProfiloController 
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepo,
        private ParametriRepositoryInterface $parametriRepo,
        private CertificatoMedicoRepositoryInterface $certificatoRepo,
        private ProfiloView $view,
        private Session $session
    ) {}

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

        // Determina l'ID del cliente da visualizzare
        $targetId = $idUtente;
        if ($ruolo === 'amministratore' || $ruolo === 'allenatore') {
            $targetId = isset($_GET['id']) ? (int)$_GET['id'] : null;
            if (!$targetId) {
                $this->view->mostraErrore("ID cliente non specificato.");
                return;
            }
        }

        $cliente = $this->clienteRepo->findById($targetId);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato.");
            return;
        }

        // Controllo di sicurezza (Anti-IDOR) per amministratore e allenatore
        if ($ruolo === 'amministratore' || $ruolo === 'allenatore') {
            $entityManager = EntityManagerFactory::create();
            $palestraUtente = null;
            if ($ruolo === 'amministratore') {
                $adminObj = $entityManager->find(Amministratore::class, $idUtente);
                $palestraUtente = $entityManager->getRepository(Palestra::class)->findOneBy(['amministratore' => $adminObj]);
            } else {
                $allenatoreObj = $entityManager->find(Allenatore::class, $idUtente);
                $palestraUtente = $allenatoreObj ? $allenatoreObj->getPalestra() : null;
            }

            // Verifica che il cliente appartenga alla stessa palestra dell'utente loggato
            if (!$palestraUtente || !$cliente->getPalestra() || $cliente->getPalestra()->getId() !== $palestraUtente->getId()) {
                $this->view->mostraErrore("Accesso negato. Non sei autorizzato a visualizzare questo profilo.");
                return;
            }
        }

        $ultimiParametri = $this->parametriRepo->findUltimaByCliente($cliente);
        $ultimoCertificato = $this->certificatoRepo->findByCliente($cliente);

        // Costruiamo l'array con TUTTI i dati presenti nella schermata "PAGE 17"
        $datiProfilo = [
            'utente' => $cliente,
            'abbonamento' => $cliente->getAbbonamento(),
            // Anagrafica e Recapiti
            'nome' => $cliente->getNome(),
            'cognome' => $cliente->getCognome(),
            'email' => $cliente->getEmail(),
            'cf' => $cliente->getCF(),
            // Il BLOB viene codificato in Base64 per essere usato direttamente nel tag <img> del template.
            // Es: <img src="data:image/jpeg;base64,{$datiProfilo.fotoProfilo}">
            'fotoProfilo' => $cliente->getProfilePicture() ? base64_encode($cliente->getProfilePicture()) : null,
            
            // Stato Abbonamento (presente nel mock-up UX)
            'abbonamento_attivo' => $cliente->isAbbonamentoAttivo(),
            
            // Parametri Biometrici e Misure Dettagliate (PAGE 17)
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

        $cliente = $this->clienteRepo->findById($idUtente);
        if (!$cliente) {
            $this->view->mostraErrore("Profilo inesistente.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostForm = true; // a helper flag if needed, or simply render
            $this->view->mostraFormModifica([
                'utente' => $cliente
            ]);
            return;
        }

        // Recupero dei campi input text
        $nuovoNome = $_POST['nome'] ?? null;
        $nuovoCognome = $_POST['cognome'] ?? null;
        $nuovoIndirizzoResidenza = $_POST['indirizzo'] ?? null; // Ereditato da Utente
        $nuovoIndirizzoDomicilio = $_POST['indirizzo_domicilio'] ?? null; // Specifico di Cliente
        $nuovoMetodoPagamento = $_POST['metodo_pagamento'] ?? null;

        if (empty($nuovoNome) || empty($nuovoCognome) || empty($nuovoIndirizzoResidenza) || empty($nuovoMetodoPagamento)) {
            $this->view->mostraErrore("I campi Nome, Cognome, Residenza e Metodo di Pagamento sono obbligatori.");
            return;
        }

        try {
            // Aggiorniamo l'entità
            $cliente->setNome($nuovoNome);
            $cliente->setCognome($nuovoCognome);
            $cliente->setIndirizzo($nuovoIndirizzoResidenza);
            if (method_exists($cliente, 'setIndirizzoDiDomicilio')) {
                $cliente->setIndirizzoDiDomicilio($nuovoIndirizzoDomicilio);
            }
            $cliente->setMetodoDiPagamento($nuovoMetodoPagamento);

            $this->clienteRepo->save($cliente);
            $this->view->mostraConfermaModifica("Modifiche salvate con successo.");
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraErrore("Errore di validazione: " . $e->getMessage());
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

        $cliente = $this->clienteRepo->findById($idUtente);
        if (!$cliente) {
            $this->view->mostraErrore("Profilo non trovato.");
            return;
        }

        if (!isset($_FILES['foto_profilo']) || $_FILES['foto_profilo']['error'] !== UPLOAD_ERR_OK) {
            $this->view->mostraErrore("File immagine non valido.");
            return;
        }

        // Legge il contenuto binario del file temporaneo
        $fileTmpPath = $_FILES['foto_profilo']['tmp_name'];
        $fileContent = file_get_contents($fileTmpPath);

        if ($fileContent !== false) {
            $cliente->setProfilePicture($fileContent);
            $this->clienteRepo->save($cliente);
            $this->view->mostraConfermaModifica("Foto profilo aggiornata con successo.");
        } else {
            $this->view->mostraErrore("Impossibile leggere il contenuto del file immagine.");
        }
    }

    /**
     * 4. AGGIORNA MISURE CORPOREE (PARAMETRI BIOMETRICI)
     */
    public function aggiornaMisureCorporee(): void 
    {
        $idUtente = $this->session->getLoggedUserId();
        if (!$idUtente) {
            $this->view->mostraErrore("Effettua il login.");
            return;
        }

        $cliente = $this->clienteRepo->findById($idUtente);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormMisure([
                'utente' => $cliente
            ]);
            return;
        }

        $peso = isset($_POST['peso']) ? (float)$_POST['peso'] : 0.0;
        $altezza = isset($_POST['altezza']) ? (float)$_POST['altezza'] : 0.0;

        if ($peso <= 0 || $altezza <= 0) {
            $this->view->mostraErrore("Peso e altezza sono obbligatori.");
            return;
        }

        // Raccolta completa delle misure antropometriche (PAGE 17)
        // Se lasciati vuoti o impostati a 0, vengono considerati null (opzionali)
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
            $this->view->mostraConfermaModifica("Misure aggiornate con successo nel tuo storico biometrico.");
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
        if (!$idUtente) {
            $this->view->mostraErrore("Utente non autenticato.");
            return;
        }

        $cliente = $this->clienteRepo->findById($idUtente);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato.");
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view->mostraFormCertificato([
                'utente' => $cliente
            ]);
            return;
        }

        // Controlla se l'upload ha superato il limite di post_max_size del server
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $this->view->mostraErrore("La dimensione del file supera il limite massimo consentito dal server (post_max_size). Riduci le dimensioni del file PDF.");
            return;
        }

        $medico = $_POST['medico'] ?? null;
        $dataEmissioneStr = $_POST['data_emissione'] ?? null;

        if (empty($medico) || empty($dataEmissioneStr)) {
            $this->view->mostraErrore("Dati del certificato incompleti. Assicurati di aver inserito il nome del medico e la data di emissione.");
            return;
        }

        if (!isset($_FILES['file_certificato']) || $_FILES['file_certificato']['error'] !== UPLOAD_ERR_OK) {
            $this->view->mostraErrore("File PDF del certificato mancante o corrotto.");
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
            
            $this->view->mostraConfermaModifica("Certificato medico caricato correttamente. La nuova scadenza è il " . $certificato->getDataScadenza()->format('d/m/Y'));
        } catch (\Exception $e) {
            $this->view->mostraErrore("Errore nell'elaborazione del certificato: " . $e->getMessage());
        }
    }
}