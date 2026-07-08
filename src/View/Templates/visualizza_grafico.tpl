<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - {$titolo}</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=1.2">
    {literal}
    <style>
        .custom-mobile-container {
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }
        .chart-container {
            background-color: var(--gymfly-bg);
            border-radius: 16px;
            padding: 1.5rem 1rem;
            border: 2px solid var(--gymfly-primary);
            overflow: hidden;
        }
    </style>
    {/literal}
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand is-flex is-justify-content-between is-align-items-center px-3" style="width: 100%;">
                
                <!-- Menu a Panino (Hamburger) -->
                <a role="button" class="navbar-burger ml-0" aria-label="menu" aria-expanded="false" data-target="grafico-navbar-menu" onclick="document.getElementById('grafico-navbar-menu').classList.toggle('is-active'); this.classList.toggle('is-active');">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>

                <!-- Titolo -->
                <div class="navbar-item py-0">
                    <strong class="is-size-5 style-theme-text" style="letter-spacing: 0.5px; text-transform: uppercase;">GRAFICO</strong>
                </div>

                <!-- Spazio vuoto a destra per simmetria -->
                <div style="width: 32px;"></div>

            </div>

            <!-- Menu che si espande sotto al click del panino -->
            <div id="grafico-navbar-menu" class="navbar-menu">
                <div class="navbar-end">
                    <a href="dashboard-cliente" class="navbar-item">
                        <span class="icon mr-2"><i class="fas fa-home"></i></span> Home Dashboard
                    </a>
                    <a href="profilo{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" class="navbar-item">
                        <span class="icon mr-2"><i class="fas fa-user-edit"></i></span> Il mio Profilo
                    </a>
                    <a href="messaggi" class="navbar-item">
                        <span class="icon mr-2"><i class="fas fa-envelope"></i></span> Bacheca Messaggi
                    </a>
                    <a href="cambia-password" class="navbar-item">
                        <span class="icon mr-2"><i class="fas fa-key"></i></span> Cambia Password
                    </a>
                    <hr class="navbar-divider">
                    <a href="logout" class="navbar-item has-text-danger">
                        <span class="icon mr-2"><i class="fas fa-sign-out-alt"></i></span> Log Out
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section px-3">
        <div class="container custom-mobile-container">
            
            <div class="mb-4">
                <a href="aggiorna-misure{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" class="button is-ghost has-text-grey pl-0">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Torna ai Parametri</span>
                </a>
            </div>

            <div class="box p-4">
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
    </section>

</body>
</html>
