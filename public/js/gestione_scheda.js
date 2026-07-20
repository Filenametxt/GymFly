document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('workouts-container');    //va a recuperare i workout container
    const addWorkoutBtn = document.getElementById('add-workout-btn');   //stessa cosa con add workout button

    // Helper per istanziare template
    const cloneTemplate = (templateId) => {
        const temp = document.getElementById(templateId);
        return temp.content.cloneNode(true).firstElementChild;       //creare una copia fisica e indipendente di un elemento HTML partendo da un modello
    };

    // 2. Aggiungi Sessione di Allenamento
    addWorkoutBtn.addEventListener('click', () => {
        const wIndex = container.children.length;         //calcolare quanti elementi ci sono all'interno di un contenitore (quello degli allenamenti)
        if (wIndex >= 7) return;

        const letter = String.fromCharCode(65 + wIndex);     //tramite ASCII
        const el = cloneTemplate('tmpl-workout');
        el.innerHTML = el.innerHTML             //prende tutto il codice HTML interno della nuova scheda sotto forma di testo e fa un "Trova e Sostituisci" globale
            .replace(/__WINDEX__/g, wIndex)
            .replace(/__LETTER__/g, letter);
        
        container.appendChild(el);     //prende la scheda appena creata e modificata in memoriae la inserisce fisicamente come ultimo elemento figlio dentro il contenitore principale
        reindexWorkouts();             //l'utente vede apparire la nuova scheda
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
        else if (target.closest('.add-exercise-group-btn')) {     //copia il contenitore dell'esercizio
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
        else if (target.closest('.add-series-btn')) {   //fa la ricerca del tag del button per aggiungere la serie
            const group = target.closest('.exercise-group');       //prende l'esercizio più vicino
            const seriesContainer = group.querySelector('.series-container');       //box della serie
            const selectEx = group.querySelector('.select-esercizio');              //stessa cosa per il box esercizio
            
            let lastReps = "10", lastCarico = "0", lastTempo = "";
            const lastRow = seriesContainer.lastElementChild;             //ultima serie inserita nella lista
            if (lastRow) {                                                               //serve per copiare l'esercizio
                lastReps = lastRow.querySelector('input[data-name="ripetizioni"]').value;
                lastCarico = lastRow.querySelector('input[data-name="carico"]').value;
                const tInput = lastRow.querySelector('input[data-name="tempo"]');
                if (tInput) lastTempo = tInput.value;               //prende il tempo per stamparlo sotto
            }

            const sIndex = seriesContainer.children.length;     //quante serie ci sono
            const el = cloneTemplate('tmpl-series-row');        //aggiunge il template per la serie
            el.innerHTML = el.innerHTML                         //Il codice prende tutto l'HTML della riga appena clonata e sostituisce i codici segnaposto con i dati reali dell'allenamento:
                .replace(/__SINDEX__/g, sIndex + 1)            //numero serie+1    
                .replace(/__EXID__/g, selectEx.value)          //Inserisce l'ID dell'esercizio selezionato nel menu a tendina
                .replace(/__REPS__/g, lastReps)               //compilazione automatica
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

    function aggiornaAbilitazioneCampiGruppo(group) {     //controlla se è per reps o tempo
        const selectEx = group.querySelector('.select-esercizio');
        const opt = selectEx.options[selectEx.selectedIndex];     //recupera nella box dell'esercizio l'index
        const isDurata = opt && opt.getAttribute('data-tipologia') === 'durata';     //verifica se è un esercizio per durata

        group.querySelectorAll('.series-row').forEach(row => {             //righe delle serie
            const inputReps = row.querySelector('input[data-name="ripetizioni"]');
            const inputTempo = row.querySelector('input[data-name="tempo"]');

            inputReps.disabled = isDurata;               //disabilita le reps se è per durata
            inputReps.required = !isDurata;
            if (isDurata) inputReps.value = "";
            else if (!inputReps.value) inputReps.value = "10";

            inputTempo.disabled = !isDurata;
            inputTempo.required = isDurata;
            if (!isDurata) inputTempo.value = "";
        });
    }

    function reindexWorkouts() {
        const workouts = Array.from(container.children);      //prende il container e recupera i workout
        addWorkoutBtn.style.display = workouts.length >= 7 ? 'none' : 'inline-block';     //se ci sono troppi allenamenti non visualizza il bottone

        workouts.forEach((workoutBlock, wIndex) => {    //valore-chiave
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
                    tr.querySelector('input[data-name="serie"]').value = sIndex + 1;        //valore dell'esercizio 
                    tr.querySelector('input[data-name="esercizio_id"]').value = exId;

                    tr.querySelector('input[data-name="serie"]').name = `workouts[${wIndex}][dettagli][${globalExIndex}][serie]`;      //nome dell'esercizio
                    tr.querySelector('input[data-name="ripetizioni"]').name = `workouts[${wIndex}][dettagli][${globalExIndex}][ripetizioni]`;
                    tr.querySelector('input[data-name="carico"]').name = `workouts[${wIndex}][dettagli][${globalExIndex}][carico]`;
                    tr.querySelector('input[data-name="tempo"]').name = `workouts[${wIndex}][dettagli][${globalExIndex}][tempo]`;

                    globalExIndex++;     //la riga successiva (anche se appartiene a un esercizio diverso o a una scheda diversa) riceverà il numero successivo
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
