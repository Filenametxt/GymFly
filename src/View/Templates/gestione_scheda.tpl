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
                            <a href="elimina-scheda?id={$scheda->getId()}" class="button is-danger is-outlined" onclick="return confirm('Sei sicuro di voler eliminare questa scheda e tutti i suoi allenamenti?')">
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
                                <div style="display: flex; justify-content: space-between; align-items: center;" class="mb-5">
                                    <h3 class="title is-4 style-theme-text mb-0"><i class="fas fa-running mr-2"></i> Allenamenti della Scheda</h3>
                                    <button type="button" class="button is-small is-success" id="add-workout-btn">
                                        <i class="fas fa-plus mr-1"></i> Aggiungi Allenamento (A, B, C...)
                                    </button>
                                </div>

                                <div id="workouts-container">
                                    {foreach $scheda->getAllenamenti() as $wIndex => $allenamento}
                                        <div class="box workout-box mb-4" data-workout-index="{$wIndex}">
                                            <div class="columns is-vcentered">
                                                <div class="column is-5">
                                                    <div class="field">
                                                        <label class="label">Nome Allenamento (Sessione)</label>
                                                        <div class="control">
                                                            <input class="input" type="text" name="workouts[{$wIndex}][nome]" value="{$allenamento->getNome()|escape}" required placeholder="Es: Allenamento A - Petto/Bicipiti">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="column is-5">
                                                    <div class="field">
                                                        <label class="label">Note / Descrizione</label>
                                                        <div class="control">
                                                            <input class="input" type="text" name="workouts[{$wIndex}][descrizione]" value="{$allenamento->getDescrizione()|escape}" placeholder="Es: Sessione del lunedì">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="column is-2 has-text-right">
                                                    <button type="button" class="button is-danger remove-workout-btn mt-4" title="Rimuovi Allenamento">
                                                        <span class="icon"><i class="fas fa-times"></i></span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="exercise-rows-container mt-4">
                                                <table class="table is-fullwidth is-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Esercizio *</th>
                                                            <th style="width: 100px;">Serie</th>
                                                            <th style="width: 100px;">Ripetizioni</th>
                                                            <th style="width: 120px;">Carico (Kg)</th>
                                                            <th style="width: 120px;">Recupero</th>
                                                            <th style="width: 60px;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {foreach $allenamento->getDettagli() as $dIndex => $dettaglio}
                                                            <tr data-exercise-index="{$dIndex}">
                                                                <td>
                                                                    <div class="select is-fullwidth">
                                                                        <select name="workouts[{$wIndex}][dettagli][{$dIndex}][esercizio_id]" required>
                                                                            <option value="">-- Seleziona --</option>
                                                                            {foreach $esercizi as $ex}
                                                                                <option value="{$ex->getId()}" {if $dettaglio->getEsercizio()->getId() == $ex->getId()}selected{/if}>
                                                                                    {$ex->getNomeEsercizio()}
                                                                                </option>
                                                                            {/foreach}
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input class="input" type="number" name="workouts[{$wIndex}][dettagli][{$dIndex}][serie]" value="{$dettaglio->getSerie()}" required min="1">
                                                                </td>
                                                                <td>
                                                                    <input class="input" type="number" name="workouts[{$wIndex}][dettagli][{$dIndex}][ripetizioni]" value="{$dettaglio->getRipetizioni()}" required min="1">
                                                                </td>
                                                                <td>
                                                                    <input class="input" type="number" step="0.5" name="workouts[{$wIndex}][dettagli][{$dIndex}][carico]" value="{$dettaglio->getCarico()}" required min="0">
                                                                </td>
                                                                <td>
                                                                    <input class="input" type="text" name="workouts[{$wIndex}][dettagli][{$dIndex}][recupero]" placeholder="Es: 90s" value="">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="button is-danger remove-exercise-btn" title="Rimuovi Esercizio">
                                                                        <span class="icon"><i class="fas fa-times"></i></span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        {/foreach}
                                                    </tbody>
                                                </table>
                                                <div class="has-text-left mt-2">
                                                    <button type="button" class="button is-small is-link is-light add-exercise-btn">
                                                        <i class="fas fa-plus mr-1"></i> Aggiungi Esercizio
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    {/foreach}
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
                const workoutHtml = `
                    <div class="box workout-box mb-4" data-workout-index="${wIndex}">
                        <div class="columns is-vcentered">
                            <div class="column is-5">
                                <div class="field">
                                    <label class="label">Nome Allenamento (Sessione)</label>
                                    <div class="control">
                                        <input class="input" type="text" name="workouts[${wIndex}][nome]" required placeholder="Es: Allenamento A - Petto/Bicipiti">
                                    </div>
                                </div>
                            </div>
                            <div class="column is-5">
                                <div class="field">
                                    <label class="label">Note / Descrizione</label>
                                    <div class="control">
                                        <input class="input" type="text" name="workouts[${wIndex}][descrizione]" placeholder="Es: Sessione del lunedì">
                                    </div>
                                </div>
                            </div>
                            <div class="column is-2 has-text-right">
                                <button type="button" class="button is-danger remove-workout-btn mt-4" title="Rimuovi Allenamento">
                                    <span class="icon"><i class="fas fa-times"></i></span>
                                </button>
                            </div>
                        </div>

                        <div class="exercise-rows-container mt-4">
                            <table class="table is-fullwidth is-striped">
                                <thead>
                                    <tr>
                                        <th>Esercizio *</th>
                                        <th style="width: 100px;">Serie</th>
                                        <th style="width: 100px;">Ripetizioni</th>
                                        <th style="width: 120px;">Carico (Kg)</th>
                                        <th style="width: 120px;">Recupero</th>
                                        <th style="width: 60px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Righe di esercizi dinamiche -->
                                </tbody>
                            </table>
                            <div class="has-text-left mt-2">
                                <button type="button" class="button is-small is-link is-light add-exercise-btn">
                                    <i class="fas fa-plus mr-1"></i> Aggiungi Esercizio
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', workoutHtml);
                attachWorkoutEvents(container.lastElementChild);
            });

            // 4. Attach events to existing workout blocks
            Array.from(container.children).forEach(block => {
                attachWorkoutEvents(block);
            });

            function attachWorkoutEvents(workoutBlock) {
                const wIndex = workoutBlock.getAttribute('data-workout-index');
                const tableBody = workoutBlock.querySelector('tbody');
                const addExBtn = workoutBlock.querySelector('.add-exercise-btn');
                const removeWBtn = workoutBlock.querySelector('.remove-workout-btn');

                // Rimuovi Sessione
                removeWBtn.addEventListener('click', () => {
                    workoutBlock.remove();
                    reindexWorkouts();
                });

                // Aggiungi Esercizio alla Sessione
                addExBtn.addEventListener('click', () => {
                    const exIndex = tableBody.children.length;
                    
                    let optionsHtml = '<option value="">-- Seleziona --</option>';
                    eserciziDisponibili.forEach(ex => {
                        optionsHtml += `<option value="${ex.id}">${ex.nome}</option>`;
                    });

                    const exHtml = `
                        <tr data-exercise-index="${exIndex}">
                            <td>
                                <div class="select is-fullwidth">
                                    <select name="workouts[${wIndex}][dettagli][${exIndex}][esercizio_id]" required>
                                        ${optionsHtml}
                                    </select>
                                </div>
                            </td>
                            <td>
                                <input class="input" type="number" name="workouts[${wIndex}][dettagli][${exIndex}][serie]" value="4" required min="1">
                            </td>
                            <td>
                                <input class="input" type="number" name="workouts[${wIndex}][dettagli][${exIndex}][ripetizioni]" value="10" required min="1">
                            </td>
                            <td>
                                <input class="input" type="number" step="0.5" name="workouts[${wIndex}][dettagli][${exIndex}][carico]" value="0" required min="0">
                            </td>
                            <td>
                                <input class="input" type="text" name="workouts[${wIndex}][dettagli][${exIndex}][recupero]" placeholder="Es: 90s">
                            </td>
                            <td>
                                <button type="button" class="button is-danger remove-exercise-btn" title="Rimuovi Esercizio">
                                    <span class="icon"><i class="fas fa-times"></i></span>
                                </button>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', exHtml);
                    
                    // Rimuovi Riga Esercizio
                    tableBody.lastElementChild.querySelector('.remove-exercise-btn').addEventListener('click', (e) => {
                        e.target.closest('tr').remove();
                        reindexExercises(tableBody, wIndex);
                    });
                });

                // Collega rimozione esercizi esistenti
                Array.from(tableBody.querySelectorAll('.remove-exercise-btn')).forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.target.closest('tr').remove();
                        reindexExercises(tableBody, wIndex);
                    });
                });
            }

            function reindexWorkouts() {
                Array.from(container.children).forEach((block, wIndex) => {
                    block.setAttribute('data-workout-index', wIndex);
                    block.querySelector('input[name*="[nome]"]').setAttribute('name', `workouts[${wIndex}][nome]`);
                    block.querySelector('input[name*="[descrizione]"]').setAttribute('name', `workouts[${wIndex}][descrizione]`);
                    
                    const tableBody = block.querySelector('tbody');
                    reindexExercises(tableBody, wIndex);
                });
            }

            function reindexExercises(tableBody, wIndex) {
                Array.from(tableBody.children).forEach((tr, exIndex) => {
                    tr.setAttribute('data-exercise-index', exIndex);
                    tr.querySelector('select').setAttribute('name', `workouts[${wIndex}][dettagli][${exIndex}][esercizio_id]`);
                    tr.querySelector('input[name*="[serie]"]').setAttribute('name', `workouts[${wIndex}][dettagli][${exIndex}][serie]`);
                    tr.querySelector('input[name*="[ripetizioni]"]').setAttribute('name', `workouts[${wIndex}][dettagli][${exIndex}][ripetizioni]`);
                    tr.querySelector('input[name*="[carico]"]').setAttribute('name', `workouts[${wIndex}][dettagli][${exIndex}][carico]`);
                    tr.querySelector('input[name*="[recupero]"]').setAttribute('name', `workouts[${wIndex}][dettagli][${exIndex}][recupero]`);
                });
            }
        });
        {/literal}
    </script>
</body>
</html>
