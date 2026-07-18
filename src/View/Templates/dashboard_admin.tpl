<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Dashboard Amministratore</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- HEADER -->
            <div class="dashboard-header-admin">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-2 has-text-white mb-2">Benvenuto, {$utente->getNome()} {$utente->getCognome()}!</h1>
                        <p class="subtitle is-5 has-text-white-ter">Dashboard di Supervisione e Amministrazione di <strong class="has-text-white">GymFly</strong></p>
                    </div>
                </div>
            </div>

            <!-- STATS -->
            <div class="columns mb-6">
                <div class="column">
                    <div class="stat-card">
                        <span class="icon is-large mb-2">
                            <i class="fas fa-users fa-2x" style="color: #AFAFE2 !important;"></i>
                        </span>
                        <h3 class="title is-4 mb-1">{$clienti|@count}</h3>
                        <p class="heading has-text-grey">Clienti Totali</p>
                    </div>
                </div>
                <div class="column">
                    <div class="stat-card">
                        <span class="icon is-large mb-2">
                            <i class="fas fa-user-tie fa-2x" style="color: #99CDEA !important;"></i>
                        </span>
                        <h3 class="title is-4 mb-1">{$allenatori|@count}</h3>
                        <p class="heading has-text-grey">Allenatori Attivi</p>
                    </div>
                </div>
                <div class="column">
                    <div class="stat-card">
                        <span class="icon is-large mb-2">
                            <i class="fas fa-dumbbell fa-2x" style="color: #AFAFE2 !important;"></i>
                        </span>
                        <h3 class="title is-4 mb-1">Attiva</h3>
                        <p class="heading has-text-grey">Stato Palestra</p>
                    </div>
                </div>
            </div>

            <!-- RIGA SUPERIORE (SEMAFORO & AZIONI RAPIDE A SINISTRA, ISTOGRAMMA TALL A DESTRA) -->
            <div class="columns mb-6">
                <!-- Colonna Sinistra: Semaforo, Azioni Rapide e Messaggi Preimpostati -->
                <div class="column is-6">
                    <!-- Box 1: Semaforo Certificati Medici (Originale) -->
                    <div class="box mb-5">
                        <h2 class="title is-5 mb-4 style-theme-text">
                            <i class="fas fa-heartbeat mr-2" style="color: #ff3860;"></i> Controllo Certificati Medici
                        </h2>
                        <div class="columns is-mobile is-vcentered" style="height: calc(100% - 2.5rem); margin-top: 0;">
                            <div class="column has-text-centered">
                                <a href="clienti?filtro_certificato=scaduti" style="display: block; text-decoration: none;">
                                    <div class="notification is-danger is-light p-3" style="border-radius: 12px; border: 1px solid #ff3860; cursor: pointer;">
                                        <span class="icon is-large"><i class="fas fa-times-circle fa-2x"></i></span>
                                        <h4 class="title is-4 mt-2 mb-0">{$certificati_scaduti}</h4>
                                        <p class="is-size-7 font-weight-bold">SCADUTI / ASSENTI</p>
                                    </div>
                                </a>
                            </div>
                            <div class="column has-text-centered">
                                <a href="clienti?filtro_certificato=in_scadenza" style="display: block; text-decoration: none;">
                                    <div class="notification is-warning is-light p-3" style="border-radius: 12px; border: 1px solid #ffdd57; cursor: pointer;">
                                        <span class="icon is-large"><i class="fas fa-exclamation-triangle fa-2x"></i></span>
                                        <h4 class="title is-4 mt-2 mb-0">{$certificati_in_scadenza}</h4>
                                        <p class="is-size-7 font-weight-bold">IN SCADENZA</p>
                                    </div>
                                </a>
                            </div>
                            <div class="column has-text-centered">
                                <a href="clienti?filtro_certificato=in_regola" style="display: block; text-decoration: none;">
                                    <div class="notification is-info is-light p-3" style="border-radius: 12px; border: 1px solid #209cee; cursor: pointer;">
                                        <span class="icon is-large"><i class="fas fa-check-circle fa-2x"></i></span>
                                        <h4 class="title is-4 mt-2 mb-0">{$certificati_validi}</h4>
                                        <p class="is-size-7 font-weight-bold">IN REGOLA</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Azioni Rapide (Originale) -->
                    <div class="box mb-5">
                        <h2 class="title is-5 mb-4 style-theme-text">
                            <i class="fas fa-bolt mr-2" style="color: #ffdd57;"></i> Azioni Rapide
                        </h2>
                        <div class="columns is-mobile" style="margin-top: -0.5rem;">
                            <!-- Nuovo Utente -->
                            <div class="column is-4 has-text-centered py-2">
                                <a href="crea-cliente" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent);">
                                    <span class="icon is-medium mb-1"><i class="fas fa-user-plus fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); white-space: normal; line-height: 1.1;">Nuovo Utente</span>
                                </a>
                            </div>
                            <!-- Aggiungi Attività -->
                            <div class="column is-4 has-text-centered py-2">
                                <a href="crea-attivita" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent);">
                                    <span class="icon is-medium mb-1"><i class="fas fa-calendar-plus fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); white-space: normal; line-height: 1.1;">Aggiungi Attività</span>
                                </a>
                            </div>
                            <!-- Cerca Utente -->
                            <div class="column is-4 has-text-centered py-2">
                                <a href="clienti" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent);">
                                    <span class="icon is-medium mb-1"><i class="fas fa-search fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); white-space: normal; line-height: 1.1;">Cerca Utente</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Box 3: Messaggi Preimpostati (Originale) -->
                    <div class="box">
                        <h2 class="title is-5 mb-4 style-theme-text">
                            <i class="fas fa-comment-alt mr-2" style="color: #3273dc;"></i> Messaggi Preimpostati
                        </h2>
                        <div class="columns is-mobile" style="margin-top: -0.5rem;">
                            <!-- Chiusura Palestra -->
                            <form action="invia-messaggio" method="POST" class="column is-4 has-text-centered py-2" style="margin: 0; padding: 0.75rem;">
                                <input type="hidden" name="oggetto" value="Avviso Chiusura Palestra">
                                <input type="hidden" name="contenuto" value="Gentili clienti, vi comunichiamo che la palestra rimarrà chiusa temporaneamente per motivi tecnici straordinari. Ci scusiamo per il disagio.">
                                <input type="hidden" name="destinatari_tipo" value="gruppo">
                                <input type="hidden" name="gruppo_tipo" value="tutti_clienti">
                                <button type="submit" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent); cursor: pointer; white-space: normal; padding: 0;">
                                    <span class="icon is-medium mb-1"><i class="fas fa-door-closed fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); line-height: 1.1;">Chiusura Palestra</span>
                                </button>
                            </form>
                            
                            <!-- Guasto Attrezzi -->
                            <form action="invia-messaggio" method="POST" class="column is-4 has-text-centered py-2" style="margin: 0; padding: 0.75rem;">
                                <input type="hidden" name="oggetto" value="Avviso Guasto Tecnico">
                                <input type="hidden" name="contenuto" value="Gentili clienti, vi avvisiamo che a causa di un guasto tecnico temporaneo ad alcune attrezzature, l'accesso a determinate sale potrebbe essere limitato. Lavoriamo al ripristino.">
                                <input type="hidden" name="destinatari_tipo" value="gruppo">
                                <input type="hidden" name="gruppo_tipo" value="tutti_clienti">
                                <button type="submit" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent); cursor: pointer; white-space: normal; padding: 0;">
                                    <span class="icon is-medium mb-1"><i class="fas fa-tools fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); line-height: 1.1;">Guasto Attrezzi</span>
                                </button>
                            </form>
                            
                            <!-- Manutenzione -->
                            <form action="invia-messaggio" method="POST" class="column is-4 has-text-centered py-2" style="margin: 0; padding: 0.75rem;">
                                <input type="hidden" name="oggetto" value="Avviso Manutenzione Programmata">
                                <input type="hidden" name="contenuto" value="Gentili clienti, vi avvisiamo che verranno effettuati lavori di manutenzione ordinaria. La palestra rimarrà regolarmente aperta ma alcune aree potrebbero subire variazioni.">
                                <input type="hidden" name="destinatari_tipo" value="gruppo">
                                <input type="hidden" name="gruppo_tipo" value="tutti_clienti">
                                <button type="submit" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent); cursor: pointer; white-space: normal; padding: 0;">
                                    <span class="icon is-medium mb-1"><i class="fas fa-wrench fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); line-height: 1.1;">Manutenzione</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Colonna Destra: Statistiche Registrazioni (Grafico Istogramma Allungato) -->
                <div class="column is-6">
                    <div class="box" style="height: 100%; display: flex; flex-direction: column;">
                        <h2 class="title is-5 mb-4 style-theme-text">
                            <i class="fas fa-chart-bar mr-2" style="color: #3273dc;"></i> Statistiche Registrazioni Mensili
                        </h2>
                        <div class="chart-container" style="background-color: var(--gymfly-bg); border-radius: 16px; padding: 1.25rem; border: 1px solid var(--gymfly-primary); flex-grow: 1; position: relative; height: 300px; width: 100%;">
                            <canvas id="chartRegistrazioni"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGA WIDGETS INFERIORI (ULTIMI MESSAGGI & OGGI IN PALESTRA) -->
            <div class="columns mb-6">
                <!-- Ultimi Messaggi -->
                <div class="column is-6">
                    <div class="box" style="height: 100%;">
                        <h2 class="title is-5 mb-4 style-theme-text">
                            <i class="fas fa-paper-plane mr-2" style="color: var(--gymfly-secondary);"></i> Ultimi Messaggi Inviati
                        </h2>
                        <div class="message-list">
                            {foreach $ultimi_messaggi as $msg}
                                <div class="p-3 mb-3" style="background-color: var(--gymfly-bg); border-radius: 12px; border-left: 4px solid var(--gymfly-primary);">
                                    <div class="is-flex is-justify-content-between">
                                        <strong class="is-size-6" style="color: var(--gymfly-text);">{$msg->getOggetto()}</strong>
                                        <span class="tag is-small is-light" style="color: var(--gymfly-text);">A: {$msg->getDestinatari()|@count} utenti</span>
                                    </div>
                                    <p class="is-size-7 mt-1 has-text-grey-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: var(--gymfly-text);">{$msg->getContenuto()}</p>
                                </div>
                            {foreachelse}
                                <div class="has-text-centered has-text-grey py-5">
                                    <span class="icon is-large"><i class="fas fa-comment-slash fa-2x"></i></span>
                                    <p class="is-size-6 mt-2">Nessun messaggio inviato di recente.</p>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>

                <!-- Oggi in Palestra -->
                <div class="column is-6">
                    <div class="box" style="height: 100%;">
                        <div class="is-flex is-justify-content-between is-align-items-center mb-4">
                            <h2 class="title is-5 mb-0 style-theme-text" style="text-transform: capitalize;">
                                <i class="fas fa-calendar-day mr-2" style="color: #3273dc;"></i> Oggi in Palestra
                            </h2>
                            <span class="tag is-link is-light font-weight-bold">({$eventi_oggi|@count} Eventi)</span>
                        </div>
                        

                        <!-- Elenco Eventi -->
                        <div class="event-list">
                            {foreach $eventi_oggi as $evento}
                                <div class="p-3 mb-3" style="background-color: var(--gymfly-bg); border-radius: 12px; border-left: 5px solid {$evento.colore};">
                                    <div class="is-flex is-align-items-center is-justify-content-between">
                                        <div class="is-flex is-align-items-center">
                                            <span style="height: 10px; width: 10px; background-color: {$evento.colore}; border-radius: 50%; display: inline-block; margin-right: 10px;"></span>
                                            <strong class="is-size-6" style="color: var(--gymfly-text);">{$evento.nome}</strong>
                                        </div>
                                        <span class="is-size-7 has-text-grey" style="font-weight: 600;">{$evento.orario}</span>
                                    </div>
                                    <div class="mt-2 is-flex is-align-items-center is-size-7 has-text-grey-dark" style="margin-left: 20px;">
                                        <span class="icon is-small mr-1"><i class="fas fa-user-ninja" style="color: var(--gymfly-text);"></i></span>
                                        <span>Trainer: <strong>{$evento.allenatore}</strong></span>
                                    </div>
                                </div>
                            {foreachelse}
                                <div class="has-text-centered has-text-grey py-5">
                                    <span class="icon is-large"><i class="fas fa-calendar-times fa-2x"></i></span>
                                    <p class="is-size-6 mt-2">Nessuna attività programmata per oggi.</p>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('chartRegistrazioni');
        if (!ctx) return;

        // Impostazioni globali di stile per Chart.js in linea con GymFly
        Chart.defaults.font.family = "'Outfit', 'Inter', 'BlinkMacSystemFont', -apple-system, 'Segoe UI', 'Roboto', sans-serif";
        Chart.defaults.color = "#4B3F72";

        const rawData = {$registrazioni|json_encode};
        const labels = rawData.map(pt => pt.data);
        const dati = rawData.map(pt => pt.valore);

        {literal}
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nuovi Iscritti',
                    data: dati,
                    backgroundColor: 'rgba(175, 175, 226, 0.75)', // color: #afafe2
                    borderColor: '#4B3F72',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(175, 175, 226, 0.15)' },
                        ticks: {
                            color: '#4B3F72',
                            precision: 0
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#4B3F72' }
                    }
                }
            }
        });
        {/literal}
    });
    </script>
</body>
</html>
