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
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- ================= DESKTOP HEADER (Coerente con le altre Dashboard) ================= -->
            <div class="dashboard-header is-hidden-mobile">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-2 has-text-white mb-2">Ciao, {$utente->getNome()}!</h1>
                        <p class="subtitle is-5 has-text-white-ter">Pronto per l'allenamento di oggi?</p>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER (Da mockup Screenshot) ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <!-- Spazio per non sovrapporsi al tasto toggle fixed della sidebar su mobile -->
                <div style="width: 45px;"></div>
                <div class="client-logo-circle mr-3">
                    logo
                </div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">CLIENTE HOME</strong>
            </div>

            <!-- ================= MOBILE GREETING (Da mockup Screenshot - Diretto sullo sfondo) ================= -->
            <div class="has-text-centered mb-6 is-hidden-tablet">
                <div class="client-avatar-wrapper mb-3">
                    <div class="client-avatar-inner" style="{if $fotoProfilo}padding: 0; background: transparent;{/if}">
                        {if $fotoProfilo}
                            <img src="data:image/jpeg;base64,{$fotoProfilo}" alt="Foto Profilo" class="is-rounded" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        {else}
                            <span class="icon is-large">
                                <i class="fas fa-user fa-3x"></i>
                            </span>
                        {/if}
                    </div>
                </div>
                <h2 class="title is-3 style-theme-text mb-1">Ciao, {$utente->getNome()}!</h2>
                <p class="subtitle is-6 has-text-grey mt-1">Pronto per l'allenamento di oggi?</p>
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
                                <h4 class="title is-5 style-theme-text mb-2">{$utente->getScheda()->getNome()}</h4>
                                <p class="is-size-7 has-text-grey-dark">{$utente->getScheda()->getDescrizione()|default:'Nessuna descrizione'}</p>
                            </div>
                            <div class="has-text-right">
                                <a href="#" class="button is-gymfly is-small">
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
                            <i class="fas fa-calendar-day mr-2" style="color: var(--gymfly-secondary);"></i> Corsi di Oggi
                        </h3>
                        
                        <div class="event-list">
                            {foreach $utente->getAttivitaPianificate() as $corso}
                                <div class="p-4 mb-3" style="background-color: var(--gymfly-bg); border-radius: 12px; border-left: 4px solid var(--gymfly-secondary);">
                                    <div class="is-flex is-align-items-center is-justify-content-between">
                                        <div class="is-flex is-align-items-center">
                                            <span class="tag is-success is-light font-weight-bold mr-3">
                                                <i class="far fa-clock mr-1"></i> {$corso->getOrario()}:00
                                            </span>
                                            <strong class="is-size-5 style-theme-text">{$corso->getAttivita()->getNome()}</strong>
                                        </div>
                                        <span class="icon has-text-grey"><i class="fas fa-chevron-right"></i></span>
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
                                <div class="has-text-centered has-text-grey py-5">
                                    <span class="icon is-large mb-2"><i class="fas fa-calendar-times fa-2x"></i></span>
                                    <p class="is-size-6">Nessun corso programmato per oggi.</p>
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
                        <div class="column is-6-mobile is-3-tablet has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Peso</p>
                            <p class="title is-4 style-theme-text">{$ultimaMisure->getPeso()} kg</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Altezza</p>
                            <p class="title is-4 style-theme-text">{$ultimaMisure->getAltezza()} cm</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Bicipite (Dx/Sx)</p>
                            <p class="title is-4 style-theme-text">{$ultimaMisure->getBicipiteDestro()|default:'0'}/{$ultimaMisure->getBicipiteSinistro()|default:'0'} cm</p>
                        </div>
                        <div class="column is-6-mobile is-3-tablet has-text-centered py-3">
                            <p class="heading has-text-grey mb-1">Petto</p>
                            <p class="title is-4 style-theme-text">{if $ultimaMisure->getMisuraPetto()}{$ultimaMisure->getMisuraPetto()} cm{else}-{/if}</p>
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
