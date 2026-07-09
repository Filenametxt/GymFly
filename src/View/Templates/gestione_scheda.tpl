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
    <style>
        .exercise-box {
            border: 2px solid var(--gymfly-primary);
            border-radius: 12px;
            padding: 1.25rem;
            background-color: var(--gymfly-card-bg);
            margin-bottom: 1.5rem;
            position: relative;
        }
        .series-table th {
            text-align: center;
            font-size: 0.85rem;
            color: var(--gymfly-primary);
            font-weight: bold;
        }
        .series-table td {
            vertical-align: middle;
            text-align: center;
        }
        .series-label {
            font-weight: bold;
            color: var(--gymfly-primary);
            font-size: 0.9rem;
        }
    </style>
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
                        <!-- HEADER CON TITOLO E TASTO ELIMINA -->
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

                            <!-- BOX METADATI CON NOME COGNOME ATLETA E INPUT -->
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

                                <div id="workouts-container">
                                    <!-- Generato dinamicamente via JS per garantire coerenza totale -->
                                </div>

                                <!-- Pulsante Aggiungi Allenamento spostato in fondo alla lista degli allenamenti -->
                                <div class="has-text-right mt-4" style="border-top: 2px dashed var(--gymfly-accent); padding-top: 1.5rem;">
                                    <button type="button" class="button is-gymfly" id="add-workout-btn" style="border-radius: 8px;">
                                        <i class="fas fa-plus mr-2"></i> Aggiungi Allenamento (A, B, C...)
                                    </button>
                                </div>
                            </div>

                            <!-- ACTION BUTTONS -->
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
        
        const existingWorkouts = [
            {foreach $scheda->getAllenamenti() as $allenamento}
            {
                id: "{$allenamento->getId()}",
                nome: "{$allenamento->getNome()|escape:'javascript'}",
                descrizione: "{$allenamento->getDescrizione()|pulisci_descrizione|escape:'javascript'}",
                dettagli: [
                    {foreach $allenamento->getDettagli() as $dettaglio}
                    {
                        id: "{$dettaglio->getId()}",
                        esercizio_id: "{$dettaglio->getEsercizio()->getId()}",
                        esercizio_nome: "{$dettaglio->getEsercizio()->getNomeEsercizio()|escape:'javascript'}",
                        serie: "{$dettaglio->getSerie()}",
                        ripetizioni: "{$dettaglio->getRipetizioni()}",
                        carico: "{$dettaglio->getCarico()}",
                        recupero: "{$allenamento->getDescrizione()|estrai_recupero:$dettaglio->getEsercizio()->getNomeEsercizio():$dettaglio->getSerie():$dettaglio->getId()|escape:'javascript'}"
                    },
                    {/foreach}
                ]
            },
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
                if (form.reportValidity()) {
                    actionField.value = 'invia';
                    form.submit();
                }
            });

            // 3. Funzione di sincronizzazione dei nomi e degli indici delle input per Doctrine
            function updateIndices() {
                const workoutBoxes = container.querySelectorAll('.workout-box');
                
                // Nascondi il tasto aggiungi allenamento se si raggiungono i 7 allenamenti
                const addWorkoutBtn = document.getElementById('add-workout-btn');
                if (addWorkoutBtn) {
                    if (workoutBoxes.length >= 7) {
                        addWorkoutBtn.style.display = 'none';
                    } else {
                        addWorkoutBtn.style.display = 'inline-flex';
                    }
                }

                workoutBoxes.forEach((wBox, wIndex) => {
                    const letter = String.fromCharCode(65 + wIndex);
                    const letterTitle = wBox.querySelector('.workout-letter-title');
                    if (letterTitle) {
                        letterTitle.textContent = `ALLENAMENTO ${letter}`;
                    }

                    // Aggiorna name dei campi workout
                    wBox.querySelector('.input-workout-nome').setAttribute('name', `workouts[${wIndex}][nome]`);
                    wBox.querySelector('.input-workout-descrizione').setAttribute('name', `workouts[${wIndex}][descrizione]`);
                    
                    const exBoxes = wBox.querySelectorAll('.exercise-box');
                    let dIndex = 0;
                    
                    exBoxes.forEach((exBox, exIdx) => {
                        const selectEx = exBox.querySelector('.select-esercizio');
                        if (selectEx && selectEx.options.length > 0) {
                            selectEx.options[0].textContent = `Esercizio ${exIdx + 1}`;
                        }
                        const exId = selectEx.value;
                        
                        // Sincronizza i dati di ogni riga di serie
                        const rows = exBox.querySelectorAll('.series-row');
                        rows.forEach((row, rIndex) => {
                            const sNum = rIndex + 1;
                            row.querySelector('.series-label').textContent = `Serie ${sNum}`;
                            
                            // Aggiorna name per Doctrine
                            row.querySelector('.input-esercizio-id').setAttribute('name', `workouts[${wIndex}][dettagli][${dIndex}][esercizio_id]`);
                            row.querySelector('.input-esercizio-id').value = exId;
                            
                            row.querySelector('.input-serie-num').setAttribute('name', `workouts[${wIndex}][dettagli][${dIndex}][serie]`);
                            row.querySelector('.input-serie-num').value = sNum;
                            
                            row.querySelector('.input-carico').setAttribute('name', `workouts[${wIndex}][dettagli][${dIndex}][carico]`);
                            row.querySelector('.input-ripetizioni').setAttribute('name', `workouts[${wIndex}][dettagli][${dIndex}][ripetizioni]`);
                            row.querySelector('.input-recupero').setAttribute('name', `workouts[${wIndex}][dettagli][${dIndex}][recupero]`);
                            
                            dIndex++;
                        });
                    });
                });
            }

            // 4. Aggiunge un Allenamento (Sessione)
            function addWorkout(wData = null) {
                if (container.children.length >= 7) {
                    alert("Non puoi inserire più di 7 allenamenti.");
                    return null;
                }
                const wIndex = container.children.length;
                const workoutDiv = document.createElement('div');
                workoutDiv.className = 'box workout-box mb-5';
                workoutDiv.style.border = '2px solid var(--gymfly-accent)';
                workoutDiv.style.borderRadius = '16px';
                
                const nomeVal = wData ? wData.nome : '';
                const descVal = wData ? wData.descrizione : '';
                
                workoutDiv.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--gymfly-accent); padding-bottom: 0.5rem; margin-bottom: 1.25rem;">
                        <h4 class="title is-5 style-theme-text mb-0 workout-letter-title" style="font-weight: 800; letter-spacing: 0.5px;">ALLENAMENTO A</h4>
                        <button type="button" class="button is-danger remove-workout-btn" title="Rimuovi Allenamento">
                            <span class="icon"><i class="fas fa-times"></i></span>
                        </button>
                    </div>
                    <div class="columns is-vcentered mb-4">
                        <div class="column is-6">
                            <div class="field">
                                <label class="label">Nome Allenamento (Sessione)</label>
                                <div class="control">
                                    <input class="input input-workout-nome" type="text" value="${nomeVal}" required placeholder="Es: Petto/Bicipiti">
                                </div>
                            </div>
                        </div>
                        <div class="column is-6">
                            <div class="field">
                                <label class="label">Note / Descrizione</label>
                                <div class="control">
                                    <input class="input input-workout-descrizione" type="text" value="${descVal}" placeholder="inserisci informazioni">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="exercises-list-container">
                        <!-- Qui finiranno gli esercizi dell'allenamento -->
                    </div>

                    <div class="has-text-right mt-3">
                        <button type="button" class="button is-small is-gymfly add-exercise-btn" style="border-radius: 8px;">
                            <i class="fas fa-plus mr-1"></i> NUOVO
                        </button>
                    </div>
                `;

                // Rimozione allenamento senza conferma
                workoutDiv.querySelector('.remove-workout-btn').addEventListener('click', () => {
                    workoutDiv.remove();
                    updateIndices();
                });

                // Aggiunta esercizio
                workoutDiv.querySelector('.add-exercise-btn').addEventListener('click', () => {
                    addExercise(workoutDiv);
                });

                container.appendChild(workoutDiv);
                updateIndices();
                return workoutDiv;
            }

            // 5. Aggiunge un blocco Esercizio
            function addExercise(wBox, exData = null) {
                const exListContainer = wBox.querySelector('.exercises-list-container');
                const exBox = document.createElement('div');
                exBox.className = 'exercise-box';

                // Genera le opzioni della select
                const exIdx = exListContainer.children.length + 1;
                let optionsHtml = `<option value="">Esercizio ${exIdx}</option>`;
                eserciziDisponibili.forEach(ex => {
                    const selected = (exData && exData.esercizio_id == ex.id) ? 'selected' : '';
                    optionsHtml += `<option value="${ex.id}" ${selected}>${ex.nome}</option>`;
                });

                exBox.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center;" class="mb-3">
                        <div class="select is-fullwidth" style="max-width: 80%;">
                            <select class="select-esercizio" required>
                                ${optionsHtml}
                            </select>
                        </div>
                        <button type="button" class="button is-danger remove-exercise-btn" title="Rimuovi Esercizio">
                            <span class="icon"><i class="fas fa-times"></i></span>
                        </button>
                    </div>

                    <table class="table is-fullwidth is-striped series-table mb-2">
                        <thead>
                            <tr>
                                <th style="width: 90px; text-align: left;">Serie</th>
                                <th>Ripetizioni</th>
                                <th>Carico (Kg)</th>
                                <th>Recupero</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody class="series-rows-container">
                            <!-- Qui finiranno le righe delle serie -->
                        </tbody>
                    </table>

                    <div class="has-text-right">
                        <button type="button" class="button is-small is-gymfly add-series-btn" style="border-radius: 8px;">
                            <i class="fas fa-plus mr-1"></i> NUOVA
                        </button>
                    </div>
                `;

                // Sincronizza l'id esercizio quando cambia
                exBox.querySelector('.select-esercizio').addEventListener('change', updateIndices);

                // Rimuovi Esercizio
                exBox.querySelector('.remove-exercise-btn').addEventListener('click', () => {
                    exBox.remove();
                    updateIndices();
                });

                // Aggiungi Serie
                exBox.querySelector('.add-series-btn').addEventListener('click', () => {
                    addSeriesRow(exBox);
                });

                exListContainer.appendChild(exBox);

                // Renderizza le serie esistenti o ne aggiunge una di default
                if (exData && exData.series && exData.series.length > 0) {
                    exData.series.forEach(sData => {
                        addSeriesRow(exBox, sData);
                    });
                } else {
                    addSeriesRow(exBox); // Inizializza con almeno una riga
                }

                updateIndices();
            }

            // 6. Aggiunge una riga di serie (S1, S2...) all'interno della tabella dell'esercizio
            function addSeriesRow(exBox, sData = null) {
                const tbody = exBox.querySelector('.series-rows-container');
                const row = document.createElement('tr');
                row.className = 'series-row';

                const pesoVal = sData ? sData.carico : '0';
                const repVal = sData ? sData.ripetizioni : '10';
                const recVal = sData ? sData.recupero : '';

                row.innerHTML = `
                    <td style="text-align: left; white-space: nowrap;">
                        <span class="series-label">Serie 1</span>
                        <input type="hidden" class="input-esercizio-id" value="">
                        <input type="hidden" class="input-serie-num" value="1">
                    </td>
                    <td>
                        <input class="input is-small input-ripetizioni" type="number" value="${repVal}" required min="1" style="text-align: center;">
                    </td>
                    <td>
                        <input class="input is-small input-carico" type="number" step="0.5" value="${pesoVal}" required min="0" style="text-align: center;">
                    </td>
                    <td>
                        <input class="input is-small input-recupero" type="text" value="${recVal}" placeholder="Es: 60s" style="text-align: center;">
                    </td>
                    <td>
                        <button type="button" class="button is-danger remove-series-btn" title="Rimuovi Serie">
                            <span class="icon"><i class="fas fa-times"></i></span>
                        </button>
                    </td>
                `;

                // Rimuovi Serie
                row.querySelector('.remove-series-btn').addEventListener('click', () => {
                    const activeRows = tbody.querySelectorAll('.series-row');
                    if (activeRows.length > 1) {
                        row.remove();
                        updateIndices();
                    } else {
                        alert('Un esercizio deve avere almeno una serie attiva.');
                    }
                });

                tbody.appendChild(row);
                updateIndices();
            }

            // 7. Renderizza i dati esistenti all'avvio
            if (existingWorkouts && existingWorkouts.length > 0) {
                existingWorkouts.forEach(wData => {
                    const wBox = addWorkout(wData);
                    
                    // Raggruppa i dettagli dell'allenamento preservando blocchi separati dello stesso esercizio
                    const blocks = [];
                    let currentBlock = null;

                    wData.dettagli.forEach(d => {
                        if (!currentBlock || 
                            currentBlock.esercizio_id !== d.esercizio_id || 
                            parseInt(d.serie) <= parseInt(currentBlock.series[currentBlock.series.length - 1].serie)) {
                            
                            currentBlock = {
                                esercizio_id: d.esercizio_id,
                                esercizio_nome: d.esercizio_nome,
                                series: []
                            };
                            blocks.push(currentBlock);
                        }
                        currentBlock.series.push(d);
                    });

                    // Aggiunge ogni blocco di esercizio con le sue serie
                    blocks.forEach(exData => {
                        addExercise(wBox, exData);
                    });
                });
            } else {
                // Se scheda nuova o vuota, inizializza con un allenamento vuoto
                addWorkout();
            }

            // Gestione dei bottoni di aggiunta allenamento dall'intestazione
            addWorkoutBtn.addEventListener('click', () => {
                addWorkout();
            });

        });
        {/literal}
    </script>
</body>
</html>
