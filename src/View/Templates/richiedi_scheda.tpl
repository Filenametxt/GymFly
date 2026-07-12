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

            <!-- FORM PRINCIPALE -->
            <form action="richiedi-scheda" method="POST">
                
                <div class="columns">
                    <div class="column is-12">
                        <div class="box">
                            <h3 class="title is-5 mb-4 style-theme-text">Parametri Richiesta</h3>
                            
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
                            <div class="field mb-4">
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

                        </div>
                    </div>
                </div>

                <!-- BOX OBIETTIVO (Sotto) -->
                <div class="box mt-5">
                    <h3 class="title is-5 style-theme-text mb-3">Obiettivo principale della scheda *</h3>
                    <div class="control">
                        <textarea class="textarea" name="obiettivo" required placeholder="Es: Definizione estiva, Aumento massa ipertrofica, Ricondizionamento posturale, ecc." rows="5"></textarea>
                    </div>
                </div>

                <!-- PULSANTI INVIA -->
                <div class="field is-grouped mt-5">
                    <div class="control is-expanded">
                        <button class="button is-gymfly is-fullwidth" type="submit">
                            <i class="fas fa-paper-plane mr-2"></i> Invia Richiesta
                        </button>
                    </div>
                </div>

            </form>
        </main>
    </div>

</body>
</html>
