<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Gestione Scheda</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            
            <!-- BACK LINK -->
            <div class="mb-4">
                <a href="visualizza-profilo?id={$scheda->getCliente()->getId()}" class="button is-ghost has-text-grey pl-0">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Torna al Profilo Cliente</span>
                </a>
            </div>

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
                            Realizza Scheda
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Crea e personalizza la scheda di allenamento per il cliente
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-clipboard-list fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <a href="visualizza-profilo?id={$scheda->getCliente()->getId()}" class="has-text-grey-dark" style="width: 45px; display: flex; align-items: center; justify-content: center;">
                    <span class="icon is-medium"><i class="fas fa-arrow-left fa-lg"></i></span>
                </a>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px; flex-grow: 1;">REALIZZA SCHEDA</strong>
            </div>

            <!-- SCHEDA FORM -->
            <div class="control-box">
                <form id="form-scheda" action="modifica-scheda?id={$scheda->getId()}" method="POST">
                            <input type="hidden" name="id_scheda" id="id_scheda" value="{$scheda->getId()}">
                            <!-- BOX METADATI CON NOME COGNOME ATLETA E INPUT (Layout bozza) -->
                            <div class="box mb-5">
                                <div class="field mb-4">
                                    <label class="label"><i class="fas fa-user-circle mr-2" style="color: var(--gymfly-primary);"></i> Atleta Cliente</label>
                                    <div class="control">
                                        <input class="input" type="text" readonly value="{$scheda->getCliente()->getNome()} {$scheda->getCliente()->getCognome()} ({$scheda->getCliente()->getCF()})">
                                    </div>
                                </div>
                                
                                <div class="columns is-multiline">
                                    <!-- NOME SCHEDA -->
                                    <div class="column is-6">
                                        <div class="field">
                                            <label class="label">Nome della Scheda *</label>
                                            <div class="control has-icons-left">
                                                <input class="input" type="text" name="nome_scheda" value="{$scheda->getNome_scheda()|escape}" required placeholder="Es: Massa Periodo 1">
                                                <span class="icon is-small is-left"><i class="fas fa-tag"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- OBIETTIVO -->
                                    <div class="column is-6">
                                        <div class="field">
                                            <label class="label">Obiettivo *</label>
                                            <div class="control has-icons-left">
                                                <input class="input" type="text" name="obiettivo" value="{$scheda->getObiettivo()|escape}" required placeholder="Es: Aumento massa muscolare">
                                                <span class="icon is-small is-left"><i class="fas fa-bullseye"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- DATA INIZIO -->
                                    <div class="column is-6">
                                        <div class="field">
                                            <label class="label">Data Inizio *</label>
                                            <div class="control has-icons-left">
                                                <input class="input" type="date" name="data_inizio" value="{$scheda->getData_inizio()->format('Y-m-d')}" required>
                                                <span class="icon is-small is-left"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- DATA FINE -->
                                    <div class="column is-6">
                                        <div class="field">
                                            <label class="label">Data Fine *</label>
                                            <div class="control has-icons-left">
                                                <input class="input" type="date" name="data_fine" value="{$scheda->getData_fine()->format('Y-m-d')}" required>
                                                <span class="icon is-small is-left"><i class="fas fa-calendar-check"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTENITORE ALLENAMENTI (Grande box centrale) -->
                            <div class="box mb-5 p-5">
                                <div class="mb-5">
                                    <h3 class="title is-4 style-theme-text mb-0"><i class="fas fa-running mr-2"></i> Allenamenti della Scheda</h3>
                                </div>
                                {assign var="letters" value=['A','B','C','D','E','F','G']}
                                <div id="workouts-container">
                                    {foreach $scheda->getAllenamenti() as $wIndex => $allenamento}
                                        <div class="box workout-box mb-4" data-workout-index="{$wIndex}">
                                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 1rem;">
                                                <h4 class="title is-5 style-theme-text mb-0 workout-title-label">ALLENAMENTO {$letters[$wIndex]}</h4>
                                                <button type="button" class="button is-danger is-small remove-workout-btn" title="Rimuovi Allenamento">
                                                    <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                                    <span>Rimuovi Allenamento</span>
                                                </button>
                                            </div>

                                            <div class="columns">
                                                <div class="column is-6">
                                                    <div class="field">
                                                        <label class="label">Nome Allenamento (Sessione)</label>
                                                        <div class="control">
                                                            <input class="input" type="text" name="workouts[{$wIndex}][nome]" value="{$allenamento->getNome()|escape}" required placeholder="Es: Allenamento A - Petto/Bicipiti">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="column is-6">
                                                    <div class="field">
                                                        <label class="label">Note / Descrizione</label>
                                                        <div class="control">
                                                            <input class="input" type="text" name="workouts[{$wIndex}][descrizione]" value="{$allenamento->getDescrizione()|escape}" placeholder="Es: Sessione del lunedì">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="exercises-container mt-4">
                                                {assign var="currentExId" value=""}
                                                {assign var="firstGroup" value=true}
                                                
                                                {foreach $allenamento->getDettagliOrdinati() as $dettaglio}
                                                    {if $currentExId !== $dettaglio->getEsercizio()->getId()}
                                                        {if !$firstGroup}
                                                                </tbody>
                                                            </table>
                                                            <div class="has-text-right">
                                                                <button type="button" class="button is-small is-gymfly add-series-btn">
                                                                    <i class="fas fa-plus mr-1"></i> Aggiungi Serie
                                                                </button>
                                                            </div>
                                                        </div> <!-- chiude exercise-group previous -->
                                                        {/if}
                                                        {assign var="firstGroup" value=false}
                                                        {assign var="currentExId" value=$dettaglio->getEsercizio()->getId()}
                                                        
                                                        <div class="box exercise-group p-4 mb-4" style="border: 1px solid var(--gymfly-accent); border-radius: 12px; background-color: #fafafa;">
                                                            <div class="columns is-vcentered mb-3">
                                                                <div class="column is-8">
                                                                    <div class="field">
                                                                        <label class="label">Esercizio *</label>
                                                                        <div class="control">
                                                                            <div class="select is-fullwidth">
                                                                                <select class="select-esercizio" required>
                                                                                    <option value="">-- Seleziona --</option>
                                                                                    {foreach $esercizi as $ex}
                                                                                        <option value="{$ex->getId()}" data-tipologia="{$ex->getTipologia()->getNomeTipologia()|lower}" {if $dettaglio->getEsercizio()->getId() == $ex->getId()}selected{/if}>
                                                                                            {$ex->getNomeEsercizio()}
                                                                                        </option>
                                                                                    {/foreach}
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="column is-4 has-text-right">
                                                                    <button type="button" class="button is-danger is-small remove-exercise-group-btn mt-4">
                                                                        <span class="icon is-small"><i class="fas fa-trash"></i></span>
                                                                        <span>Rimuovi Esercizio</span>
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <table class="table is-fullwidth is-striped mb-2 series-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 80px;">Serie</th>
                                                                        <th>Ripetizioni</th>
                                                                        <th>Carico (Kg) *</th>
                                                                        <th>Tempo</th>
                                                                        <th style="width: 50px;"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="series-container">
                                                    {/if}
                                                    
                                                    {assign var="nomeTipo" value=$dettaglio->getEsercizio()->getTipologia()->getNomeTipologia()|lower}
                                                    {assign var="isDurata" value=($nomeTipo === 'durata')}
                                                    <tr class="series-row">
                                                         <td class="has-text-centered is-vcentered">
                                                             <span class="series-number-label">{$dettaglio->getSerie()}</span>
                                                             <input type="hidden" data-name="serie" value="{$dettaglio->getSerie()}">
                                                             <input type="hidden" data-name="esercizio_id" value="{$dettaglio->getEsercizio()->getId()}">
                                                         </td>
                                                         <td>
                                                             <input class="input" type="number" data-name="ripetizioni" value="{$dettaglio->getRipetizioni()}" {if !$isDurata}required min="1"{else}disabled{/if}>
                                                         </td>
                                                         <td>
                                                             <input class="input" type="number" step="0.5" data-name="carico" value="{$dettaglio->getCarico()}" required min="0">
                                                         </td>
                                                         <td>
                                                             <input class="input" type="text" data-name="tempo" placeholder="Es: 90s" value="{$dettaglio->getTempo()}" {if $isDurata}required{else}disabled{/if}>
                                                         </td>
                                                         <td>
                                                             <button type="button" class="button is-small is-danger remove-series-btn" title="Rimuovi Serie">
                                                                 <span class="icon is-small"><i class="fas fa-times"></i></span>
                                                             </button>
                                                         </td>
                                                     </tr>
                                                {/foreach}
                                                
                                                {if !$firstGroup}
                                                                </tbody>
                                                            </table>
                                                            <div class="has-text-right">
                                                                <button type="button" class="button is-small is-gymfly add-series-btn">
                                                                    <i class="fas fa-plus mr-1"></i> Aggiungi Serie
                                                                </button>
                                                            </div>
                                                        </div> <!-- chiude l'ultimo exercise-group -->
                                                {/if}
                                            </div>

                                            <div class="has-text-right mt-2">
                                                <button type="button" class="button is-small is-gymfly add-exercise-group-btn">
                                                    <i class="fas fa-plus mr-1"></i> Aggiungi Esercizio
                                                </button>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>

                                <div class="has-text-right mt-3">
                                    <button type="button" class="button is-gymfly" id="add-workout-btn">
                                        <i class="fas fa-plus mr-1"></i> Aggiungi Allenamento (A, B, C...)
                                    </button>
                                </div>
                            </div>

                            <!-- ACTION BUTTONS -->
                            <div class="field is-grouped is-grouped-right mt-5">
                                <div class="control">
                                    <a href="elimina-scheda?id={$scheda->getId()}" class="button is-danger">
                                        <i class="fas fa-trash-alt mr-2"></i> Elimina Scheda
                                    </a>
                                </div>
                                <div class="control">
                                    <button type="submit" class="button is-success">
                                        <i class="fas fa-paper-plane mr-2"></i> Manda
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
        </main>
    </div>

    <script src="js/gestione_scheda.js"></script>

    <!-- TEMPLATES HTML5 PER INSERIMENTO DINAMICO -->
    <template id="tmpl-workout">
        {include file='allenamento_template.tpl'}
    </template>

    <template id="tmpl-exercise-group">
        {include file='gruppo_esercizio_template.tpl'}
    </template>

    <template id="tmpl-series-row">
        {include file='riga_serie_template.tpl'}
    </template>
</body>
</html>
