<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Richiedi Scheda</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .custom-mobile-container {
            max-width: 500px;
            margin: 0 auto;
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
                    <span>Indietro</span>
                </a>
                <div class="navbar-item py-0">
                    <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">RICHIESTA SCHEDA</strong>
                </div>
                <div style="width: 50px;"></div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section px-3">
        <div class="container custom-mobile-container">
            
            <div class="box p-5">
                <div class="has-text-centered mb-5">
                    <span class="icon is-large has-text-link">
                        <i class="fas fa-paper-plane fa-3x" style="color: var(--gymfly-primary);"></i>
                    </span>
                    <h2 class="title is-4 style-theme-text mt-3">Richiedi Scheda al Coach</h2>
                    <p class="subtitle is-6 has-text-grey">Invia i tuoi obiettivi e preferenze per ricevere una scheda personalizzata</p>
                </div>

                <form action="richiedi-scheda" method="POST">
                    
                    <!-- OBIETTIVO -->
                    <div class="field mb-4">
                        <label class="label">Obiettivo principale della scheda *</label>
                        <div class="control">
                            <textarea class="textarea" name="obiettivo" required placeholder="Es: Definizione estiva, Aumento massa ipertrofica, Ricondizionamento posturale, ecc." rows="3"></textarea>
                        </div>
                    </div>

                    <!-- NUMERO ALLENAMENTI -->
                    <div class="field mb-4">
                        <label class="label">Numero allenamenti per ogni ciclo/settimana *</label>
                        <div class="control has-icons-left">
                            <input class="input" type="number" name="n_allenamenti" required min="1" max="7" value="3" placeholder="Es: 3 o 4">
                            <span class="icon is-small is-left">
                                <i class="fas fa-redo-alt"></i>
                            </span>
                        </div>
                    </div>

                    <!-- ALLENATORE -->
                    <div class="field mb-5">
                        <label class="label">Seleziona Allenatore *</label>
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth">
                                <select name="cf_allenatore" required>
                                    <option value="">-- Scegli il tuo Coach --</option>
                                    {foreach $allenatori as $coach}
                                        <option value="{$coach->getCF()}">{$coach->getNome()} {$coach->getCognome()}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <span class="icon is-small is-left">
                                <i class="fas fa-user-ninja"></i>
                            </span>
                        </div>
                    </div>

                    <!-- INVIA -->
                    <div class="field mt-5">
                        <div class="control">
                            <button class="button is-gymfly is-fullwidth" type="submit">
                                <i class="fas fa-check-circle mr-2"></i> Invia Richiesta
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </section>

</body>
</html>
