<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Parametri Biometrici</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .biometric-box {
            border: 2px solid var(--gymfly-accent) !important;
            border-radius: 15px !important;
            box-shadow: 0 8px 16px rgba(0,0,0,0.02) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .measurement-row {
            gap: 0.4rem;
        }
        .measurement-value {
            white-space: nowrap;
        }
        .biometric-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(175, 175, 226, 0.12) !important;
        }
        .biometric-box a.has-text-grey:hover {
            color: var(--gymfly-primary) !important;
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">MISURE</strong>
            </div>

            <!-- BACK BUTTON -->
            <div class="mb-4 has-text-left">
                <a href="profilo{if !$isSelf}?id={$utente->getId()}{/if}" class="button is-ghost has-text-grey pl-0">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Torna al Profilo</span>
                </a>
            </div>

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
                        <h1 class="title is-2 has-text-white mb-2">
                            Parametri Biometrici
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Visualizza lo storico e l'andamento delle tue misurazioni fisiche
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <span class="icon is-large has-text-white" style="margin-right: 1.5rem;">
                            <i class="fas fa-heartbeat fa-3x"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ================= CONTENUTI PRINCIPALI RESPONSIVE ================= -->
            <div class="columns">
                
                <!-- COLONNA PARAMETRI (1/3 su Desktop, full width su Mobile) -->
                <div class="column is-4-desktop is-12-tablet">
                    
                    <!-- PARAMETRI BIOMETRICI BOX -->
                    <div class="box biometric-box p-5 mb-5" style="height: 100%;">
                        <div class="is-flex is-align-items-center is-justify-content-between pb-3 mb-4" style="border-bottom: 2px solid var(--gymfly-accent);">
                            <h3 class="title is-5 style-theme-text mb-0"><i class="fas fa-weight-hanging mr-2 has-text-info"></i>Biometrici</h3>
                            <a href="visualizza-grafico?tipo=peso{if !$isSelf}&id={$utente->getId()}{/if}" class="has-text-grey" title="Visualizza Grafico">
                                <span class="icon"><i class="fas fa-chevron-right fa-lg"></i></span>
                            </a>
                        </div>
                        
                        <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center" style="border-bottom: 1px solid rgba(175, 175, 226, 0.15);">
                            <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-weight mr-2 has-text-info"></i>Peso</span>
                            <strong class="measurement-value is-size-6 style-theme-text">{if $ultimaMisure}{$ultimaMisure->getPeso()} kg{else}-{/if}</strong>
                        </div>
                        
                        <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center">
                            <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-ruler-vertical mr-2 has-text-info"></i>Altezza</span>
                            <strong class="measurement-value is-size-6 style-theme-text">{if $ultimaMisure}{$ultimaMisure->getAltezza()} cm{else}-{/if}</strong>
                        </div>
                    </div>
                </div>

                <!-- COLONNA MISURE (2/3 su Desktop, full width su Mobile) -->
                <div class="column is-8-desktop is-12-tablet">
                    
                    <!-- GRIGLIA MISURE: SUPERIORE E INFERIORE AFFIANCATI SU DESKTOP -->
                    <div class="columns">
                        
                        <!-- PARTE SUPERIORE -->
                        <div class="column is-6-desktop is-12-tablet">
                            <div class="box biometric-box p-5 mb-5" style="height: 100%;">
                                <div class="is-flex is-align-items-center is-justify-content-between pb-3 mb-4" style="border-bottom: 2px solid var(--gymfly-accent);">
                                    <h3 class="title is-5 style-theme-text mb-0"><i class="fas fa-child mr-2 has-text-primary"></i>Parte Superiore</h3>
                                    <a href="visualizza-grafico?tipo=superiore{if !$isSelf}&id={$utente->getId()}{/if}" class="has-text-grey" title="Visualizza Grafico">
                                        <span class="icon"><i class="fas fa-chevron-right fa-lg"></i></span>
                                    </a>
                                </div>
                                
                                <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center" style="border-bottom: 1px solid rgba(175, 175, 226, 0.15);">
                                    <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-dumbbell mr-2 has-text-primary"></i>Bicipite dx/sx</span>
                                    <strong class="measurement-value is-size-6 style-theme-text">
                                        {if $ultimaMisure}
                                            {$ultimaMisure->getBicipiteDestro()|default:'0'} / {$ultimaMisure->getBicipiteSinistro()|default:'0'} cm
                                        {else}
                                            - / - cm
                                        {/if}
                                    </strong>
                                </div>
                                
                                <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center" style="border-bottom: 1px solid rgba(175, 175, 226, 0.15);">
                                    <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-dumbbell mr-2 has-text-primary"></i>Tricipite dx/sx</span>
                                    <strong class="measurement-value is-size-6 style-theme-text">
                                        {if $ultimaMisure}
                                            {$ultimaMisure->getTricipiteDestro()|default:'0'} / {$ultimaMisure->getTricipiteSinistro()|default:'0'} cm
                                        {else}
                                            - / - cm
                                        {/if}
                                    </strong>
                                </div>
                                
                                <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center" style="border-bottom: 1px solid rgba(175, 175, 226, 0.15);">
                                    <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-heart mr-2 has-text-primary"></i>Misura Petto</span>
                                    <strong class="measurement-value is-size-6 style-theme-text">{if $ultimaMisure && $ultimaMisure->getMisuraPetto()}{$ultimaMisure->getMisuraPetto()} cm{else}-{/if}</strong>
                                </div>
                                
                                <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center">
                                    <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-arrows-alt-h mr-2 has-text-primary"></i>Misura Spalle</span>
                                    <strong class="measurement-value is-size-6 style-theme-text">{if $ultimaMisure && $ultimaMisure->getMisuraSpalle()}{$ultimaMisure->getMisuraSpalle()} cm{else}-{/if}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- PARTE INFERIORE -->
                        <div class="column is-6-desktop is-12-tablet">
                            <div class="box biometric-box p-5 mb-5" style="height: 100%;">
                                <div class="is-flex is-align-items-center is-justify-content-between pb-3 mb-4" style="border-bottom: 2px solid var(--gymfly-accent);">
                                    <h3 class="title is-5 style-theme-text mb-0"><i class="fas fa-running mr-2 has-text-success"></i>Parte Inferiore</h3>
                                    <a href="visualizza-grafico?tipo=inferiore{if !$isSelf}&id={$utente->getId()}{/if}" class="has-text-grey" title="Visualizza Grafico">
                                        <span class="icon"><i class="fas fa-chevron-right fa-lg"></i></span>
                                    </a>
                                </div>
                                
                                <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center" style="border-bottom: 1px solid rgba(175, 175, 226, 0.15);">
                                    <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-walking mr-2 has-text-success"></i>Coscia dx/sx</span>
                                    <strong class="measurement-value is-size-6 style-theme-text">
                                        {if $ultimaMisure}
                                            {$ultimaMisure->getCosciaDestra()|default:'0'} / {$ultimaMisure->getCosciaSinistra()|default:'0'} cm
                                        {else}
                                            - / - cm
                                        {/if}
                                    </strong>
                                </div>
                                
                                <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center" style="border-bottom: 1px solid rgba(175, 175, 226, 0.15);">
                                    <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-shoe-prints mr-2 has-text-success"></i>Polpaccio dx/sx</span>
                                    <strong class="measurement-value is-size-6 style-theme-text">
                                        {if $ultimaMisure}
                                            {$ultimaMisure->getPolpaccioDestro()|default:'0'} / {$ultimaMisure->getPolpaccioSinistro()|default:'0'} cm
                                        {else}
                                            - / - cm
                                        {/if}
                                    </strong>
                                </div>
                                
                                <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center" style="border-bottom: 1px solid rgba(175, 175, 226, 0.15);">
                                    <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-compress-arrows-alt mr-2 has-text-success"></i>Misura Vita</span>
                                    <strong class="measurement-value is-size-6 style-theme-text">{if $ultimaMisure && $ultimaMisure->getMisuraVita()}{$ultimaMisure->getMisuraVita()} cm{else}-{/if}</strong>
                                </div>
                                
                                <div class="measurement-row py-3 is-flex is-justify-content-between is-align-items-center">
                                    <span class="has-text-weight-semibold has-text-grey"><i class="fas fa-expand-arrows-alt mr-2 has-text-success"></i>Misura Fianchi</span>
                                    <strong class="measurement-value is-size-6 style-theme-text">{if $ultimaMisure && $ultimaMisure->getMisuraFianchi()}{$ultimaMisure->getMisuraFianchi()} cm{else}-{/if}</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- TASTO AGGIORNA MISURE (in basso a destra) -->
            {if $smarty.session.ruolo_utente !== 'allenatore'}
            <div class="has-text-right mt-4 mb-5">
                <a href="inserisci-misure{if !$isSelf}?id={$utente->getId()}{/if}" class="button is-gymfly" title="Aggiorna parametri" style="border-radius: 10px;">
                    <span class="icon"><i class="fas fa-pencil-alt mr-2"></i></span>
                    <span>Aggiorna Misure</span>
                </a>
            </div>
            {/if}

        </main>
    </div>

</body>
</html>
