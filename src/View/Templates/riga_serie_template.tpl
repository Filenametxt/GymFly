<tr class="series-row">
    <td class="has-text-centered is-vcentered">
        <span class="series-number-label">__SINDEX__</span>
        <input type="hidden" data-name="serie" value="__SINDEX__">
        <input type="hidden" data-name="esercizio_id" value="__EXID__">
    </td>
    <td><input class="input" type="number" data-name="ripetizioni" value="__REPS__" required min="1"></td>
    <td><input class="input" type="number" step="0.5" data-name="carico" value="__CARICO__" required min="0"></td>
    <td><input class="input" type="text" data-name="tempo" placeholder="Es: 90s" value="__TEMPO__"></td>
    <td>
        <button type="button" class="button is-small is-danger remove-series-btn" title="Rimuovi Serie">
            <span class="icon is-small"><i class="fas fa-times"></i></span>
        </button>
    </td>
</tr>
