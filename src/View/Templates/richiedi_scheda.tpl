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
        @media screen and (max-width: 768px) {
            .app-content {
                padding-top: 5rem !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            .mobile-column-header {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }
            .mobile-column-header h1 {
                display: block !important;
                text-align: center !important;
            }
            .mobile-column-header h1 i {
                margin-right: 0.5rem !important;
                margin-bottom: 0 !important;
                font-size: 1.8rem !important;
            }
        }
    </style>
</head>
<body>
 
    <div class="app-container">
        {include file='sidebar.tpl'}
        
        <main class="app-content">
            <div class="columns is-centered">
                <div class="column is-10-tablet is-8-desktop is-12-mobile">
                    <!-- HEADER -->
                    <div class="mb-5 mobile-column-header has-text-centered-mobile">
                        <h1 class="title is-2 style-theme-text mb-2"><span style="white-space: nowrap;"><i class="fas fa-paper-plane mr-2"></i>Richiesta</span> Scheda</h1>
                        <p class="subtitle is-6 has-text-grey">Invia i tuoi obiettivi e preferenze per ricevere una scheda personalizzata dal tuo Coach</p>
                    </div>

                    <div class="box p-5">
                        <form action="richiedi-scheda" method="POST">
                            
                            <!-- OBIETTIVO -->
                            <div class="field mb-4">
                                <label class="label">Obiettivo principale della scheda *</label>
                                <div class="control">
                                    <textarea class="textarea" name="obiettivo" required placeholder="Es: Definizione estiva, Aumento massa ipertrofica, Ricondizionamento posturale, ecc." rows="4"></textarea>
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
                                        <i class="fas fa-check-circle mr-2"></i> Richiedi
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
