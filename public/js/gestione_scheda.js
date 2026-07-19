document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('workouts-container');
    const addWorkoutBtn = document.getElementById('add-workout-btn');

    // 1. Gestione cambio cliente dal menu a tendina
    const selectCliente = document.getElementById('select-cambia-cliente');
    if (selectCliente) {
        selectCliente.addEventListener('change', () => {
            const option = selectCliente.options[selectCliente.selectedIndex];
            const targetSchedaId = parseInt(option.getAttribute('data-scheda-id') || '0');
            if (targetSchedaId > 0) {
                window.location.href = `modifica-scheda?id=${targetSchedaId}&azione_rapida=1`;
            } else {
                window.location.href = `crea-scheda?cf=${selectCliente.value}&azione_rapida=1`;
            }
        });
    }

    // Helper per istanziare template
    const cloneTemplate = (templateId) => {
        const temp = document.getElementById(templateId);
        return temp.content.cloneNode(true).firstElementChild;
    };

    // 2. Aggiungi Sessione di Allenamento
    addWorkoutBtn.addEventListener('click', () => {
        const wIndex = container.children.length;
        if (wIndex >= 7) return;

        const letter = String.fromCharCode(65 + wIndex);
        const el = cloneTemplate('tmpl-workout');
        el.innerHTML = el.innerHTML
            .replace(/__WINDEX__/g, wIndex)
            .replace(/__LETTER__/g, letter);
        
        container.appendChild(el);
        reindexWorkouts();
    });

    // 3. Delegazione degli Eventi sul contenitore principale degli allenamenti
    container.addEventListener('click', (e) => {
        const target = e.target;
        
        // Rimuovi Allenamento
        if (target.closest('.remove-workout-btn')) {
            target.closest('.workout-box').remove();
            reindexWorkouts();
        }

        // Aggiungi Esercizio (nuovo gruppo)
        else if (target.closest('.add-exercise-group-btn')) {
            const workoutBox = target.closest('.workout-box');
            const exercisesContainer = workoutBox.querySelector('.exercises-container');
            const el = cloneTemplate('tmpl-exercise-group');
            exercisesContainer.appendChild(el);
            aggiornaAbilitazioneCampiGruppo(el);
            reindexWorkouts();
        }

        // Rimuovi Esercizio (tutto il gruppo)
        else if (target.closest('.remove-exercise-group-btn')) {
            target.closest('.exercise-group').remove();
            reindexWorkouts();
        }

        // Aggiungi Serie
        else if (target.closest('.add-series-btn')) {
            const group = target.closest('.exercise-group');
            const seriesContainer = group.querySelector('.series-container');
            const selectEx = group.querySelector('.select-esercizio');
            
            let lastReps = "10", lastCarico = "0", lastTempo = "";
            const lastRow = seriesContainer.lastElementChild;
            if (lastRow) {
                lastReps = lastRow.querySelector('input[data-name="ripetizioni"]').value;
                lastCarico = lastRow.querySelector('input[data-name="carico"]').value;
                const tInput = lastRow.querySelector('input[data-name="tempo"]');
                if (tInput) lastTempo = tInput.value;
            }

            const sIndex = seriesContainer.children.length;
            const el = cloneTemplate('tmpl-series-row');
            el.innerHTML = el.innerHTML
                .replace(/__SINDEX__/g, sIndex + 1)
                .replace(/__EXID__/g, selectEx.value)
                .replace(/__REPS__/g, lastReps)
                .replace(/__CARICO__/g, lastCarico)
                .replace(/__TEMPO__/g, lastTempo);

            seriesContainer.appendChild(el);
            aggiornaAbilitazioneCampiGruppo(group);
            reindexWorkouts();
        }

        // Rimuovi Singola Serie
        else if (target.closest('.remove-series-btn')) {
            target.closest('.series-row').remove();
            reindexWorkouts();
        }
    });

    // Monitora il cambiamento della select dell'esercizio tramite delegazione
    container.addEventListener('change', (e) => {
        if (e.target.classList.contains('select-esercizio')) {
            const group = e.target.closest('.exercise-group');
            aggiornaAbilitazioneCampiGruppo(group);
            reindexWorkouts();
        }
    });

    function aggiornaAbilitazioneCampiGruppo(group) {
        const selectEx = group.querySelector('.select-esercizio');
        const opt = selectEx.options[selectEx.selectedIndex];
        const isDurata = opt && opt.getAttribute('data-tipologia') === 'durata';

        group.querySelectorAll('.series-row').forEach(row => {
            const inputReps = row.querySelector('input[data-name="ripetizioni"]');
            const inputTempo = row.querySelector('input[data-name="tempo"]');

            inputReps.disabled = isDurata;
            inputReps.required = !isDurata;
            if (isDurata) inputReps.value = "";
            else if (!inputReps.value) inputReps.value = "10";

            inputTempo.disabled = !isDurata;
            inputTempo.required = isDurata;
            if (!isDurata) inputTempo.value = "";
        });
    }

    function reindexWorkouts() {
        const workouts = Array.from(container.children);
        addWorkoutBtn.style.display = workouts.length >= 7 ? 'none' : 'inline-block';

        workouts.forEach((workoutBlock, wIndex) => {
            workoutBlock.setAttribute('data-workout-index', wIndex);
            workoutBlock.querySelector('.workout-title-label').textContent = 'ALLENAMENTO ' + String.fromCharCode(65 + wIndex);
            workoutBlock.querySelector('input[name*="[nome]"]').name = `workouts[${wIndex}][nome]`;
            workoutBlock.querySelector('input[name*="[descrizione]"]').name = `workouts[${wIndex}][descrizione]`;

            let globalExIndex = 0;
            workoutBlock.querySelectorAll('.exercise-group').forEach(group => {
                const selectEx = group.querySelector('.select-esercizio');
                const exId = selectEx.value;

                group.querySelectorAll('.series-row').forEach((tr, sIndex) => {
                    tr.querySelector('.series-number-label').textContent = sIndex + 1;
                    tr.querySelector('input[data-name="serie"]').value = sIndex + 1;
                    tr.querySelector('input[data-name="esercizio_id"]').value = exId;

                    tr.querySelector('input[data-name="esercizio_id"]').name = `workouts[${wIndex}][dettagli][${globalExIndex}][esercizio_id]`;
                    tr.querySelector('input[data-name="serie"]').name = `workouts[${wIndex}][dettagli][${globalExIndex}][serie]`;
                    tr.querySelector('input[data-name="ripetizioni"]').name = `workouts[${wIndex}][dettagli][${globalExIndex}][ripetizioni]`;
                    tr.querySelector('input[data-name="carico"]').name = `workouts[${wIndex}][dettagli][${globalExIndex}][carico]`;
                    tr.querySelector('input[data-name="tempo"]').name = `workouts[${wIndex}][dettagli][${globalExIndex}][tempo]`;

                    globalExIndex++;
                });
            });
        });
    }

    // Inizializzazione iniziale per schede precompilate
    document.querySelectorAll('.exercise-group').forEach(group => {
        aggiornaAbilitazioneCampiGruppo(group);
    });
    reindexWorkouts();
});
