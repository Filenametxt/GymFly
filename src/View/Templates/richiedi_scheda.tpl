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
                            Richiedi Scheda
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Invia i tuoi obiettivi e preferenze per ricevere una scheda personalizzata dal tuo Coach
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <span class="icon is-large has-text-white" style="margin-right: 1.5rem;">
                            <i class="fas fa-paper-plane fa-3x"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">RICHIESTA SCHEDA</strong>
            </div>

            <div class="columns is-centered">
                <div class="column is-10-tablet is-8-desktop is-12-mobile">

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
