<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Modifica Dettagli Tecnici</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .custom-mobile-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .detail-item-box {
            border: 2px solid var(--gymfly-primary);
            border-radius: 12px;
            padding: 1.25rem;
            background-color: var(--gymfly-card-bg);
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand is-flex is-justify-content-between is-align-items-center w-100 px-3">
                <a href="visualizza-scheda" class="button is-ghost has-text-grey pl-0">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Indietro</span>
                </a>
                <div class="navbar-item py-0">
                    <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">AGGIORNA DATI</strong>
                </div>
                <div style="width: 50px;"></div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section px-3">
        <div class="container custom-mobile-container">
            
            <div class="box p-4 mb-4">
                <div class="has-text-centered mb-5">
                    <span class="icon is-large has-text-link">
                        <i class="fas fa-edit fa-3x" style="color: var(--gymfly-primary);"></i>
                    </span>
                    <h2 class="title is-4 style-theme-text mt-3">Aggiorna Dettagli Tecnici</h2>
                    <p class="subtitle is-6 has-text-grey">Aggiorna il carico e le ripetizioni per ciascun esercizio del tuo piano d'allenamento attuale</p>
                </div>

                <form action="modifica-dettagli" method="POST">
                    
                    {foreach $scheda->getAllenamenti() as $allenamento}
                        <h3 class="title is-5 style-theme-text mt-4 mb-3"><i class="fas fa-dumbbell mr-2"></i>{$allenamento->getNome()}</h3>
                        
                        {foreach $allenamento->getDettagli() as $dettaglio}
                            <div class="detail-item-box">
                                <h4 class="title is-6 mb-3">{$dettaglio->getEsercizio()->getNomeEsercizio()} - Serie {$dettaglio->getSerie()}</h4>
                                
                                <div class="columns is-mobile">
                                    <!-- RIPETIZIONI -->
                                    <div class="column is-4">
                                        <div class="field">
                                            <label class="label">Ripetizioni</label>
                                            <div class="control has-icons-left">
                                                <input class="input" type="number" name="dettagli[{$dettaglio->getId()}][ripetizioni]" value="{$dettaglio->getRipetizioni()}" required min="1">
                                                <span class="icon is-small is-left"><i class="fas fa-redo"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- CARICO -->
                                    <div class="column is-4">
                                        <div class="field">
                                            <label class="label">Carico (Kg)</label>
                                            <div class="control has-icons-left">
                                                <input class="input" type="number" step="0.5" name="dettagli[{$dettaglio->getId()}][carico]" value="{$dettaglio->getCarico()}" required min="0">
                                                <span class="icon is-small is-left"><i class="fas fa-weight-hanging"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- RECUPERO -->
                                    <div class="column is-4">
                                        <div class="field">
                                            <label class="label">Recupero</label>
                                            <div class="control has-icons-left">
                                                <input class="input" type="text" name="dettagli[{$dettaglio->getId()}][recupero]" placeholder="Es: 90s" value="{$allenamento->getDescrizione()|estrai_recupero:$dettaglio->getEsercizio()->getNomeEsercizio():$dettaglio->getSerie():$dettaglio->getId()}">
                                                <span class="icon is-small is-left"><i class="fas fa-stopwatch"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {foreachelse}
                            <p class="has-text-grey-light is-size-7 mb-4">Nessun esercizio per questa sessione.</p>
                        {/foreach}
                    {foreachelse}
                        <p class="has-text-grey-light is-size-6 mb-4">La tua scheda non contiene allenamenti.</p>
                    {/foreach}

                    <div class="field mt-5">
                        <div class="control">
                            <button class="button is-gymfly is-fullwidth" type="submit">
                                <i class="fas fa-save mr-2"></i> Salva e Aggiorna Scheda
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </section>

</body>
</html>
