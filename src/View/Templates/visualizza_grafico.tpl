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

                    {if count($punti) > 0}
                        <div class="chart-container">
                            <svg viewBox="0 0 450 180" style="display: block; width: 100%; height: auto; max-width: 100%; overflow: hidden;">
                                <!-- Linee di griglia di sfondo -->
                                <line x1="40" y1="20" x2="430" y2="20" stroke="#dbdbdb" stroke-dasharray="5,5" />
                                <line x1="40" y1="70" x2="430" y2="70" stroke="#dbdbdb" stroke-dasharray="5,5" />
                                <line x1="40" y1="120" x2="430" y2="120" stroke="#dbdbdb" stroke-dasharray="5,5" />
                                <line x1="40" y1="140" x2="430" y2="140" stroke="var(--gymfly-primary)" stroke-width="2" />
                                
                                <!-- Polyline (linea continua dell'andamento) -->
                                {if count($punti) > 1}
                                    <polyline points="{foreach $punti as $p}{$p.x},{$p.y} {/foreach}" fill="none" stroke="var(--gymfly-secondary)" stroke-width="3" />
                                {/if}
                                
                                <!-- Punti (Cerchi) e Valori numerici -->
                                {foreach $punti as $p}
                                    <circle cx="{$p.x}" cy="{$p.y}" r="5" fill="var(--gymfly-text)" />
                                    
                                    <!-- Valore numerico sopra il punto -->
                                    <text x="{$p.x}" y="{$p.y - 8}" font-size="9" fill="var(--gymfly-text)" text-anchor="middle" font-weight="bold">{$p.valore}</text>
                                    
                                    <!-- Data di registrazione sotto l'asse X -->
                                    <text x="{$p.x}" y="155" font-size="9" fill="var(--gymfly-text)" text-anchor="middle">{$p.data}</text>
                                {/foreach}
                            </svg>
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

</body>
</html>
