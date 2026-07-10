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
                    
                    <!-- OPTION: COPIA DA UN ALTRO CLIENTE -->
                    {if $altre_schede|@count > 0}
                    <div class="control-box mb-5">
                        <h3 class="title is-5 mb-3" style="color: #AFAFE2;">
                            <i class="fas fa-copy mr-2"></i> Copia Programma da altro Cliente
                        </h3>
                        <p class="subtitle is-6 has-text-grey-dark mb-4">
                            Scegli una scheda esistente nel sistema per importarne l'elenco degli allenamenti e degli esercizi in questa scheda.
                        </p>
                        <div class="field has-addons">
                            <div class="control is-expanded">
                                <div class="select is-fullwidth">
                                    <select id="select-copia-scheda">
                                        <option value="">-- Seleziona Scheda Sorgente --</option>
                                        {foreach $altre_schede as $als}
                                            <option value="{$als->getId()}">
                                                {$als->getCliente()->getNome()} {$als->getCliente()->getCognome()} - {$als->getNome_scheda()}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="control">
                                <button type="button" class="button is-gymfly" id="btn-copia-scheda">
                                    <i class="fas fa-clone mr-2"></i> Copia Struttura
                                </button>
                            </div>
                        </div>
                    </div>
                    {/if}

                    <!-- SCHEDA FORM -->
                    <div class="control-box">
                        <!-- HEADER CON TITOLO E TASTO ELIMINA (Top-right con display flex inline per compatibilità) -->
                        <div style="display: flex; justify-content: space-between; align-items: center;" class="mb-5">
                            <h1 class="title is-2 style-theme-text mb-0">REALIZZA SCHEDA</h1>
                            <a href="elimina-scheda?id={$scheda->getId()}" class="button is-danger" onclick="return confirm('Sei sicuro di voler eliminare questa scheda e tutti i suoi allenamenti?')">
                                <span class="icon"><i class="fas fa-trash"></i></span>
                                <span>Elimina</span>
                            </a>
                        </div>

                        <form id="form-scheda" action="salva-scheda" method="POST">
                            <input type="hidden" name="id_scheda" id="id_scheda" value="{$scheda->getId()}">
                            <input type="hidden" name="azione" id="azione-field" value="salva">

                            {assign var="em" value=App\Infrastructure\Doctrine\EntityManagerFactory::create()}
                            {assign var="clienti" value=$em->getRepository('App\Entity\Cliente')->findBy(['palestra' => $utente->getPalestra()])}

                            <!-- BOX METADATI CON NOME COGNOME ATLETA E INPUT (Layout bozza) -->
                            <div class="box mb-5">
                                <div class="field mb-4">
                                    <label class="label"><i class="fas fa-user-circle mr-2" style="color: var(--gymfly-primary);"></i> Atleta Cliente</label>
                                    <div class="control">
                                        <div class="select is-fullwidth">
                                            <select id="select-cambia-cliente">
                                                {foreach $clienti as $c}
                                                    <option value="{$c->getCF()}" {if $c->getCF() === $scheda->getCliente()->getCF()}selected{/if} data-scheda-id="{if $c->getScheda()}{$c->getScheda()->getId()}{else}0{/if}">
                                                        {$c->getNome()} {$c->getCognome()} ({$c->getCF()})
                                                    </option>
                                                {/foreach}
                                            </select>
                                        </div>
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
                                {assign var="letters" value=['A','B','C','D','E','F','G','H','I','J']}
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
                                                
                                                {foreach $allenamento->getDettagli() as $dettaglio}
                                                    {if $currentExId !== $dettaglio->getEsercizio()->getId()}
                                                        {if !$firstGroup}
                                                                </tbody>
                                                            </table>
                                                            <div class="has-text-right">
                                                                <button type="button" class="button is-small is-link is-light add-series-btn">
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
                                                                                        <option value="{$ex->getId()}" {if $dettaglio->getEsercizio()->getId() == $ex->getId()}selected{/if}>
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
                                                                        <th>Ripetizioni *</th>
                                                                        <th>Carico (Kg) *</th>
                                                                        <th>Recupero *</th>
                                                                        <th style="width: 50px;"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="series-container">
                                                    {/if}
                                                    
                                                    <tr class="series-row">
                                                        <td class="has-text-centered is-vcentered">
                                                            <span class="series-number-label">{$dettaglio->getSerie()}</span>
                                                            <input type="hidden" data-name="serie" value="{$dettaglio->getSerie()}">
                                                            <input type="hidden" data-name="esercizio_id" value="{$dettaglio->getEsercizio()->getId()}">
                                                        </td>
                                                        <td>
                                                            <input class="input" type="number" data-name="ripetizioni" value="{$dettaglio->getRipetizioni()}" required min="1">
                                                        </td>
                                                        <td>
                                                            <input class="input" type="number" step="0.5" data-name="carico" value="{$dettaglio->getCarico()}" required min="0">
                                                        </td>
                                                        <td>
                                                            <input class="input" type="text" data-name="recupero" placeholder="Es: 90s" value="{$allenamento->getDescrizione()|estrai_recupero:$dettaglio->getEsercizio()->getNomeEsercizio():$dettaglio->getSerie():$dettaglio->getId()}">
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
                                                                <button type="button" class="button is-small is-link is-light add-series-btn">
                                                                    <i class="fas fa-plus mr-1"></i> Aggiungi Serie
                                                                </button>
                                                            </div>
                                                        </div> <!-- chiude l'ultimo exercise-group -->
                                                {/if}
                                            </div>

                                            <div class="has-text-right mt-2">
                                                <button type="button" class="button is-small is-link is-light add-exercise-group-btn">
                                                    <i class="fas fa-plus mr-1"></i> Aggiungi Esercizio
                                                </button>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>

                                <div class="has-text-right mt-3">
                                    <button type="button" class="button is-link" id="add-workout-btn">
                                        <i class="fas fa-plus mr-1"></i> Aggiungi Allenamento (A, B, C...)
                                    </button>
                                </div>
                            </div>

                            <!-- ACTION BUTTONS (In basso a destra come da bozza: "Manda") -->
                            <div class="field is-grouped is-grouped-right mt-5">
                                <div class="control">
                                    <button type="button" class="button is-success" id="btn-save-send">
                                        <i class="fas fa-paper-plane mr-2"></i> Manda
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
        </main>
    </div>

    <!-- UI ESERCIZI LIST TEMPLATE (JSON-ENCODED PER JAVASCRIPT) -->
    <script>
        const eserciziDisponibili = [
            {foreach $esercizi as $ex}
                { id: "{$ex->getId()}", nome: "{$ex->getNomeEsercizio()|escape:'javascript'}" },
            {/foreach}
        ];
        const schedaId = "{$scheda->getId()}";
    </script>

    <!-- SCRIPT GESTIONE FORM DINAMICO -->
    <script>
        {literal}
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('workouts-container');
            const addWorkoutBtn = document.getElementById('add-workout-btn');
            const form = document.getElementById('form-scheda');
            const actionField = document.getElementById('azione-field');

            // 1. Gestione Copia Scheda da altro cliente
            const btnCopia = document.getElementById('btn-copia-scheda');
            if (btnCopia) {
                btnCopia.addEventListener('click', () => {
                    const select = document.getElementById('select-copia-scheda');
                    if (select.value) {
                        window.location.href = `modifica-scheda?id=${ schedaId }&copia_da=${ select.value }`;
                    } else {
                        alert('Seleziona una scheda da copiare.');
                    }
                });
            }

            // 1.1 Gestione cambio cliente dal menu a tendina
            const selectCliente = document.getElementById('select-cambia-cliente');
            if (selectCliente) {
                selectCliente.addEventListener('change', () => {
                    const cf = selectCliente.value;
                    const option = selectCliente.options[selectCliente.selectedIndex];
                    const targetSchedaId = parseInt(option.getAttribute('data-scheda-id') || '0');
                    if (targetSchedaId > 0) {
                        window.location.href = `modifica-scheda?id=${targetSchedaId}`;
                    } else {
                        window.location.href = `crea-scheda?cf=${cf}`;
                    }
                });
            }

            // 2. Click Invio al Cliente
            document.getElementById('btn-save-send').addEventListener('click', () => {
                actionField.value = 'invia';
                form.submit();
            });

            // 3. Aggiungi Sessione di Allenamento
            addWorkoutBtn.addEventListener('click', () => {
                const wIndex = container.children.length;
                if (wIndex >= 7) return;

                const letter = String.fromCharCode(65 + wIndex);
                const workoutHtml = `
                    <div class="box workout-box mb-4" data-workout-index="${wIndex}">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 1rem;">
                            <h4 class="title is-5 style-theme-text mb-0 workout-title-label">ALLENAMENTO ${letter}</h4>
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
                                        <input class="input" type="text" name="workouts[${wIndex}][nome]" required placeholder="Es: Allenamento A - Petto/Bicipiti">
                                    </div>
                                </div>
                            </div>
                            <div class="column is-6">
                                <div class="field">
                                    <label class="label">Note / Descrizione</label>
                                    <div class="control">
                                        <input class="input" type="text" name="workouts[${wIndex}][descrizione]" placeholder="Es: Sessione del lunedì">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="exercises-container mt-4">
                            <!-- Qui verranno inseriti i gruppi di esercizi -->
                        </div>
                        <div class="has-text-right mt-2">
                            <button type="button" class="button is-small is-link is-light add-exercise-group-btn">
                                <i class="fas fa-plus mr-1"></i> Aggiungi Esercizio
                            </button>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', workoutHtml);
                attachWorkoutEvents(container.lastElementChild);
                reindexWorkouts();
            });

            // 4. Attach events to existing workout blocks
            Array.from(container.children).forEach(block => {
                attachWorkoutEvents(block);
            });
            reindexWorkouts();

            function attachWorkoutEvents(workoutBlock) {
                const wIndex = workoutBlock.getAttribute('data-workout-index');
                const exercisesContainer = workoutBlock.querySelector('.exercises-container');
                const addExGroupBtn = workoutBlock.querySelector('.add-exercise-group-btn');
                const removeWBtn = workoutBlock.querySelector('.remove-workout-btn');

                // Rimuovi Sessione
                removeWBtn.addEventListener('click', () => {
                    workoutBlock.remove();
                    reindexWorkouts();
                });

                // Aggiungi Esercizio (nuovo gruppo)
                addExGroupBtn.addEventListener('click', () => {
                    let optionsHtml = '<option value="">-- Seleziona --</option>';
                    eserciziDisponibili.forEach(ex => {
                        optionsHtml += `<option value="${ex.id}">${ex.nome}</option>`;
                    });

                    const groupHtml = `
                        <div class="box exercise-group p-4 mb-4" style="border: 1px solid var(--gymfly-accent); border-radius: 12px; background-color: #fafafa;">
                            <div class="columns is-vcentered mb-3">
                                <div class="column is-8">
                                    <div class="field">
                                        <label class="label">Esercizio *</label>
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select class="select-esercizio" required>
                                                    ${optionsHtml}
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
                                        <th>Ripetizioni *</th>
                                        <th>Carico (Kg) *</th>
                                        <th>Recupero *</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody class="series-container">
                                    <!-- Prima serie di default -->
                                    <tr class="series-row">
                                        <td class="has-text-centered is-vcentered">
                                            <span class="series-number-label">1</span>
                                            <input type="hidden" data-name="serie" value="1">
                                            <input type="hidden" data-name="esercizio_id" value="">
                                        </td>
                                        <td>
                                            <input class="input" type="number" data-name="ripetizioni" value="10" required min="1">
                                        </td>
                                        <td>
                                            <input class="input" type="number" step="0.5" data-name="carico" value="0" required min="0">
                                        </td>
                                        <td>
                                            <input class="input" type="text" data-name="recupero" placeholder="Es: 90s" value="120s">
                                        </td>
                                        <td>
                                            <button type="button" class="button is-small is-danger remove-series-btn" title="Rimuovi Serie">
                                                <span class="icon is-small"><i class="fas fa-times"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="has-text-right">
                                <button type="button" class="button is-small is-link is-light add-series-btn">
                                    <i class="fas fa-plus mr-1"></i> Aggiungi Serie
                                </button>
                            </div>
                        </div>
                    `;

                    exercisesContainer.insertAdjacentHTML('beforeend', groupHtml);
                    const newGroup = exercisesContainer.lastElementChild;
                    bindExerciseGroupEvents(newGroup, exercisesContainer, wIndex);
                    reindexExercises(exercisesContainer, wIndex);
                });

                // Collega eventi ai gruppi esistenti
                Array.from(exercisesContainer.children).forEach(group => {
                    bindExerciseGroupEvents(group, exercisesContainer, wIndex);
                });
            }

            function bindExerciseGroupEvents(group, exercisesContainer, wIndex) {
                const selectEx = group.querySelector('.select-esercizio');
                const removeGroupBtn = group.querySelector('.remove-exercise-group-btn');
                const addSeriesBtn = group.querySelector('.add-series-btn');
                const seriesContainer = group.querySelector('.series-container');

                // Rimuovi Esercizio (tutto il gruppo)
                removeGroupBtn.addEventListener('click', () => {
                    group.remove();
                    reindexExercises(exercisesContainer, wIndex);
                });

                // Sincronizza esercizio_id nascosto nelle serie quando cambia il select dell'esercizio
                selectEx.addEventListener('change', () => {
                    const exId = selectEx.value;
                    Array.from(seriesContainer.querySelectorAll('input[data-name="esercizio_id"]')).forEach(hiddenInput => {
                        hiddenInput.value = exId;
                    });
                });

                // Forza il valore iniziale di esercizio_id nelle serie esistenti
                const initialExId = selectEx.value;
                Array.from(seriesContainer.querySelectorAll('input[data-name="esercizio_id"]')).forEach(hiddenInput => {
                    hiddenInput.value = initialExId;
                });

                // Aggiungi Serie
                addSeriesBtn.addEventListener('click', () => {
                    const sIndex = seriesContainer.children.length;
                    const exId = selectEx.value;
                    
                    // Prendi valori dall'ultima serie (se presente) per agevolare l'inserimento
                    let lastReps = "10";
                    let lastCarico = "0";
                    let lastRecupero = "120s";
                    const lastRow = seriesContainer.lastElementChild;
                    if (lastRow) {
                        lastReps = lastRow.querySelector('input[data-name="ripetizioni"]').value;
                        lastCarico = lastRow.querySelector('input[data-name="carico"]').value;
                        lastRecupero = lastRow.querySelector('input[data-name="recupero"]').value;
                    }

                    const seriesHtml = `
                        <tr class="series-row">
                            <td class="has-text-centered is-vcentered">
                                <span class="series-number-label">${sIndex + 1}</span>
                                <input type="hidden" data-name="serie" value="${sIndex + 1}">
                                <input type="hidden" data-name="esercizio_id" value="${exId}">
                            </td>
                            <td>
                                <input class="input" type="number" data-name="ripetizioni" value="${lastReps}" required min="1">
                            </td>
                            <td>
                                <input class="input" type="number" step="0.5" data-name="carico" value="${lastCarico}" required min="0">
                            </td>
                            <td>
                                <input class="input" type="text" data-name="recupero" placeholder="Es: 90s" value="${lastRecupero}">
                            </td>
                            <td>
                                <button type="button" class="button is-small is-danger remove-series-btn" title="Rimuovi Serie">
                                    <span class="icon is-small"><i class="fas fa-times"></i></span>
                                </button>
                            </td>
                        </tr>
                    `;
                    seriesContainer.insertAdjacentHTML('beforeend', seriesHtml);
                    
                    // Associa eventi al pulsante rimozione della nuova serie
                    const newRow = seriesContainer.lastElementChild;
                    newRow.querySelector('.remove-series-btn').addEventListener('click', () => {
                        newRow.remove();
                        reindexSeriesRows(seriesContainer, selectEx.value);
                        reindexExercises(exercisesContainer, wIndex);
                    });

                    reindexSeriesRows(seriesContainer, selectEx.value);
                    reindexExercises(exercisesContainer, wIndex);
                });

                // Associa eventi per rimozione serie esistenti
                Array.from(seriesContainer.querySelectorAll('.remove-series-btn')).forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.target.closest('tr').remove();
                        reindexSeriesRows(seriesContainer, selectEx.value);
                        reindexExercises(exercisesContainer, wIndex);
                    });
                });
            }

            function reindexSeriesRows(seriesContainer, exId) {
                Array.from(seriesContainer.children).forEach((tr, sIndex) => {
                    tr.querySelector('.series-number-label').textContent = sIndex + 1;
                    tr.querySelector('input[data-name="serie"]').value = sIndex + 1;
                    tr.querySelector('input[data-name="esercizio_id"]').value = exId;
                });
            }

            function reindexWorkouts() {
                const count = container.children.length;
                if (count >= 7) {
                    addWorkoutBtn.style.display = 'none';
                } else {
                    addWorkoutBtn.style.display = 'inline-block';
                }

                Array.from(container.children).forEach((block, wIndex) => {
                    block.setAttribute('data-workout-index', wIndex);
                    
                    const letter = String.fromCharCode(65 + wIndex);
                    block.querySelector('.workout-title-label').textContent = 'ALLENAMENTO ' + letter;

                    block.querySelector('input[name*="[nome]"]').setAttribute('name', `workouts[${wIndex}][nome]`);
                    block.querySelector('input[name*="[descrizione]"]').setAttribute('name', `workouts[${wIndex}][descrizione]`);
                    
                    const exercisesContainer = block.querySelector('.exercises-container');
                    reindexExercises(exercisesContainer, wIndex);
                });
            }

            function reindexExercises(exercisesContainer, wIndex) {
                let globalExIndex = 0;
                Array.from(exercisesContainer.children).forEach(group => {
                    const selectEx = group.querySelector('.select-esercizio');
                    const seriesContainer = group.querySelector('.series-container');
                    
                    Array.from(seriesContainer.children).forEach(tr => {
                        tr.setAttribute('data-exercise-index', globalExIndex);
                        
                        const inputExId = tr.querySelector('input[data-name="esercizio_id"]');
                        const inputSerie = tr.querySelector('input[data-name="serie"]');
                        const inputReps = tr.querySelector('input[data-name="ripetizioni"]');
                        const inputCarico = tr.querySelector('input[data-name="carico"]');
                        const inputRecupero = tr.querySelector('input[data-name="recupero"]');

                        inputExId.setAttribute('name', `workouts[${wIndex}][dettagli][${globalExIndex}][esercizio_id]`);
                        inputSerie.setAttribute('name', `workouts[${wIndex}][dettagli][${globalExIndex}][serie]`);
                        inputReps.setAttribute('name', `workouts[${wIndex}][dettagli][${globalExIndex}][ripetizioni]`);
                        inputCarico.setAttribute('name', `workouts[${wIndex}][dettagli][${globalExIndex}][carico]`);
                        inputRecupero.setAttribute('name', `workouts[${wIndex}][dettagli][${globalExIndex}][recupero]`);

                        globalExIndex++;
                    });
                });
            }
        });
        {/literal}
    </script>
</body>
</html>
