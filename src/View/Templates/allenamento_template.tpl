<div class="box workout-box mb-4" data-workout-index="__WINDEX__">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 1rem;">
        <h4 class="title is-5 style-theme-text mb-0 workout-title-label">ALLENAMENTO __LETTER__</h4>
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
                    <input class="input" type="text" name="workouts[__WINDEX__][nome]" required placeholder="Es: Allenamento A - Petto/Bicipiti">
                </div>
            </div>
        </div>
        <div class="column is-6">
            <div class="field">
                <label class="label">Note / Descrizione</label>
                <div class="control">
                    <input class="input" type="text" name="workouts[__WINDEX__][descrizione]" placeholder="Es: Sessione del lunedì">
                </div>
            </div>
        </div>
    </div>
    <div class="exercises-container mt-4"></div>
    <div class="has-text-right mt-2">
        <button type="button" class="button is-small is-gymfly add-exercise-group-btn">
            <i class="fas fa-plus mr-1"></i> Aggiungi Esercizio
        </button>
    </div>
</div>
