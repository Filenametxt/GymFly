<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Progressi Cliente</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .progressi-container {
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
        }

        /* WORKOUT ACCORDION (Style matched to style.css) */
        .workout-accordion {
            border: 2px solid var(--gymfly-primary);
            border-radius: 15px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            background: var(--gymfly-card-bg);
            transition: all 0.2s ease;
            box-shadow: 0 8px 16px rgba(0,0,0,0.03) !important;
        }

        .workout-accordion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            cursor: pointer;
            user-select: none;
            background: var(--gymfly-card-bg);
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .workout-accordion.active .workout-accordion-header {
            border-bottom-color: var(--gymfly-primary);
            background: rgba(197, 224, 252, 0.05);
        }

        .workout-accordion-header:hover {
            background: rgba(197, 224, 252, 0.1);
        }

        .workout-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--gymfly-text) !important;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .workout-toggle-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: var(--gymfly-accent);
            border-radius: 50%;
            color: var(--gymfly-text);
            transition: transform 0.3s ease;
        }

        .workout-accordion.active .workout-toggle-icon {
            transform: rotate(180deg);
        }

        .workout-accordion-content {
            display: none;
            padding: 1.5rem;
            background-color: var(--gymfly-card-bg);
            animation: slideDown 0.3s ease;
        }

        .workout-accordion.active .workout-accordion-content {
            display: block;
        }

        /* EXERCISE CARD (Nested Box Style) */
        .exercise-progress-card {
            background: var(--gymfly-bg) !important;
            border: 2px solid var(--gymfly-accent) !important;
            border-radius: 12px !important;
            padding: 1.5rem !important;
            margin-bottom: 1.5rem !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.01) !important;
            transition: all 0.3s ease;
        }

        .exercise-progress-card:hover {
            border-color: var(--gymfly-primary) !important;
            box-shadow: 0 6px 18px rgba(175, 175, 226, 0.08) !important;
        }

        .exercise-header-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gymfly-text) !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--gymfly-accent);
            padding-bottom: 0.5rem;
        }

        .target-badge {
            font-size: 0.75rem;
            background: var(--gymfly-card-bg) !important;
            border: 1px solid var(--gymfly-primary) !important;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            font-weight: 600;
            color: var(--gymfly-text) !important;
        }

        /* CHARTS AND TABS */
        .chart-wrapper {
            background: var(--gymfly-card-bg) !important;
            border-radius: 10px;
            border: 2px solid var(--gymfly-accent) !important;
            padding: 1.25rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .chart-legend {
            display: flex;
            justify-content: center;
            gap: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gymfly-text);
            margin-top: 0.5rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* TABS (GymFly Theme Aligned) */
        .progress-tabs {
            margin-bottom: 0.75rem !important;
        }

        .progress-tabs ul {
            border-bottom-color: var(--gymfly-accent) !important;
        }

        .progress-tabs li a {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.4rem 0.8rem;
            border-color: var(--gymfly-accent) !important;
            color: var(--gymfly-text) !important;
            background-color: var(--gymfly-card-bg) !important;
        }

        .progress-tabs li.is-active a {
            background-color: var(--gymfly-accent) !important;
            border-color: var(--gymfly-primary) !important;
            color: #1e3a8a !important;
            font-weight: bold !important;
        }

        /* TIMELINE/TABLE */
        .history-table {
            font-size: 0.8rem;
            background: transparent !important;
            width: 100%;
        }

        .history-table th {
            color: var(--gymfly-text) !important;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--gymfly-accent) !important;
        }

        .history-table td {
            border-bottom: 1px solid var(--gymfly-accent) !important;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            <div class="container progressi-container">
                
                <!-- BACK LINK -->
                <div class="mb-4">
                    <a href="visualizza-profilo?id={$cliente->getId()}" class="button is-ghost has-text-grey pl-0">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span class="style-theme-text font-weight-bold">Torna al Profilo Cliente</span>
                    </a>
                </div>

                <!-- ================= DESKTOP HEADER (Aligned with style.css dashboard-header-trainer) ================= -->
                <div class="dashboard-header-trainer is-hidden-mobile">
                    <div class="columns is-vcentered">
                        <div class="column">
                            <strong class="is-size-6 has-text-white-ter" style="letter-spacing: 1px; text-transform: uppercase;">MONITORAGGIO SCHEDA</strong>
                            <h1 class="title is-2 has-text-white mt-1 mb-2">{$cliente->getNome()} {$cliente->getCognome()}</h1>
                            <p class="subtitle is-5 has-text-white-ter mb-0">
                                <strong>Scheda:</strong> {$scheda->getNome_scheda()} ({$scheda->getObiettivo()})
                            </p>
                        </div>
                        <div class="column is-narrow">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-chart-line fa-4x"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ================= MOBILE HEADER ================= -->
                <div class="box p-4 mb-4 is-hidden-tablet" style="border: 2px solid var(--gymfly-primary) !important;">
                    <div class="mb-2">
                        <span class="tag is-trainer-theme">MONITORAGGIO ATLETA</span>
                    </div>
                    <h1 class="title is-4 style-theme-text mb-1">{$cliente->getNome()} {$cliente->getCognome()}</h1>
                    <p class="is-size-7 style-theme-text"><strong>Scheda:</strong> {$scheda->getNome_scheda()}</p>
                    <p class="is-size-7 style-theme-text"><strong>Obiettivo:</strong> {$scheda->getObiettivo()}</p>
                    <p class="is-size-7 style-theme-text"><strong>Validità:</strong> {$scheda->getData_inizio()->format('d/m/Y')} — {$scheda->getData_fine()->format('d/m/Y')}</p>
                </div>

                <!-- INFO SCHEDA SUMMARY ON DESKTOP -->
                <div class="box p-4 mb-5 is-hidden-mobile">
                    <div class="columns is-size-7 style-theme-text">
                        <div class="column">
                            <p><i class="fas fa-id-card mr-1"></i> <strong>Codice Fiscale:</strong> {$cliente->getCF()}</p>
                        </div>
                        <div class="column">
                            <p><i class="fas fa-calendar-alt mr-1"></i> <strong>Periodo Validità:</strong> {$scheda->getData_inizio()->format('d/m/Y')} — {$scheda->getData_fine()->format('d/m/Y')}</p>
                        </div>
                    </div>
                </div>

                <!-- WORKOUT LIST -->
                <h2 class="title is-4 style-theme-text mb-4">
                    <i class="fas fa-dumbbell mr-2" style="color: var(--gymfly-primary);"></i> Progressi per Allenamento
                </h2>

                {if $workouts|@count > 0}
                    {foreach $workouts as $wIdx => $wData}
                        <div class="workout-accordion {if $wIdx === 0}active{/if}">
                            <!-- Accordion Header -->
                            <div class="workout-accordion-header" onclick="toggleAccordion(this)">
                                <h3 class="workout-title">
                                    <i class="fas fa-running mr-2" style="color: var(--gymfly-primary);"></i>
                                    {$wData.allenamento->getNome()}
                                </h3>
                                <div class="workout-toggle-icon">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>

                            <!-- Accordion Content -->
                            <div class="workout-accordion-content">
                                {if $wData.allenamento->getDescrizione()|pulisci_descrizione}
                                    <p class="is-size-7 style-theme-text mb-4" style="font-style: italic;">
                                        <i class="fas fa-info-circle mr-1" style="color: var(--gymfly-primary);"></i> {$wData.allenamento->getDescrizione()|pulisci_descrizione}
                                    </p>
                                {/if}

                                {if $wData.esercizi|@count > 0}
                                    {foreach $wData.esercizi as $eData}
                                        <div class="exercise-progress-card">
                                            <!-- Title & Target parameters -->
                                            <div class="exercise-header-title">
                                                <span class="style-theme-text">{$eData.esercizio->getNomeEsercizio()}</span>
                                                <div class="is-flex" style="gap: 5px; flex-wrap: wrap;">
                                                    <span class="target-badge">
                                                        Serie: {$eData.dettaglio->getSerie()}
                                                    </span>
                                                    {if $eData.dettaglio->getRipetizioni()}
                                                        <span class="target-badge">
                                                            Rip Target: {$eData.dettaglio->getRipetizioni()}
                                                        </span>
                                                    {/if}
                                                    {if $eData.dettaglio->getCarico() > 0}
                                                        <span class="target-badge">
                                                            Carico Target: {$eData.dettaglio->getCarico()} Kg
                                                        </span>
                                                    {/if}
                                                    {if $eData.dettaglio->getTempo()}
                                                        <span class="target-badge">
                                                            Tempo Target: {$eData.dettaglio->getTempo()}
                                                        </span>
                                                    {/if}
                                                </div>
                                            </div>

                                            <!-- Progress Views (Tabs & Charts) -->
                                            {if $eData.hasCarico || $eData.hasReps || $eData.hasDurata}
                                                <div class="tabs is-toggle is-fullwidth progress-tabs">
                                                    <ul>
                                                        {if $eData.hasCarico}
                                                            <li class="is-active" onclick="switchExerciseTab(this, 'carico', '{$eData.esercizio->getId()}')">
                                                                <a><i class="fas fa-weight mr-2"></i>Carico</a>
                                                            </li>
                                                        {/if}
                                                        {if $eData.hasReps}
                                                            <li class="{if !$eData.hasCarico}is-active{/if}" onclick="switchExerciseTab(this, 'reps', '{$eData.esercizio->getId()}')">
                                                                <a><i class="fas fa-redo mr-2"></i>Ripetizioni</a>
                                                            </li>
                                                        {/if}
                                                        {if $eData.hasDurata}
                                                            <li class="{if !$eData.hasCarico && !$eData.hasReps}is-active{/if}" onclick="switchExerciseTab(this, 'durata', '{$eData.esercizio->getId()}')">
                                                                <a><i class="fas fa-stopwatch mr-2"></i>Tempo / Durata</a>
                                                            </li>
                                                        {/if}
                                                    </ul>
                                                </div>

                                                <!-- Tab Contents -->
                                                <div class="tab-contents-container">
                                                    <!-- CARICO CHART -->
                                                    {if $eData.hasCarico}
                                                        <div class="tab-content-{$eData.esercizio->getId()} tab-content-carico">
                                                            <div class="chart-wrapper">
                                                                <div style="position: relative; height: 160px; width: 100%;">
                                                                    <canvas class="exercise-chart" 
                                                                            data-type="carico" 
                                                                            data-exercise="{$eData.esercizio->getId()}"
                                                                            data-label="Carico di Lavoro (Kg)"
                                                                            data-color="#4B3F72"
                                                                            data-bg="rgba(75, 63, 114, 0.05)"
                                                                            data-points='{$eData.carico|json_encode}'></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}

                                                    <!-- REPETITIONS CHART -->
                                                    {if $eData.hasReps}
                                                        <div class="tab-content-{$eData.esercizio->getId()} tab-content-reps {if $eData.hasCarico}is-hidden{/if}">
                                                            <div class="chart-wrapper">
                                                                <div style="position: relative; height: 160px; width: 100%;">
                                                                    <canvas class="exercise-chart" 
                                                                            data-type="reps" 
                                                                            data-exercise="{$eData.esercizio->getId()}"
                                                                            data-label="Ripetizioni"
                                                                            data-color="#afafe2"
                                                                            data-bg="rgba(175, 175, 226, 0.05)"
                                                                            data-points='{$eData.reps|json_encode}'></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}

                                                    <!-- DURATA CHART -->
                                                    {if $eData.hasDurata}
                                                        <div class="tab-content-{$eData.esercizio->getId()} tab-content-durata {if $eData.hasCarico || $eData.hasReps}is-hidden{/if}">
                                                            <div class="chart-wrapper">
                                                                <div style="position: relative; height: 160px; width: 100%;">
                                                                    <canvas class="exercise-chart" 
                                                                            data-type="durata" 
                                                                            data-exercise="{$eData.esercizio->getId()}"
                                                                            data-label="Durata Sessione (sec)"
                                                                            data-color="#3273dc"
                                                                            data-bg="rgba(50, 115, 220, 0.05)"
                                                                            data-points='{$eData.durata|json_encode}'></canvas>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                </div>

                                                <!-- History Table -->
                                                <div class="mt-4">
                                                    <h4 class="title is-6 mb-2 style-theme-text">Cronologia Registrazioni</h4>
                                                    <table class="table is-striped is-hoverable is-fullwidth history-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Data</th>
                                                                <th>Tipo Progresso</th>
                                                                <th>Valore Registrato</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {foreach $eData.storico as $sLog}
                                                                <tr>
                                                                    <td>{$sLog.data}</td>
                                                                    <td><span class="tag is-light style-theme-text font-weight-bold" style="background-color: var(--gymfly-accent) !important;">{$sLog.tipo}</span></td>
                                                                    <td class="font-weight-bold style-theme-text">{$sLog.valore}</td>
                                                                </tr>
                                                            {/foreach}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            {else}
                                                <div class="notification is-light py-5 has-text-centered" style="background-color: var(--gymfly-card-bg) !important; border: 1px solid var(--gymfly-accent);">
                                                    <span class="icon is-large has-text-grey-light"><i class="fas fa-chart-bar fa-2x" style="color: var(--gymfly-primary) !important;"></i></span>
                                                    <p class="is-size-7 mt-2 style-theme-text">
                                                        Nessun progresso registrato per questo esercizio.<br>
                                                        I dati storici appariranno quando il cliente aggiornerà la scheda.
                                                    </p>
                                                </div>
                                            {/if}
                                        </div>
                                    {/foreach}
                                {else}
                                    <p class="style-theme-text has-text-centered py-4">Nessun esercizio presente in questo allenamento.</p>
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                {else}
                    <div class="box has-text-centered py-6">
                        <span class="icon is-large mb-3 has-text-grey"><i class="fas fa-file-invoice fa-3x" style="color: var(--gymfly-primary);"></i></span>
                        <p class="style-theme-text is-size-5">La scheda non contiene allenamenti.</p>
                    </div>
                {/if}
            </div>
        </main>
    </div>

    <!-- SCRIPT PER ACCORDION, TABS E GRAFICI CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleAccordion(headerElement) {
            const accordionItem = headerElement.parentElement;
            accordionItem.classList.toggle('active');
        }

        function switchExerciseTab(clickedTab, tabType, exerciseId) {
            // Rimuovi classe active da tutti i tab dello stesso esercizio
            const tabList = clickedTab.parentElement.children;
            for (let tab of tabList) {
                tab.classList.remove('is-active');
            }
            clickedTab.classList.add('is-active');

            // Nascondi tutti i contenuti dei tab dello stesso esercizio
            const contents = document.querySelectorAll('.tab-content-' + exerciseId);
            contents.forEach(content => {
                content.classList.add('is-hidden');
            });

            // Mostra il contenuto selezionato
            const activeContent = document.querySelector('.tab-content-' + exerciseId + '.tab-content-' + tabType);
            if (activeContent) {
                activeContent.classList.remove('is-hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Impostazioni globali di stile per Chart.js in linea con GymFly
            Chart.defaults.font.family = "'Outfit', 'Inter', 'BlinkMacSystemFont', -apple-system, 'Segoe UI', 'Roboto', sans-serif";
            Chart.defaults.color = "#4B3F72";

            const canvases = document.querySelectorAll('.exercise-chart');
            canvases.forEach(canvas => {
                const rawPoints = JSON.parse(canvas.getAttribute('data-points') || '[]');
                if (rawPoints.length === 0) return;

                const labels = rawPoints.map(pt => pt.data);
                const values = rawPoints.map(pt => pt.valore);
                const label = canvas.getAttribute('data-label');
                const color = canvas.getAttribute('data-color') || '#4B3F72';
                const bg = canvas.getAttribute('data-bg') || 'rgba(75, 63, 114, 0.05)';

                {literal}
                new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: values,
                            borderColor: color,
                            backgroundColor: bg,
                            borderWidth: 3,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: color,
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
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
        });
    </script>
</body>
</html>
