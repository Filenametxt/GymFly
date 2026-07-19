<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Area Cliente</title>
    <link class="style-theme-link" rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        @media screen and (max-width: 768px) {
            .dashboard-header {
                height: auto !important;
                min-height: auto !important;
                padding: 1rem 1.2rem !important; /* Ridotto il padding verticale per rendere la card più corta */
                flex-direction: column !important;
                align-items: flex-start !important;
                justify-content: center !important;
                margin-bottom: 1.5rem !important;
            }
            .dashboard-header .columns {
                margin: 0 !important;
                width: 100% !important;
            }
            .dashboard-header .column {
                padding: 0 !important;
            }
            .dashboard-header .title {
                font-size: 1.8rem !important; /* Testo più grande e leggibile */
                margin-bottom: 0.3rem !important;
            }
            .dashboard-header .subtitle {
                font-size: 1.1rem !important; /* Testo più grande e leggibile */
            }
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- ================= HEADER RESPONSIVO (Garantisce gli stessi elementi visuali) ================= -->
            <div class="dashboard-header">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-2 has-text-white mb-2">Ciao, {$utente->getNome()}!</h1>
                        <p class="subtitle is-5 has-text-white-ter">Pronto per l'allenamento di oggi?</p>
                    </div>
                </div>
            </div>

            <!-- ================= CONTENUTI PRINCIPALI ================= -->
            <div class="columns">
                
                <!-- COLONNA SCHEDA ALLENAMENTO -->
                <div class="column is-12-mobile is-5-tablet is-4-desktop">
                    <div class="box" style="height: 100%;">
                        <h3 class="title is-4 mb-4 style-theme-text">
                            <i class="fas fa-dumbbell mr-2" style="color: var(--gymfly-primary);"></i> Scheda Attiva
                        </h3>
                        
                        {if $utente->getScheda()}
                            <div class="p-4 mb-4" style="background-color: var(--gymfly-bg); border-radius: 12px; border-left: 4px solid var(--gymfly-primary);">
                                <h4 class="title is-5 style-theme-text mb-2">{$utente->getScheda()->getNome_scheda()}</h4>
                                <p class="is-size-7 has-text-grey-dark">{$utente->getScheda()->getObiettivo()|default:'Nessuna descrizione'}</p>
                            </div>
                            <div class="has-text-right">
                                <a href="visualizza-scheda" class="button is-gymfly is-small">
                                    <span>Vedi Esercizi</span>
                                    <span class="icon"><i class="fas fa-arrow-right"></i></span>
                                </a>
                            </div>
                        {else}
                            <div class="has-text-centered has-text-grey py-5">
                                <span class="icon is-large mb-2"><i class="fas fa-dumbbell fa-2x"></i></span>
                                <p class="is-size-6">Nessuna scheda di allenamento assegnata.</p>
                            </div>
                        {/if}
                    </div>
                </div>

                <!-- COLONNA CORSI PROGRAMMATI -->
                <div class="column is-12-mobile is-7-tablet is-8-desktop">
                    <div class="box" style="height: 100%;">
                        <h3 class="title is-4 mb-4 style-theme-text">
                            <i class="fas fa-calendar-day mr-2" style="color: var(--gymfly-secondary);"></i> Attività di Oggi
                        </h3>
                        
                        <div class="event-list">
                            {foreach $attivitaOggi as $corso}
                                <div class="p-4 mb-3" style="background-color: var(--gymfly-bg); border-radius: 12px; border-left: 4px solid var(--gymfly-secondary);">
                                    <div class="is-flex is-align-items-center is-justify-content-between">
                                        <div class="is-flex is-align-items-center">
                                            <span class="tag is-success is-light font-weight-bold mr-3">
                                                <i class="far fa-clock mr-1"></i> {$corso->getOrario()}:00
                                            </span>
                                            <strong class="is-size-5 style-theme-text">{$corso->getAttivita()->getNome()}</strong>
                                        </div>
                                    </div>
                                    <div class="mt-3 columns is-mobile is-gapless is-size-7 has-text-grey-dark">
                                        <div class="column">
                                            <i class="fas fa-user-ninja mr-1"></i> Trainer: <strong>{$corso->getAllenatore()->getNome()} {$corso->getAllenatore()->getCognome()}</strong>
                                        </div>
                                        <div class="column">
                                            <i class="fas fa-map-marker-alt mr-1"></i> Sala: <strong>{$corso->getSala()->getNome()}</strong>
                                        </div>
                                    </div>
                                </div>
                            {foreachelse}
                                <div class="has-text-centered py-5 my-auto">
                                    <span class="icon is-large has-text-grey mb-2"><i class="fas fa-info-circle fa-2x"></i></span>
                                    <p class="has-text-grey">Nessuna attività programmata per oggi.</p>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= WIDGET MISURE CORPOREE (Inserito Sotto) ================= -->
            <div class="box mt-5">
                <div class="has-text-centered mb-5">
                    <h3 class="title is-4 style-theme-text mb-2">
                        <i class="fas fa-heartbeat mr-2" style="color: #ff3860;"></i> Parametri Biometrici
                    </h3>
                    <a href="aggiorna-misure" class="button is-link is-light is-small">
                        <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                        <span>Storico & Aggiorna</span>
                    </a>
                </div>

                {if $ultimaMisure}
                    <div class="columns is-multiline is-mobile is-centered">
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Peso</p>
                            <p class="title is-4 style-theme-text">{$ultimaMisure->getPeso()} kg</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Altezza</p>
                            <p class="title is-4 style-theme-text">{$ultimaMisure->getAltezza()} cm</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Bicipite (Dx/Sx)</p>
                            <p class="title is-4 style-theme-text">{$ultimaMisure->getBicipiteDestro()|default:'0'}/{$ultimaMisure->getBicipiteSinistro()|default:'0'} cm</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Tricipite (Dx/Sx)</p>
                            <p class="title is-4 style-theme-text">{$ultimaMisure->getTricipiteDestro()|default:'0'}/{$ultimaMisure->getTricipiteSinistro()|default:'0'} cm</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Petto</p>
                            <p class="title is-4 style-theme-text">{if $ultimaMisure->getMisuraPetto()}{$ultimaMisure->getMisuraPetto()} cm{else}-{/if}</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Spalle</p>
                            <p class="title is-4 style-theme-text">{if $ultimaMisure->getMisuraSpalle()}{$ultimaMisure->getMisuraSpalle()} cm{else}-{/if}</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Coscia (Dx/Sx)</p>
                            <p class="title is-4 style-theme-text">{$ultimaMisure->getCosciaDestra()|default:'0'}/{$ultimaMisure->getCosciaSinistra()|default:'0'} cm</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Polpaccio (Dx/Sx)</p>
                            <p class="title is-4 style-theme-text">{$ultimaMisure->getPolpaccioDestro()|default:'0'}/{$ultimaMisure->getPolpaccioSinistro()|default:'0'} cm</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Vita</p>
                            <p class="title is-4 style-theme-text">{if $ultimaMisure->getMisuraVita()}{$ultimaMisure->getMisuraVita()} cm{else}-{/if}</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet is-2-desktop has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Fianchi</p>
                            <p class="title is-4 style-theme-text">{if $ultimaMisure->getMisuraFianchi()}{$ultimaMisure->getMisuraFianchi()} cm{else}-{/if}</p>
                        </div>
                    </div>
                {else}
                    <div class="has-text-centered has-text-grey py-5">
                        <span class="icon is-large mb-2"><i class="fas fa-weight fa-2x"></i></span>
                        <p class="is-size-6 mb-3">Nessuna misurazione registrata di recente.</p>
                        <a href="inserisci-misure" class="button is-gymfly is-small">Inserisci Misure Corporee</a>
                    </div>
                {/if}
            </div>

        </main>
    </div>

</body>
</html>
