<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Parametri Biometrici</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=1.2">
    {literal}
    <style>
        .custom-mobile-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .parameter-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid var(--gymfly-primary);
            padding-bottom: 0.5rem;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .parameter-row {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid rgba(175, 175, 226, 0.2);
            font-size: 1.05rem;
        }
        .parameter-label {
            font-weight: 600;
            color: var(--gymfly-text);
        }
        .parameter-value {
            color: var(--gymfly-text);
        }
        .chart-container {
            background-color: var(--gymfly-bg);
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid var(--gymfly-primary);
        }
    </style>
    {/literal}
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand is-flex is-justify-content-between is-align-items-center w-100 px-3">
                
                <!-- Menu a Panino (Hamburger) -->
                <a role="button" class="navbar-burger ml-0" aria-label="menu" aria-expanded="false" data-target="misure-navbar-menu" onclick="document.getElementById('misure-navbar-menu').classList.toggle('is-active'); this.classList.toggle('is-active');">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>

                <!-- Titolo con Collegamento alla Nuova Pagina di Inserimento -->
                <div class="navbar-item py-0 is-flex is-align-items-center">
                    <strong class="is-size-4 style-theme-text mr-2" style="letter-spacing: 1px;">PARAMETRI</strong>
                    <a href="inserisci-misure{if !$isSelf}?id={$utente->getId()}{/if}" class="has-text-link">
                        <span class="icon"><i class="fas fa-pen fa-sm"></i></span>
                    </a>
                </div>

                <!-- Spazio vuoto a destra per simmetria -->
                <div style="width: 32px;"></div>

            </div>

            <!-- Menu che si espande sotto al click del panino -->
            <div id="misure-navbar-menu" class="navbar-menu">
                <div class="navbar-end">
                    <a href="dashboard-cliente" class="navbar-item">
                        <span class="icon mr-2"><i class="fas fa-home"></i></span> Home Dashboard
                    </a>
                    <a href="profilo{if !$isSelf}?id={$utente->getId()}{/if}" class="navbar-item">
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
                <a href="profilo{if !$isSelf}?id={$utente->getId()}{/if}" class="button is-ghost has-text-grey pl-0">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Torna al Profilo</span>
                </a>
            </div>

            <!-- PARAMETRI BIOMETRICI -->
            <div>
                <div class="parameter-section-header">
                    <h3 class="title is-5 style-theme-text mb-0">PARAMETRI BIOMETRICI</h3>
                    <a href="visualizza-grafico?tipo=peso{if !$isSelf}&id={$utente->getId()}{/if}" class="has-text-grey">
                        <span class="icon"><i class="fas fa-chevron-right fa-lg"></i></span>
                    </a>
                </div>
                <div class="parameter-row">
                    <span class="parameter-label">Peso:</span>
                    <span class="parameter-value">{if $ultimaMisure}{$ultimaMisure->getPeso()} kg{else}-{/if}</span>
                </div>
                <div class="parameter-row">
                    <span class="parameter-label">Altezza:</span>
                    <span class="parameter-value">{if $ultimaMisure}{$ultimaMisure->getAltezza()} cm{else}-{/if}</span>
                </div>
            </div>

            <!-- MISURE PARTE SUPERIORE -->
            <div class="mt-5">
                <div class="parameter-section-header">
                    <h3 class="title is-5 style-theme-text mb-0">PARTE SUPERIORE</h3>
                    <a href="visualizza-grafico?tipo=superiore{if !$isSelf}&id={$utente->getId()}{/if}" class="has-text-grey">
                        <span class="icon"><i class="fas fa-chevron-right fa-lg"></i></span>
                    </a>
                </div>
                <div class="parameter-row">
                    <span class="parameter-label">misura bicipite dx/sx:</span>
                    <span class="parameter-value">
                        {if $ultimaMisure}
                            {$ultimaMisure->getBicipiteDestro()|default:'0'} / {$ultimaMisure->getBicipiteSinistro()|default:'0'} cm
                        {else}
                            - / - cm
                        {/if}
                    </span>
                </div>
                <div class="parameter-row">
                    <span class="parameter-label">misura tricipite dx/sx:</span>
                    <span class="parameter-value">
                        {if $ultimaMisure}
                            {$ultimaMisure->getTricipiteDestro()|default:'0'} / {$ultimaMisure->getTricipiteSinistro()|default:'0'} cm
                        {else}
                            - / - cm
                        {/if}
                    </span>
                </div>
                <div class="parameter-row">
                    <span class="parameter-label">Misura petto:</span>
                    <span class="parameter-value">{if $ultimaMisure && $ultimaMisure->getMisuraPetto()}{$ultimaMisure->getMisuraPetto()} cm{else}-{/if}</span>
                </div>
                <div class="parameter-row">
                    <span class="parameter-label">Misura spalle:</span>
                    <span class="parameter-value">{if $ultimaMisure && $ultimaMisure->getMisuraSpalle()}{$ultimaMisure->getMisuraSpalle()} cm{else}-{/if}</span>
                </div>
            </div>

            <!-- MISURE PARTE INFERIORE -->
            <div class="mt-5">
                <div class="parameter-section-header">
                    <h3 class="title is-5 style-theme-text mb-0">PARTE INFERIORE</h3>
                    <a href="visualizza-grafico?tipo=inferiore{if !$isSelf}&id={$utente->getId()}{/if}" class="has-text-grey">
                        <span class="icon"><i class="fas fa-chevron-right fa-lg"></i></span>
                    </a>
                </div>
                <div class="parameter-row">
                    <span class="parameter-label">misura coscia dx/sx:</span>
                    <span class="parameter-value">
                        {if $ultimaMisure}
                            {$ultimaMisure->getCosciaDestra()|default:'0'} / {$ultimaMisure->getCosciaSinistra()|default:'0'} cm
                        {else}
                            - / - cm
                        {/if}
                    </span>
                </div>
                <div class="parameter-row">
                    <span class="parameter-label">misura polpaccio dx/sx:</span>
                    <span class="parameter-value">
                        {if $ultimaMisure}
                            {$ultimaMisure->getPolpaccioDestro()|default:'0'} / {$ultimaMisure->getPolpaccioSinistro()|default:'0'} cm
                        {else}
                            - / - cm
                        {/if}
                    </span>
                </div>
                <div class="parameter-row">
                    <span class="parameter-label">Misura fianchi:</span>
                    <span class="parameter-value">{if $ultimaMisure && $ultimaMisure->getMisuraFianchi()}{$ultimaMisure->getMisuraFianchi()} cm{else}-{/if}</span>
                </div>
            </div>

        </div>
    </section>

</body>
</html>
