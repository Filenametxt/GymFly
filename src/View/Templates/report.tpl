<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Report & Analisi</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .report-card {
            background-color: var(--gymfly-card-bg);
            border: 2px solid var(--gymfly-accent);
            border-radius: 15px;
            padding: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 16px rgba(0,0,0,0.02) !important;
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(175, 175, 226, 0.15) !important;
            border-color: var(--gymfly-secondary);
        }
        .pie-layout {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-grow: 1;
        }
        .pie-chart-container {
            width: 50%;
            height: 180px;
        }
        .pie-details-container {
            width: 50%;
            background-color: rgba(175, 175, 226, 0.04);
            border: 2px dashed var(--gymfly-primary);
            border-radius: 12px;
            padding: 1.5rem;
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 0.95rem;
            color: var(--gymfly-text);
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        .pie-details-container:hover {
            background-color: rgba(175, 175, 226, 0.08);
            border-color: var(--gymfly-secondary);
        }
        .chart-wrapper {
            position: relative;
            flex-grow: 1;
            height: 220px;
        }
        .select select {
            border-radius: 12px !important;
            font-weight: 600;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .select select:hover {
            border-color: var(--gymfly-secondary) !important;
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- ================= DESKTOP HEADER ================= -->
            {assign var="headerClass" value="dashboard-header"}
            {if isset($smarty.session.ruolo_utente)}
                {if $smarty.session.ruolo_utente === 'amministratore'}
                    {assign var="headerClass" value="dashboard-header-admin"}
                {elseif $smarty.session.ruolo_utente === 'allenatore'}
                    {assign var="headerClass" value="dashboard-header-trainer"}
                {/if}
            {/if}
            <div class="{$headerClass} is-hidden-mobile">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-2 has-text-white mb-2">Report & Analisi</h1>
                        <p class="subtitle is-5 has-text-white-ter">Statistiche aziendali reali filtrate per mese ed anno selezionati</p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-chart-pie fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">REPORT & ANALISI</strong>
            </div>

            <!-- FILTRI TEMPORALI -->
            <div class="is-flex is-justify-content-end mb-5">
                <form action="report" method="GET" class="is-flex" style="gap: 0.5rem;">
                    <div class="select">
                        <select name="mese" onchange="this.form.submit()">
                            {foreach from=$mesiNomi key=num item=nome}
                                <option value="{$num}" {if $meseSelezionato === $num}selected{/if}>{$nome}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="select">
                        <select name="anno" onchange="this.form.submit()">      <!--serve a creare un menu a tendina che invia automaticamente i dati non appena l'utente sceglie un'opzione-->
                            {foreach from=$anniDisponibili item=a}
                                <option value="{$a}" {if $annoSelezionato === $a}selected{/if}>{$a}</option>
                            {/foreach}
                        </select>
                    </div>
                </form>
            </div>

            <!-- LAYOUT GRAFICI -->
            <div class="columns is-multiline">
                
                <!-- 1. TIPOLOGIA ABBONAMENTO (PIE CHART CON DETTAGLI AL CLICK) -->
                <div class="column is-6">
                    <div class="report-card">
                        <h3 class="title is-5 mb-4 style-theme-text">Tipologia Abbonamento ({$mesiNomi[$meseSelezionato]} {$annoSelezionato})</h3>
                        {if count($abbonamentiDati) > 0}
                            <div class="pie-layout">
                                <div class="pie-chart-container">
                                    <canvas id="chartTipologie"></canvas>
                                </div>
                                <div class="pie-details-container" id="pieDetails">
                                    <span class="icon is-large has-text-grey mb-1"><i class="fas fa-mouse-pointer fa-lg"></i></span>
                                    <p class="has-text-grey-dark">Clicca sulle sezioni della torta per i dettagli</p>
                                </div>
                            </div>
                        {else}
                            <div class="has-text-centered py-5 my-auto">
                                <span class="icon is-large has-text-grey mb-2"><i class="fas fa-info-circle fa-2x"></i></span>
                                <p class="has-text-grey">Nessun abbonamento attivo nel mese selezionato.</p>
                            </div>
                        {/if}
                    </div>
                </div>

                <!-- 2. PRENOTAZIONI ALLE ATTIVITÀ (HORIZONTAL BAR CHART) -->
                <div class="column is-6">
                    <div class="report-card">
                        <h3 class="title is-5 mb-4 style-theme-text">Prenotazione Attività ({$mesiNomi[$meseSelezionato]} {$annoSelezionato})</h3>
                        {if count($prenotazioniCorsi) > 0}
                            <div class="chart-wrapper">
                                <canvas id="chartCorsi"></canvas>
                            </div>
                        {else}
                            <div class="has-text-centered py-5 my-auto">
                                <span class="icon is-large has-text-grey mb-2"><i class="fas fa-info-circle fa-2x"></i></span>
                                <p class="has-text-grey">Nessuna prenotazione trovata nel mese selezionato.</p>
                            </div>
                        {/if}
                    </div>
                </div>

                <!-- 3. ISCRITTI GIORNALIERI (VERTICAL BAR CHART - LARGO) -->
                <div class="column is-12">
                    <div class="report-card">
                        <h3 class="title is-5 mb-4 style-theme-text">Nuovi Iscritti Giornalieri ({$mesiNomi[$meseSelezionato]} {$annoSelezionato})</h3>
                        <div class="chart-wrapper" style="height: 250px;">
                            <canvas id="chartIscritti"></canvas>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>    <!-- INIZIALIZZAZIONE GRAFICI CHART.JS -->
    <script>
        {literal}
        document.addEventListener("DOMContentLoaded", function() {       //verifica che la pagina sia completamente caricata
            
            // Impostazioni globali di stile per Chart.js in linea con GymFly
            Chart.defaults.font.family = "'Outfit', 'Inter', 'BlinkMacSystemFont', -apple-system, 'Segoe UI', 'Roboto', sans-serif";
            Chart.defaults.color = "#4B3F72";
        {/literal}

            {if count($abbonamentiDati) > 0}
            // 1. Grafico Tipologie Abbonamento (Pie Chart)
            const ctxTipologie = document.getElementById('chartTipologie').getContext('2d');     //recupera tutte le chart tipologie e i posta il 2d
            
            {assign var="abbLabels" value=[]}
            {assign var="abbValues" value=[]}
            {foreach from=$abbonamentiDati key=tipo item=count}
                {$abbLabels[] = $tipo}              //array tutte chiavi
                {$abbValues[] = $count}             //array tutti valori
            {/foreach}
            const abbonamentiDati = {ldelim}         //Non toccare questa riga, stampami semplicemente una normale parentesi graffa perché serve a JavaScript
                labels: {$abbLabels|json_encode},
                datasets: [{ldelim}
                    data: {$abbValues|json_encode},
                    backgroundColor: [
                        '#afafe2', // periwinkle (primario)
                        '#99cdea', // baby-blue (secondario)
                        '#c5e0fc', // pale-sky (accento)
                        '#d0d0f5', // periwinkle-2
                        '#85a6cc', // darker baby blue
                        '#6c6ca6'  // darker periwinkle
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                {rdelim}]
            {rdelim};

            {literal}
            const chartTipologie = new Chart(ctxTipologie, {
                type: 'pie',
                data: abbonamentiDati,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    onClick: (event, activeElements) => {
                        if (activeElements.length > 0) {
                            const index = activeElements[0].index;
                            const label = chartTipologie.data.labels[index];
                            const value = chartTipologie.data.datasets[0].data[index];
                            
                            const detailsDiv = document.getElementById("pieDetails");
                            detailsDiv.innerHTML = 
                                '<h4 class="title is-6 mb-2 style-theme-text">' + label + '</h4>' +
                                '<p class="is-size-5 mb-0">Iscritti Attivi: <strong>' + value + '</strong></p>';
                        }
                    }
                }
            });
            {/literal}
            {/if}

            {if count($prenotazioniCorsi) > 0}
            // 2. Prenotazioni Corsi (Horizontal Bar Chart)
            const ctxCorsi = document.getElementById('chartCorsi').getContext('2d');
            {assign var="corsiLabels" value=[]}
            {assign var="corsiValues" value=[]}
            {foreach from=$prenotazioniCorsi key=corso item=prenotati}
                {$corsiLabels[] = $corso}
                {$corsiValues[] = $prenotati}
            {/foreach}
            const labelsCorsi = {$corsiLabels|json_encode};
            const datiCorsi = {$corsiValues|json_encode};

            {literal}
            new Chart(ctxCorsi, {
                type: 'bar',
                data: {
                    labels: labelsCorsi,
                    datasets: [{
                        data: datiCorsi,
                        backgroundColor: '#afafe2', // periwinkle (primario)
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { 
                            grid: { color: 'rgba(175, 175, 226, 0.15)' },
                            ticks: { 
                                precision: 0,
                                color: '#4B3F72'
                            }
                        },
                        y: { 
                            grid: { display: false },
                            ticks: { color: '#4B3F72' }
                        }
                    }
                }
            });
            {/literal}
            {/if}

            // 3. Registrazioni Giornaliere (Vertical Bar Chart)
            const ctxIscritti = document.getElementById('chartIscritti').getContext('2d');
            {assign var="iscrittiValues" value=[]}
            {foreach from=$iscrittiGiornalieri item=val}
                {$iscrittiValues[] = $val}
            {/foreach}
            const datiIscritti = {$iscrittiValues|json_encode};
            
            {literal}
            new Chart(ctxIscritti, {
                type: 'bar',
                data: {
                    labels: [
            {/literal}
                        {foreach from=$giorniMese item=g} '{$g}', {/foreach}
            {literal}
                    ],
                    datasets: [{
                        label: 'Iscritti',
                        data: datiIscritti,
                        backgroundColor: '#99cdea', // baby blue (secondario)
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            grid: { color: 'rgba(175, 175, 226, 0.15)' },
                            ticks: { 
                                precision: 0,
                                color: '#4B3F72'
                            }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: '#4B3F72' }
                        }
                    }
                }
            });
        });
        {/literal}
    </script>

</body>
</html>
