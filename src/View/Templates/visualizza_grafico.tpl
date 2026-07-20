<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - {$titolo}</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .custom-mobile-container {
            max-width: 550px;
            margin: 0 auto;
            width: 100%;
        }
        .chart-container {
            background-color: var(--gymfly-bg);
            border-radius: 15px;
            padding: 1.5rem 1rem;
            border: 2px solid var(--gymfly-accent);
            overflow: hidden;
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            <div class="container custom-mobile-container">
                
                <div class="mb-4">
                    <a href="aggiorna-misure{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" class="button is-ghost has-text-grey pl-0">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Torna ai Parametri</span>
                    </a>
                </div>

                <div class="box p-5" style="border: 2px solid var(--gymfly-accent); border-radius: 15px; background-color: var(--gymfly-card-bg); box-shadow: 0 8px 16px rgba(0,0,0,0.02) !important;">
                    <h1 class="title is-4 style-theme-text has-text-centered mb-5">{$titolo}</h1>

                    {if count($valori) > 0}
                        <div class="chart-container" style="position: relative; height: 260px; width: 100%;">
                            <canvas id="chartMisure"></canvas>
                        </div>
                    {else}
                        <div class="notification is-warning is-light has-text-centered py-5">
                            <span class="icon is-large"><i class="fas fa-chart-line fa-2x"></i></span>
                            <p class="is-size-6 mt-2">Nessun dato registrato nello storico per tracciare il grafico.</p>
                        </div>
                    {/if}
                </div>

            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('chartMisure');
        if (!ctx) return;

        const labels = {$labels|json_encode};
        const dati = {$valori|json_encode};
        const chartTitle = '{$titolo}';

        {literal}
        // Impostazioni globali di stile per Chart.js in linea con GymFly
        Chart.defaults.font.family = "'Outfit', 'Inter', 'BlinkMacSystemFont', -apple-system, 'Segoe UI', 'Roboto', sans-serif";
        Chart.defaults.color = "#4B3F72";

        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: chartTitle,
                    data: dati,
                    borderColor: '#99cdea', // baby-blue (secondario)
                    backgroundColor: 'rgba(153, 205, 234, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#4B3F72',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
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
                        ticks: { color: '#4B3F72' }
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
