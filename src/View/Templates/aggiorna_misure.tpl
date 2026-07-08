<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Parametri Biometrici</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=1.6">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- ================= MOBILE HEADER (Coerente con dashboard_cliente) ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <!-- Spazio per non sovrapporsi al tasto toggle fixed della sidebar su mobile -->
                <div style="width: 45px;"></div>
                <div class="client-logo-circle mr-3">
                    logo
                </div>
                <strong class="is-size-4 style-theme-text mr-2" style="letter-spacing: 1px;">PARAMETRI</strong>
                <a href="inserisci-misure" class="button is-outlined is-link is-small" title="Modifica parametri">
                    <span class="icon"><i class="fas fa-pencil-alt"></i></span>
                </a>
            </div>

            <!-- BACK BUTTON -->
            <div class="mb-4">
                <a href="profilo" class="button is-ghost has-text-grey pl-0">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Torna al Profilo</span>
                </a>
            </div>

            <!-- HEADER (DESKTOP) -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-mobile">
                <h1 class="title is-3 style-theme-text mb-0 mr-3" style="letter-spacing: 1px;">PARAMETRI</h1>
                <a href="inserisci-misure" class="button is-outlined is-link" title="Modifica parametri">
                    <span class="icon"><i class="fas fa-pencil-alt"></i></span>
                </a>
            </div>

            <!-- ================= CONTENUTI PRINCIPALI RESPONSIVE ================= -->
            <div class="columns">
                
                <!-- COLONNA PARAMETRI (1/3 su Desktop, full width su Mobile) -->
                <div class="column is-4-desktop is-12-tablet">
                    
                    <!-- HEADER PARAMETRI BIOMETRICI -->
                    <div class="mb-4">
                        <h2 class="title is-4 style-theme-text mb-0" style="letter-spacing: 1px;">BIOMETRICI</h2>
                    </div>

                    <!-- PARAMETRI BIOMETRICI BOX -->
                    <div class="box p-5 mb-5" style="height: calc(100% - 2.5rem);">
                        <div class="is-flex is-align-items-center is-justify-content-between pb-3 mb-3" style="border-bottom: 2px solid var(--gymfly-primary);">
                            <h3 class="title is-5 style-theme-text mb-0">PARAMETRI BIOMETRICI</h3>
                            <a href="visualizza-grafico?tipo=peso" class="has-text-grey">
                                <span class="icon"><i class="fas fa-chevron-right fa-lg"></i></span>
                            </a>
                        </div>
                        <div class="py-2" style="border-bottom: 1px solid rgba(175, 175, 226, 0.2);">
                            <span class="has-text-weight-semibold has-text-grey is-size-6">Peso:</span>
                            <div class="is-size-5 mt-1">{if $ultimaMisure}{$ultimaMisure->getPeso()} kg{else}-{/if}</div>
                        </div>
                        <div class="py-2">
                            <span class="has-text-weight-semibold has-text-grey is-size-6">Altezza:</span>
                            <div class="is-size-5 mt-1">{if $ultimaMisure}{$ultimaMisure->getAltezza()} cm{else}-{/if}</div>
                        </div>
                    </div>
                </div>

                <!-- COLONNA MISURE (2/3 su Desktop, full width su Mobile) -->
                <div class="column is-8-desktop is-12-tablet">
                    
                    <!-- HEADER MISURE -->
                    <div class="mb-4">
                        <h2 class="title is-4 style-theme-text mb-0" style="letter-spacing: 1px;">MISURE CORPOREE</h2>
                    </div>

                    <!-- GRIGLIA MISURE: SUPERIORE E INFERIORE AFFIANCATI SU DESKTOP -->
                    <div class="columns">
                        
                        <!-- PARTE SUPERIORE -->
                        <div class="column is-6-desktop is-12-tablet">
                            <div class="box p-5 mb-5" style="height: 100%;">
                                <div class="is-flex is-align-items-center is-justify-content-between pb-3 mb-3" style="border-bottom: 2px solid var(--gymfly-primary);">
                                    <h3 class="title is-5 style-theme-text mb-0">PARTE SUPERIORE</h3>
                                    <a href="visualizza-grafico?tipo=superiore" class="has-text-grey">
                                        <span class="icon"><i class="fas fa-chevron-right fa-lg"></i></span>
                                    </a>
                                </div>
                                <div class="py-2" style="border-bottom: 1px solid rgba(175, 175, 226, 0.2);">
                                    <span class="has-text-weight-semibold has-text-grey is-size-6">Misura bicipite dx/sx:</span>
                                    <div class="is-size-5 mt-1">
                                        {if $ultimaMisure}
                                            {$ultimaMisure->getBicipiteDestro()|default:'0'} / {$ultimaMisure->getBicipiteSinistro()|default:'0'} cm
                                        {else}
                                            - / - cm
                                        {/if}
                                    </div>
                                </div>
                                <div class="py-2" style="border-bottom: 1px solid rgba(175, 175, 226, 0.2);">
                                    <span class="has-text-weight-semibold has-text-grey is-size-6">Misura tricipite dx/sx:</span>
                                    <div class="is-size-5 mt-1">
                                        {if $ultimaMisure}
                                            {$ultimaMisure->getTricipiteDestro()|default:'0'} / {$ultimaMisure->getTricipiteSinistro()|default:'0'} cm
                                        {else}
                                            - / - cm
                                        {/if}
                                    </div>
                                </div>
                                <div class="py-2" style="border-bottom: 1px solid rgba(175, 175, 226, 0.2);">
                                    <span class="has-text-weight-semibold has-text-grey is-size-6">Misura petto:</span>
                                    <div class="is-size-5 mt-1">{if $ultimaMisure && $ultimaMisure->getMisuraPetto()}{$ultimaMisure->getMisuraPetto()} cm{else}-{/if}</div>
                                </div>
                                <div class="py-2">
                                    <span class="has-text-weight-semibold has-text-grey is-size-6">Misura spalle:</span>
                                    <div class="is-size-5 mt-1">{if $ultimaMisure && $ultimaMisure->getMisuraSpalle()}{$ultimaMisure->getMisuraSpalle()} cm{else}-{/if}</div>
                                </div>
                            </div>
                        </div>

                        <!-- PARTE INFERIORE -->
                        <div class="column is-6-desktop is-12-tablet">
                            <div class="box p-5 mb-5" style="height: 100%;">
                                <div class="is-flex is-align-items-center is-justify-content-between pb-3 mb-3" style="border-bottom: 2px solid var(--gymfly-primary);">
                                    <h3 class="title is-5 style-theme-text mb-0">PARTE INFERIORE</h3>
                                    <a href="visualizza-grafico?tipo=inferiore" class="has-text-grey">
                                        <span class="icon"><i class="fas fa-chevron-right fa-lg"></i></span>
                                    </a>
                                </div>
                                <div class="py-2" style="border-bottom: 1px solid rgba(175, 175, 226, 0.2);">
                                    <span class="has-text-weight-semibold has-text-grey is-size-6">Misura coscia dx/sx:</span>
                                    <div class="is-size-5 mt-1">
                                        {if $ultimaMisure}
                                            {$ultimaMisure->getCosciaDestra()|default:'0'} / {$ultimaMisure->getCosciaSinistra()|default:'0'} cm
                                        {else}
                                            - / - cm
                                        {/if}
                                    </div>
                                </div>
                                <div class="py-2" style="border-bottom: 1px solid rgba(175, 175, 226, 0.2);">
                                    <span class="has-text-weight-semibold has-text-grey is-size-6">Misura polpaccio dx/sx:</span>
                                    <div class="is-size-5 mt-1">
                                        {if $ultimaMisure}
                                            {$ultimaMisure->getPolpaccioDestro()|default:'0'} / {$ultimaMisure->getPolpaccioSinistro()|default:'0'} cm
                                        {else}
                                            - / - cm
                                        {/if}
                                    </div>
                                </div>
                                <div class="py-2" style="border-bottom: 1px solid rgba(175, 175, 226, 0.2);">
                                    <span class="has-text-weight-semibold has-text-grey is-size-6">Misura vita:</span>
                                    <div class="is-size-5 mt-1">{if $ultimaMisure && $ultimaMisure->getMisuraVita()}{$ultimaMisure->getMisuraVita()} cm{else}-{/if}</div>
                                </div>
                                <div class="py-2">
                                    <span class="has-text-weight-semibold has-text-grey is-size-6">Misura fianchi:</span>
                                    <div class="is-size-5 mt-1">{if $ultimaMisure && $ultimaMisure->getMisuraFianchi()}{$ultimaMisure->getMisuraFianchi()} cm{else}-{/if}</div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </main>
    </div>

</body>
</html>
