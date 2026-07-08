<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - La mia Scheda</title>
    <link class="sheet" rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=1.2">
    <style>
        .custom-mobile-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .workout-session-box {
            border-left: 6px solid var(--gymfly-primary);
            margin-bottom: 2rem;
        }
        .exercise-item-box {
            background-color: var(--gymfly-bg);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid var(--gymfly-accent);
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand is-flex is-justify-content-between is-align-items-center w-100 px-3">
                <a href="dashboard-cliente" class="button is-ghost has-text-grey pl-0">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Dashboard</span>
                </a>
                <div class="navbar-item py-0">
                    <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">SCHEDA DETTAGLI</strong>
                </div>
                <div style="width: 50px;"></div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section px-3">
        <div class="container custom-mobile-container">
            
            <!-- HEADER SCHEDA -->
            <div class="box p-4 mb-4 has-background-link-light" style="border: 2px solid var(--gymfly-primary); border-radius: 16px;">
                <span class="tag is-success is-light mb-2">SCHEDA ATTIVA</span>
                <h1 class="title is-3 style-theme-text mb-1">{$scheda->getNome_scheda()}</h1>
                <p class="subtitle is-6 has-text-grey-dark mb-3">Obiettivo: <strong>{$scheda->getObiettivo()}</strong></p>
                <p class="is-size-7 has-text-grey">Creato da: <strong>Coach {$scheda->getAllenatore()->getNome()} {$scheda->getAllenatore()->getCognome()}</strong></p>
                <p class="is-size-7 has-text-grey">Validità: <strong>{$scheda->getData_inizio()->format('d/m/Y')}</strong> al <strong>{$scheda->getData_fine()->format('d/m/Y')}</strong></p>

                <div class="buttons mt-4">
                    <a href="modifica-dettagli" class="button is-gymfly is-small is-fullwidth">
                        <i class="fas fa-edit mr-2"></i> Aggiorna Carico & Ripetizioni
                    </a>
                    <a href="esporta-scheda" target="_blank" class="button is-link is-light is-small is-fullwidth">
                        <i class="fas fa-file-download mr-2"></i> Esporta in PDF
                    </a>
                </div>
            </div>

            <!-- WORKOUTS LIST -->
            {foreach $scheda->getAllenamenti() as $allenamento}
                <div class="box p-4 workout-session-box">
                    <h2 class="title is-4 style-theme-text mb-1"><i class="fas fa-dumbbell mr-2"></i>{$allenamento->getNome()}</h2>
                    <p class="subtitle is-6 has-text-grey mb-4">{$allenamento->getDescrizione()|pulisci_descrizione|default:'Sessione di allenamento'}</p>

                    <!-- EXERCISES IN WORKOUT -->
                    {foreach $allenamento->getDettagli() as $dettaglio}
                        <div class="exercise-item-box">
                            <div class="columns is-mobile is-vcentered">
                                <div class="column is-8">
                                    <h3 class="title is-5 style-theme-text mb-1">{$dettaglio->getEsercizio()->getNomeEsercizio()}</h3>
                                    
                                    <div class="tags mb-2">
                                        <span class="tag is-light is-primary-light">{$dettaglio->getSerie()} x {$dettaglio->getRipetizioni()}</span>
                                        <span class="tag is-info is-light">Carico: <strong>{$dettaglio->getCarico()} Kg</strong></span>
                                    </div>
                                    
                                    <p class="is-size-7 has-text-grey-dark">
                                        <i class="fas fa-clock mr-1"></i> Recupero: {$allenamento->getDescrizione()|estrai_recupero:$dettaglio->getEsercizio()->getNomeEsercizio()}
                                    </p>
                                </div>
                                <div class="column is-4 has-text-centered">
                                    {if $dettaglio->getEsercizio()->getImmagine()}
                                        <figure class="image is-64x64 is-inline-block" style="border-radius: 8px; overflow: hidden; border: 1px solid var(--gymfly-primary);">
                                            <img src="data:image/jpeg;base64,{$dettaglio->getEsercizio()->getImmagine()|base64_encode}" alt="Esercizio">
                                        </figure>
                                    {else}
                                        <span class="icon is-large has-text-grey-light"><i class="fas fa-image fa-2x"></i></span>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {foreachelse}
                        <p class="has-text-grey-light is-size-7">Nessun esercizio per questa sessione.</p>
                    {/foreach}
                </div>
            {foreachelse}
                <div class="box has-text-centered py-5">
                    <span class="icon is-large has-text-grey-light"><i class="fas fa-file-invoice fa-2x"></i></span>
                    <p class="has-text-grey">Nessun allenamento presente in questa scheda.</p>
                </div>
            {/foreach}

        </div>
    </section>

</body>
</html>
