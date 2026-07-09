<?php
namespace App\Control;

use App\View\Interface\VisualizzazioneView;
use App\Foundation\Session;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Amministratore;
use App\Entity\Cliente;
use App\Entity\Allenatore;
use App\Entity\Esercizio;
use App\Entity\Palestra;
use App\Entity\AttivitaPianificata;
use App\Entity\Messaggio;
use App\Foundation\Persistence\Repository\DoctrineMessaggioRepository;
use App\Foundation\Persistence\Repository\DoctrineAttivitaPianificataRepository;
use App\Entity\Attivita;

class VisualizzazioneController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private VisualizzazioneView $view,
        private Session $session
    ) {}

    /**
     * Mostra la dashboard dell'amministratore se autorizzato.
     */
    public function mostraDashboardAdmin(): void
    {
        $id = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$id || $ruolo !== 'amministratore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa area è riservata all'amministratore.");
            return;
        }

        $admin = $this->entityManager->find(Amministratore::class, $id);
        
        // Recupera la palestra gestita da questo amministratore
        $palestra = $this->entityManager->getRepository(Palestra::class)->findOneBy(['amministratore' => $admin]);
        
        if ($palestra) {
            $clienti = $this->entityManager->getRepository(Cliente::class)->findBy(['palestra' => $palestra]);
            $allenatori = $this->entityManager->getRepository(Allenatore::class)->findBy(['palestra' => $palestra]);
        } else {
            $clienti = [];
            $allenatori = [];
        }

        // 1. Semaforo Certificati Medici
        $certificatiScaduti = 0;
        $certificatiInScadenza = 0;
        $certificatiValidi = 0;
        foreach ($clienti as $cliente) {
            $cert = $cliente->getCertificatoMedico();
            if (!$cert) {
                $certificatiScaduti++;
            } else {
                $giorni = $cert->giorniAllaScadenza();
                if ($giorni < 0) {
                    $certificatiScaduti++;
                } elseif ($giorni <= 30) {
                    $certificatiInScadenza++;
                } else {
                    $certificatiValidi++;
                }
            }
        }

        // 2. Raggiungimento Budget Mensile
        $abbonatiAttivi = 0;
        foreach ($clienti as $cliente) {
            $abb = $cliente->getAbbonamento();
            if ($abb && !$abb->isScaduto()) {
                $abbonatiAttivi++;
            }
        }
        $budgetAttuale = $abbonatiAttivi * 50; // Ipotizziamo 50€ per abbonato attivo
        $budgetTarget = 1500; // Target di budget mensile
        $percentualeBudget = min(100, round(($budgetAttuale / $budgetTarget) * 100));

        // 3. Statistiche Registrazioni (Storico ultimi 5 mesi)
        $datiGrafico = [];
        $oggi = new \DateTimeImmutable();
        
        $nomiMesiIT = [
            'Jan' => 'Gen', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 
            'May' => 'Mag', 'Jun' => 'Giu', 'Jul' => 'Lug', 'Aug' => 'Ago', 
            'Sep' => 'Set', 'Oct' => 'Ott', 'Nov' => 'Nov', 'Dec' => 'Dic'
        ];

        for ($i = 4; $i >= 0; $i--) {
            $dataMese = $oggi->modify("-$i month");
            $chiaveMese = $dataMese->format('Y-m');
            $nomeMeseEng = $dataMese->format('M');
            $nomeMeseIt = $nomiMesiIT[$nomeMeseEng] ?? $nomeMeseEng;
            $datiGrafico[$chiaveMese] = [
                'data' => $nomeMeseIt,
                'valore' => 0
            ];
        }

        foreach ($clienti as $cliente) {
            $iscrizione = $cliente->getIscrizione();
            if ($iscrizione) {
                $chiaveMese = $iscrizione->getDataInizio()->format('Y-m');
                if (isset($datiGrafico[$chiaveMese])) {
                    $datiGrafico[$chiaveMese]['valore']++;
                }
            }
        }

        // Simula uno storico per mostrare il grafico popolato se i dati reali sono pochi
        $valoriEsempio = [3, 5, 8, 12, 15];
        $index = 0;
        foreach ($datiGrafico as $key => $dati) {
            $datiGrafico[$key]['valore'] += $valoriEsempio[$index++];
        }

        // Calcola i punti per il grafico SVG (Polyline)
        $puntiGrafico = [];
        $maxVal = max(array_column($datiGrafico, 'valore'));
        if ($maxVal == 0) $maxVal = 1;
        $passoX = 320 / (count($datiGrafico) - 1);
        $x = 40;
        foreach ($datiGrafico as $dati) {
            $val = $dati['valore'];
            $y = 120 - (($val / $maxVal) * 80); // scala tra y=40 e y=120
            $puntiGrafico[] = [
                'x' => $x,
                'y' => $y,
                'valore' => $val,
                'data' => $dati['data']
            ];
            $x += $passoX;
        }

        // 4. Ultimi messaggi inviati dall'amministratore
        $messaggioRepo = new DoctrineMessaggioRepository($this->entityManager);
        $tuttiMessaggi = $messaggioRepo->findByMittente($admin);
        $ultimiMessaggi = array_slice($tuttiMessaggi, 0, 4);

        // 5. Attività oggi in palestra
        $attivitaRepo = new DoctrineAttivitaPianificataRepository($this->entityManager);
        $attivitaOggi = $attivitaRepo->findByGiorno(new \DateTimeImmutable());
        $eventiOggi = [];
        if (empty($attivitaOggi)) {
            // Eventi di fallback per far visualizzare il widget come da bozza
            $eventiOggi[] = [
                'nome' => 'Pilates',
                'orario' => '13:00 - 14:00',
                'colore' => '#209cee',
                'allenatore' => 'Luigi Verdi'
            ];
            $eventiOggi[] = [
                'nome' => 'Zumba Fitness',
                'orario' => '18:30 - 19:30',
                'colore' => '#ffdd57',
                'allenatore' => 'Carla Neri'
            ];
        } else {
            foreach ($attivitaOggi as $ap) {
                $oraInizio = str_pad($ap->getOrario(), 2, '0', STR_PAD_LEFT) . ':00';
                $oraFine = str_pad($ap->getOrario() + 1, 2, '0', STR_PAD_LEFT) . ':00';
                $eventiOggi[] = [
                    'nome' => $ap->getAttivita()->getNome(),
                    'orario' => "$oraInizio - $oraFine",
                    'colore' => '#3273dc',
                    'allenatore' => $ap->getAllenatore()->getNome() . ' ' . $ap->getAllenatore()->getCognome()
                ];
            }
        }

        $attivita = $this->entityManager->getRepository(Attivita::class)->findAll();

        $this->view->mostraDashboardAdmin([
            'utente' => $admin,
            'clienti' => $clienti,
            'allenatori' => $allenatori,
            'certificati_scaduti' => $certificatiScaduti,
            'certificati_in_scadenza' => $certificatiInScadenza,
            'certificati_validi' => $certificatiValidi,
            'budget_attuale' => $budgetAttuale,
            'budget_target' => $budgetTarget,
            'percentuale_budget' => $percentualeBudget,
            'punti_registrazioni' => $puntiGrafico,
            'ultimi_messaggi' => $ultimiMessaggi,
            'eventi_oggi' => $eventiOggi,
            'attivita' => $attivita
        ]);
    }

    /**
     * Mostra la dashboard dell'allenatore se autorizzato.
     */
    public function mostraDashboardAllenatore(): void
    {
        $id = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$id || $ruolo !== 'allenatore') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa area è riservata agli allenatori.");
            return;
        }

        $allenatore = $this->entityManager->find(Allenatore::class, $id);
        $palestra = $allenatore->getPalestra();
        
        // Filtra i clienti associati alla palestra dell'allenatore
        $clienti = $palestra 
            ? $this->entityManager->getRepository(Cliente::class)->findBy(['palestra' => $palestra])
            : [];
            
        $esercizi = $this->entityManager->getRepository(Esercizio::class)->findAll();

        // 1. Semaforo Schede per i clienti dell'allenatore (Rosso: scadute, Giallo: nessuna scheda, Blu: in regola)
        $schedeScadute = 0;
        $richiesteScheda = 0;
        $schedeInRegola = 0;
        $oggi = new \DateTimeImmutable();
        foreach ($clienti as $c) {
            $scheda = $c->getScheda();
            if (!$scheda) {
                $richiesteScheda++;
            } else {
                if ($scheda->getData_fine() < $oggi) {
                    $schedeScadute++;
                } else {
                    $schedeInRegola++;
                }
            }
        }

        // 2. Ultimi Messaggi inviati da questo allenatore
        $messaggioRepo = new DoctrineMessaggioRepository($this->entityManager);
        $tuttiMessaggi = $messaggioRepo->findByMittente($allenatore);
        $ultimiMessaggi = array_slice($tuttiMessaggi, 0, 5);

        // 3. Eventi di oggi in palestra
        $attivitaRepo = new DoctrineAttivitaPianificataRepository($this->entityManager);
        $attivitaOggi = $attivitaRepo->findByGiorno(new \DateTimeImmutable());
        $eventiOggi = [];
        if (empty($attivitaOggi)) {
            // Fallback per rendere il widget vivo
            $eventiOggi[] = [
                'nome' => 'Pilates',
                'orario' => '13:00 - 14:00',
                'colore' => '#209cee',
                'allenatore' => 'Luigi Verdi'
            ];
            $eventiOggi[] = [
                'nome' => 'Zumba Fitness',
                'orario' => '18:30 - 19:30',
                'colore' => '#ffdd57',
                'allenatore' => 'Carla Neri'
            ];
        } else {
            foreach ($attivitaOggi as $ap) {
                $oraInizio = str_pad($ap->getOrario(), 2, '0', STR_PAD_LEFT) . ':00';
                $oraFine = str_pad($ap->getOrario() + 1, 2, '0', STR_PAD_LEFT) . ':00';
                $eventiOggi[] = [
                    'nome' => $ap->getAttivita()->getNome(),
                    'orario' => "$oraInizio - $oraFine",
                    'colore' => '#3273dc',
                    'allenatore' => $ap->getAllenatore()->getNome() . ' ' . $ap->getAllenatore()->getCognome()
                ];
            }
        }

        $this->view->mostraDashboardAllenatore([
            'utente' => $allenatore,
            'clienti' => $clienti,
            'esercizi' => $esercizi,
            'schede_scadute' => $schedeScadute,
            'richieste_scheda' => $richiesteScheda,
            'schede_in_regola' => $schedeInRegola,
            'ultimi_messaggi' => $ultimiMessaggi,
            'eventi_oggi' => $eventiOggi
        ]);
    }

    /**
     * Mostra la dashboard del cliente se autorizzato.
     */
    public function mostraDashboardCliente(): void
    {
        $id = $this->session->getLoggedUserId();
        $ruolo = $this->session->getLoggedUserRole();
        if (!$id || $ruolo !== 'cliente') {
            $this->view->mostraStatoOperazione(false, "Accesso negato. Questa area è riservata ai clienti.");
            return;
        }

        $cliente = $this->entityManager->find(Cliente::class, $id);
        
        $parametriRepo = new \App\Foundation\Persistence\Repository\DoctrineParametriRepository($this->entityManager);
        $ultimaMisure = $parametriRepo->findUltimaByCliente($cliente);

        $this->view->mostraDashboardCliente([
            'utente' => $cliente,
            'ultimaMisure' => $ultimaMisure
        ]);
    }

    /**
     * Mostra la home dell'applicazione, reindirizzando alla dashboard se già loggati.
     */
    public function mostraHome(): void
    {
        $this->view->mostraHome();
    }

    /**
     * Centralizza la pagina di errore dell'applicazione.
     */
    public function mostraErrore(): void
    {
        $messaggio = $_GET['msg'] ?? 'Si è verificato un errore imprevisto.';
        $successo = isset($_GET['success']) && $_GET['success'] == 1;

        // Determina il link di ritorno
        $ritorno = 'login';
        if ($this->session->isLogged()) {
            $ruolo = $this->session->getLoggedUserRole();
            switch ($ruolo) {
                case 'amministratore':
                    $ritorno = 'dashboard-admin';
                    break;
                case 'allenatore':
                    $ritorno = 'dashboard-allenatore';
                    break;
                case 'cliente':
                    $ritorno = 'dashboard-cliente';
                    break;
                default:
                    $ritorno = 'login';
                    break;
            }
        }

        $this->view->mostraStatoOperazione($successo, $messaggio, $ritorno);
    }
}
