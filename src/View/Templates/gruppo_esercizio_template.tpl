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
                                <option value="{$ex->getId()}" data-tipologia="{$ex->getTipologia()->getNomeTipologia()|lower}">{$ex->getNomeEsercizio()}</option>
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
            <tr class="series-row">
                <td class="has-text-centered is-vcentered">
                    <span class="series-number-label">1</span>
                    <input type="hidden" data-name="serie" value="1">
                    <input type="hidden" data-name="esercizio_id" value="">
                </td>
                <td><input class="input" type="number" data-name="ripetizioni" value="10" required min="1"></td>
                <td><input class="input" type="number" step="0.5" data-name="carico" value="0" required min="0"></td>
                <td><input class="input" type="text" data-name="tempo" placeholder="Es: 90s" value=""></td>
                <td>
                    <button type="button" class="button is-small is-danger remove-series-btn" title="Rimuovi Serie">
                        <span class="icon is-small"><i class="fas fa-times"></i></span>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="has-text-right">
        <button type="button" class="button is-small is-gymfly add-series-btn">
            <i class="fas fa-plus mr-1"></i> Aggiungi Serie
        </button>
    </div>
</div>
