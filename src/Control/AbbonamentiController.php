<?php
namespace App\Control;

use App\View\Interface\AbbonamentiView;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Amministratore;
use App\Entity\Cliente;
use App\Entity\Palestra;
use App\Entity\Abbonamento;
use App\Entity\AbbonamentoAttivo;
use App\Entity\Iscrizione;

class AbbonamentiController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AbbonamentiView $view,
        private Session $session
    ) {}

    public function gestisciAbbonamento(): void
    {
        $idAdmin = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();

        if (!$idAdmin || $ruolo !== 'amministratore') {
            $this->view->mostraErrore("Accesso negato. Questa operazione è riservata all'Amministratore della palestra.");
            return;
        }

        $idCliente = $_GET['id'] ?? $_POST['id_cliente'] ?? null;
        if (!$idCliente) {
            $this->view->mostraErrore("Identificativo del cliente non specificato.");
            return;
        }

        $cliente = $this->entityManager->find(Cliente::class, (int)$idCliente);
        if (!$cliente) {
            $this->view->mostraErrore("Cliente non trovato.");
            return;
        }

        // Prevenzione IDOR: verifica multitenant
        $admin = $this->entityManager->find(Amministratore::class, $idAdmin);
        if (!$admin) {
            $this->view->mostraErrore("Amministratore non trovato.");
            return;
        }

        $palestra = $this->entityManager->getRepository(Palestra::class)->findOneBy(['amministratore' => $admin]);
        if (!$palestra || !$cliente->getPalestra() || $cliente->getPalestra()->getId() !== $palestra->getId()) {
            $this->view->mostraErrore("Accesso negato. Il cliente non appartiene alla palestra gestita.");
            return;
        }

        $abbonamentiDisponibili = $this->entityManager->getRepository(Abbonamento::class)->findAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $azione = $_POST['azione'] ?? '';

            if ($azione === 'abbonamento') {
                $idAbbonamentoScelto = $_POST['abbonamento_id'] ?? null;
                $dataInizioStr = $_POST['data_inizio_abbonamento'] ?? '';

                if (!$idAbbonamentoScelto) {
                    $this->view->mostraErrore("Nessun abbonamento selezionato.");
                    return;
                }

                $abbonamentoPlan = $this->entityManager->find(Abbonamento::class, (int)$idAbbonamentoScelto);
                if (!$abbonamentoPlan) {
                    $this->view->mostraErrore("Piano di abbonamento non valido.");
                    return;
                }

                try {
                    $dataInizio = $dataInizioStr !== '' ? new \DateTimeImmutable($dataInizioStr) : new \DateTimeImmutable();
                } catch (\Exception $e) {
                    $this->view->mostraErrore("Formato data di inizio non valido.");
                    return;
                }

                // Rimuove l'eventuale abbonamento attivo esistente per evitare record orfani
                $oldAbbonamento = $cliente->getAbbonamento();
                if ($oldAbbonamento !== null) {
                    $cliente->setAbbonamento(null);
                    $this->entityManager->remove($oldAbbonamento);
                    $this->entityManager->flush();
                }

                // Sottoscrizione
                $nuovoAbbonamentoAttivo = new AbbonamentoAttivo($dataInizio, $abbonamentoPlan);
                $cliente->setAbbonamento($nuovoAbbonamentoAttivo);

                $this->entityManager->persist($nuovoAbbonamentoAttivo);
                $this->entityManager->flush();

                $this->view->mostraConferma("Abbonamento del cliente registrato con successo!", "visualizza-profilo?id=" . $cliente->getId());
                return;

            } elseif ($azione === 'crea_tipologia') {
                $tipologia = $_POST['nuova_tipologia'] ?? '';
                $categoria = $_POST['nuova_categoria'] ?? '';
                $durata = $_POST['nuova_durata'] ?? '';

                if (empty($tipologia) || empty($categoria) || empty($durata)) {
                    $this->view->mostraErrore("Tutti i campi per la nuova tipologia sono obbligatori.");
                    return;
                }

                try {
                    $nuovoPlan = new \App\Entity\AbbonamentoDurata($tipologia, $categoria, (int)$durata);
                    $this->entityManager->persist($nuovoPlan);
                    $this->entityManager->flush();

                    header('Location: gestione-abbonamento?id=' . $cliente->getId());
                    exit();
                } catch (\Exception $e) {
                    $this->view->mostraErrore("Errore nella creazione della tipologia: " . $e->getMessage());
                    return;
                }

            } elseif ($azione === 'iscrizione') {
                $dataInizioStr = $_POST['data_inizio_iscrizione'] ?? '';

                try {
                    $dataInizio = $dataInizioStr !== '' ? new \DateTimeImmutable($dataInizioStr) : new \DateTimeImmutable();
                } catch (\Exception $e) {
                    $this->view->mostraErrore("Formato data di inizio non valido.");
                    return;
                }

                try {
                    $oldIscrizione = $cliente->getIscrizione();
                    if ($oldIscrizione !== null) {
                        $oldIscrizione->setDataInizio($dataInizio);
                    } else {
                        $nuovaIscrizione = new Iscrizione($dataInizio, $cliente);
                        $this->entityManager->persist($nuovaIscrizione);
                    }
                    $this->entityManager->flush();
                } catch (\InvalidArgumentException $e) {
                    $this->view->mostraErrore("Errore di validazione: " . $e->getMessage());
                    return;
                }

                $this->view->mostraConferma("Iscrizione annuale aggiornata con successo!", "visualizza-profilo?id=" . $cliente->getId());
                return;
            }

            $this->view->mostraErrore("Azione non riconosciuta.");
            return;
        }

        // Rendering GET
        $this->view->mostraGestioneAbbonamento([
            'cliente' => $cliente,
            'abbonamentoAttivo' => $cliente->getAbbonamento(),
            'iscrizione' => $cliente->getIscrizione(),
            'abbonamentiDisponibili' => $abbonamentiDisponibili
        ]);
    }
}
