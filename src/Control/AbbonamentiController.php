<?php
namespace App\Control;

use App\View\Interface\AbbonamentiView;
use App\View\AbbonamentiViewSmarty;
use App\Foundation\Session;
use App\Entity\Repository\PalestraRepositoryInterface;
use App\Entity\Repository\AbbonamentoRepositoryInterface;
use App\Entity\Repository\ClienteRepositoryInterface;
use App\Entity\Repository\AmministratoreRepositoryInterface;
use App\Entity\Repository\AbbonamentoAttivoRepositoryInterface;
use App\Entity\Repository\IscrizioneRepositoryInterface;
use App\Foundation\Persistence\Repository\DoctrinePalestraRepository;
use App\Foundation\Persistence\Repository\DoctrineAbbonamentoRepository;
use App\Foundation\Persistence\Repository\DoctrineClienteRepository;
use App\Foundation\Persistence\Repository\DoctrineAmministratoreRepository;
use App\Foundation\Persistence\Repository\DoctrineAbbonamentoAttivoRepository;
use App\Foundation\Persistence\Repository\DoctrineIscrizioneRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AbbonamentoAttivo;
use App\Entity\Iscrizione;

class AbbonamentiController
{
    private AbbonamentiView $view;
    private PalestraRepositoryInterface $palestraRepo;
    private AbbonamentoRepositoryInterface $abbonamentoRepo;
    private ClienteRepositoryInterface $clienteRepo;
    private AmministratoreRepositoryInterface $amministratoreRepo;
    private AbbonamentoAttivoRepositoryInterface $abbonamentoAttivoRepo;
    private IscrizioneRepositoryInterface $iscrizioneRepo;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Session $session
    ) {
        $this->view = new AbbonamentiViewSmarty();
        $this->palestraRepo = new DoctrinePalestraRepository($this->entityManager);
        $this->abbonamentoRepo = new DoctrineAbbonamentoRepository($this->entityManager);
        $this->clienteRepo = new DoctrineClienteRepository($this->entityManager);
        $this->amministratoreRepo = new DoctrineAmministratoreRepository($this->entityManager);
        $this->abbonamentoAttivoRepo = new DoctrineAbbonamentoAttivoRepository($this->entityManager);
        $this->iscrizioneRepo = new DoctrineIscrizioneRepository($this->entityManager);
    }

    public function gestisciAbbonamento(): void
    {
        $cliente = $this->verificaErecuperaCliente();
        if (!$cliente) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->gestisciPostAzione($cliente);
            return;
        }

        $this->view->mostraGestioneAbbonamento([
            'cliente' => $cliente,
            'abbonamentoAttivo' => $cliente->getAbbonamento(),
            'iscrizione' => $cliente->getIscrizione(),
            'abbonamentiDisponibili' => $this->abbonamentoRepo->findAll()
        ]);
    }

    private function verificaErecuperaCliente(): ?\App\Entity\Cliente
    {
        $idAdmin = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$idAdmin || $ruolo !== 'amministratore') {
            $this->view->mostraErrore("Accesso negato. Questa operazione è riservata all'Amministratore.", "login", "Effettua il Login");
            return null;
        }
        $idCliente = $_GET['id'] ?? $_POST['id_cliente'] ?? null;
        $cliente = $idCliente ? $this->clienteRepo->findById((int)$idCliente) : null;
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato o non specificato.", "clienti", "Torna ai Clienti");
            return null;
        }
        $admin = $this->amministratoreRepo->findById($idAdmin);
        $palestra = $admin ? $this->palestraRepo->findByAmministratore($admin) : null;
        if (!$palestra || !$cliente->getPalestra() || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraErrore("Accesso negato. Il cliente non appartiene alla palestra gestita.", "dashboard-admin", "Torna alla Dashboard");
            return null;
        }
        return $cliente;
    }

    private function gestisciPostAzione(\App\Entity\Cliente $cliente): void
    {
        $azione = $_POST['azione'] ?? '';
        if ($azione === 'abbonamento') {
            $this->sottoscriviAbbonamento($cliente);
        } elseif ($azione === 'crea_tipologia') {
            $this->creaTipologiaAbbonamento($cliente);
        } elseif ($azione === 'iscrizione') {
            $this->aggiornaIscrizioneCliente($cliente);
        } else {
            $this->view->mostraErrore("Azione non riconosciuta.", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
        }
    }

    private function sottoscriviAbbonamento(\App\Entity\Cliente $cliente): void
    {
        $idPlan = $_POST['abbonamento_id'] ?? null;
        $plan = $idPlan ? $this->abbonamentoRepo->findById((int)$idPlan) : null;
        if (!$plan) {
            $this->view->mostraErrore("Piano di abbonamento non valido.", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
            return;
        }
        $start = $this->parseDataInizio($_POST['data_inizio_abbonamento'] ?? '', $cliente);
        if (!$start) {
            return;
        }
        $this->rimuoviAbbonamentoEsistente($cliente);
        $newAbb = new AbbonamentoAttivo($start, $plan);
        $cliente->setAbbonamento($newAbb);
        $this->abbonamentoAttivoRepo->save($newAbb);
        $this->clienteRepo->save($cliente);
        $this->view->mostraConferma("Abbonamento registrato con successo!", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
    }

    private function parseDataInizio(string $dataStr, \App\Entity\Cliente $cliente): ?\DateTimeImmutable
    {
        try {
            return $dataStr !== '' ? new \DateTimeImmutable($dataStr) : new \DateTimeImmutable();
        } catch (\Exception $e) {
            $this->view->mostraErrore("Formato data non valido.", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
            return null;
        }
    }

    private function rimuoviAbbonamentoEsistente(\App\Entity\Cliente $cliente): void
    {
        $oldAbb = $cliente->getAbbonamento();
        if ($oldAbb !== null) {
            $cliente->setAbbonamento(null);
            $this->abbonamentoAttivoRepo->delete($oldAbb);
            $this->clienteRepo->save($cliente);
        }
    }

    private function creaTipologiaAbbonamento(\App\Entity\Cliente $cliente): void
    {
        $tipologia = $_POST['nuova_tipologia'] ?? '';
        $categoria = $_POST['nuova_categoria'] ?? '';
        $durata = $_POST['nuova_durata'] ?? '';
        if (empty($tipologia) || empty($categoria) || empty($durata)) {
            $this->view->mostraErrore("Campi tipologia incompleti.", "gestione-abbonamento?id=" . $cliente->getId(), "Torna all'Abbonamento");
            return;
        }
        try {
            $nuovoPlan = new \App\Entity\AbbonamentoDurata($tipologia, $categoria, (int)$durata);
            $this->abbonamentoRepo->save($nuovoPlan);
            header('Location: gestione-abbonamento?id=' . $cliente->getId());
            exit();
        } catch (\Exception $e) {
            $this->view->mostraErrore("Errore creazione tipologia: " . $e->getMessage(), "gestione-abbonamento?id=" . $cliente->getId(), "Torna all'Abbonamento");
        }
    }

    private function aggiornaIscrizioneCliente(\App\Entity\Cliente $cliente): void
    {
        $start = $this->parseDataInizio($_POST['data_inizio_iscrizione'] ?? '', $cliente);
        if (!$start) {
            return;
        }
        try {
            $oldIscrizione = $cliente->getIscrizione();
            if ($oldIscrizione !== null) {
                $oldIscrizione->setDataInizio($start);
                $this->iscrizioneRepo->save($oldIscrizione);
            } else {
                $nuovaIscrizione = new Iscrizione($start, $cliente);
                $this->iscrizioneRepo->save($nuovaIscrizione);
            }
            $this->clienteRepo->save($cliente);
            $this->view->mostraConferma("Iscrizione annuale aggiornata con successo!", "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
        } catch (\InvalidArgumentException $e) {
            $this->view->mostraErrore("Errore di validazione: " . $e->getMessage(), "visualizza-profilo?id=" . $cliente->getId(), "Torna al Profilo");
        }
    }
}
