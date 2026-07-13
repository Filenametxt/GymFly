<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Nuova Attività</title>
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
            {if isset($ruolo_utente)}
                {if $ruolo_utente === 'amministratore'}
                    {assign var="headerClass" value="dashboard-header-admin"}
                {elseif $ruolo_utente === 'allenatore'}
                    {assign var="headerClass" value="dashboard-header-trainer"}
                {/if}
            {elseif isset($smarty.session.ruolo_utente)}
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
                            Crea Attività
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Registra una nuova disciplina sportiva nel catalogo generale della palestra
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-dumbbell fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px; flex-grow: 1;">CREA ATTIVITÀ</strong>
            </div>

            <!-- FORM PRINCIPALE -->
            <form action="crea-attivita" method="POST">
                
                <div class="columns">
                    
                    <!-- COLONNA PARAMETRI -->
                    <div class="column is-12">
                        <div class="box">
                            <h3 class="title is-5 mb-4 style-theme-text">Parametri Attività</h3>
                            
                            <!-- NOME -->
                            <div class="field mb-4">
                                <label class="label">Nome Attività *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="nome" required placeholder="Es: Pilates, Spinning, Zumba...">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- PARTECIPANTI -->
                            <div class="field mb-4">
                                <label class="label">Numero Massimo Partecipanti *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="number" name="max_partecipanti" required placeholder="Es: 15" min="1">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-users"></i>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- BOX DESCRIZIONE -->
                <div class="box mt-5">
                    <h3 class="title is-5 style-theme-text mb-3">Descrizione / Dettagli</h3>
                    <div class="control">
                        <textarea class="textarea" name="descrizione" required placeholder="Fornisci una breve descrizione delle finalità e svolgimento dell'attività..." rows="5"></textarea>
                    </div>
                </div>

                <!-- PULSANTI SALVATAGGIO -->
                <div class="field is-grouped mt-5">
                    <div class="control is-expanded">
                        <button class="button is-gymfly is-fullwidth" type="submit">
                            <i class="fas fa-save mr-2"></i> Crea Attività
                        </button>
                    </div>
                    <div class="control">
                        <a href="{$ritorno}" class="button is-danger is-light">
                            Annulla
                        </a>
                    </div>
                </div>

            </form>

        </main>
    </div>

</body>
</html>
