<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Abilitazione Attività</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="dashboard-admin">
                    <strong class="is-size-4" style="color: #AFAFE2;">GymFly 🏋️‍♂️</strong>
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-ten-twelfths-tablet is-eight-tenths-desktop">
                    
                    <div class="mb-5">
                        <a href="dashboard-admin" class="button is-ghost has-text-grey">
                            <span class="icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Torna alla Dashboard</span>
                        </a>
                    </div>

                    <div class="columns">
                        
                        <!-- FORM ASSEGNAZIONE -->
                        <div class="column is-5">
                            <div class="card p-5">
                                <h2 class="title is-4 style-theme-text mb-4">
                                    <span class="icon mr-2"><i class="fas fa-link"></i></span>
                                    Nuova Abilitazione
                                </h2>
                                <p class="subtitle is-6 has-text-grey mb-4">Abilita un allenatore all'insegnamento di una specifica disciplina.</p>

                                <form action="abilita-attivita-allenatore" method="POST">
                                    <input type="hidden" name="azione" value="abilita">

                                    <div class="field">
                                        <label class="label">Seleziona Allenatore *</label>
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select name="id_allenatore" required>
                                                    <option value="" disabled selected>Scegli un allenatore...</option>
                                                    {foreach $allenatori as $allenatore}
                                                        <option value="{$allenatore->getId()}">{$allenatore->getNome()} {$allenatore->getCognome()}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="field">
                                        <label class="label">Seleziona Attività *</label>
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select name="id_attivita" required>
                                                    <option value="" disabled selected>Scegli una disciplina...</option>
                                                    {foreach $attivita as $att}
                                                        <option value="{$att->getId()}">{$att->getNome()}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="button is-gymfly is-fullwidth mt-5" type="submit">
                                        <span class="icon mr-1"><i class="fas fa-plus"></i></span> Abilita Allenatore
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- LISTA ABILITAZIONI ATTUALI -->
                        <div class="column is-7">
                            <div class="card p-5">
                                <h2 class="title is-4 style-theme-text mb-4">
                                    <span class="icon mr-2"><i class="fas fa-user-shield"></i></span>
                                    Stato Abilitazioni
                                </h2>
                                <p class="subtitle is-6 has-text-grey mb-4">Discipline che ciascun allenatore è abilitato a tenere.</p>

                                <div class="content">
                                    {foreach $allenatori as $allenatore}
                                        <div class="box p-3 mb-3">
                                            <h3 class="title is-5 mb-2">{$allenatore->getNome()} {$allenatore->getCognome()}</h3>
                                            <div class="tags">
                                                {foreach $allenatore->getAttivitaAbilitate() as $att}
                                                    <span class="tag is-info is-light is-medium" style="margin-bottom: 0.5rem;">
                                                        {$att->getNome()}
                                                        <form action="abilita-attivita-allenatore" method="POST" style="display: inline; margin-left: 8px;">
                                                            <input type="hidden" name="id_allenatore" value="{$allenatore->getId()}">
                                                            <input type="hidden" name="id_attivita" value="{$att->getId()}">
                                                            <input type="hidden" name="azione" value="disabilita">
                                                            <button type="submit" class="delete is-small" style="vertical-align: middle;" title="Disabilita"></button>
                                                        </form>
                                                    </span>
                                                {foreachelse}
                                                    <span class="has-text-grey is-italic">Nessuna attività abilitata.</span>
                                                {/foreach}
                                            </div>
                                        </div>
                                    {foreachelse}
                                        <p class="has-text-centered has-text-grey py-4">Nessun allenatore registrato.</p>
                                    {/foreach}
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

</body>
</html>
