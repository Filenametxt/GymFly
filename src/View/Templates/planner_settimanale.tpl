<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Planner Settimanale</title>
    <link class="style-sheet" rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .planner-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
            table-layout: fixed;
        }
        .planner-table th {
            text-align: center;
            padding: 10px;
            background: linear-gradient(135deg, var(--periwinkle-2) 0%, var(--gymfly-accent) 100%) !important;
            color: var(--gymfly-text) !important;
            border-radius: 8px;
            border: 1px solid var(--gymfly-primary);
            font-size: 0.9rem;
            font-weight: 700;
        }
        .planner-table th small {
            color: rgba(75, 63, 114, 0.7) !important;
        }
        .planner-table td {
            height: 90px;
            vertical-align: top;
            padding: 6px;
            background-color: var(--gymfly-card-bg);
            border-radius: 8px;
            border: 1px solid rgba(175, 175, 226, 0.4);
            position: relative;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.01);
            transition: background-color 0.2s ease;
        }
        .planner-table td:hover {
            background-color: rgba(153, 205, 234, 0.05);
        }
        .planner-table .hour-col {
            width: 65px;
            vertical-align: middle;
            text-align: center;
            background: linear-gradient(135deg, var(--periwinkle-2) 0%, var(--gymfly-accent) 100%) !important;
            color: var(--gymfly-text) !important;
            font-weight: bold;
            font-size: 0.85rem;
            border: 1px solid var(--gymfly-primary);
            height: auto;
            border-radius: 8px;
        }
        .ap-block {
            background: linear-gradient(135deg, var(--periwinkle-2) 0%, var(--gymfly-accent) 100%);
            color: var(--gymfly-text);
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            margin-bottom: 4px;
            box-shadow: 0 2px 5px rgba(75, 63, 114, 0.08);
            border-left: 3px solid var(--gymfly-primary);
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: block;
        }
        .ap-block:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(75, 63, 114, 0.15);
        }
        .ap-block.is-selected {
            border: 2px solid var(--gymfly-text);
            box-shadow: 0 0 10px rgba(175, 175, 226, 0.5);
        }
        .weekday-checkboxes label {
            display: inline-block;
            margin-right: 8px;
            background: rgba(175, 175, 226, 0.1);
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
            border: 1px solid transparent;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .weekday-checkboxes input[type="checkbox"]:checked + span {
            color: var(--gymfly-secondary);
            font-weight: bold;
        }
        .weekday-checkboxes input[type="checkbox"] {
            margin-right: 4px;
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- ================= DESKTOP HEADER ================= -->
            {assign var="headerClass" value="dashboard-header"}
            {if isset($ruolo_utente)}
                {if $ruolo_utente === 'amministratore'}
                    {assign var="headerClass" value="dashboard-header-admin"}
                {elseif $ruolo_utente === 'allenatore'}
                    {assign var="headerClass" value="dashboard-header-trainer"}
                {/if}
            {elseif isset($smarty.session.ruolo_utente)}
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
                            Weekly Planner
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Programmazione dell'agenda attività e gestione delle attività pianificate
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <span class="icon is-large has-text-white" style="margin-right: 1.5rem;">
                            <i class="fas fa-calendar-alt fa-3x"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px; flex-grow: 1;">PLANNER</strong>
            </div>

            <!-- ACTION TOOLBAR (Nuova Attività) -->
            {if isset($smarty.session.ruolo_utente) && ($smarty.session.ruolo_utente === 'amministratore' || $smarty.session.ruolo_utente === 'allenatore')}
            <div class="mb-5">
                <a href="calendario?nuovo=1" class="button is-gymfly" title="+ Nuova Attività" style="border-radius: 10px;">
                    <span class="icon"><i class="fas fa-plus"></i></span>
                    <span> Nuova Attività</span>
                </a>
            </div>
            {/if}

            <!-- LAYOUT SPLIT PANEL -->
            <div class="columns">
                
                <!-- COLONNA GRID PLANNER -->
                <div class="column {if $selectedAp || $nuovo}is-8{else}is-12{/if}">
                    <div class="table-container">
                        <table class="planner-table">
                            <thead>
                                <tr>
                                    <th class="hour-col">Ora</th>
                                    <th>Lun</th>
                                    <th>Mar</th>
                                    <th>Mer</th>
                                    <th>Gio</th>
                                    <th>Ven</th>
                                    <th>Sab</th>
                                    <th>Dom</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$fasceOrarie item=ora}
                                    <tr>
                                        <!-- Ora di inizio -->
                                        <td class="hour-col">{$ora}:00</td>

                                        <!-- Giorni da 1 (Lunedì) a 7 (Domenica) -->
                                        {for $giorno=1 to 7}
                                            <td>
                                                {if isset($grid[$ora][$giorno])}
                                                    {foreach from=$grid[$ora][$giorno] item=ap}
                                                        <a href="calendario?id_ap={$ap->getId()}" class="ap-block {if $selectedAp && $selectedAp->getId() === $ap->getId()}is-selected{/if}">
                                                            <div class="has-text-weight-bold">{$ap->getAttivita()->getNome()}</div>
                                                            <div class="is-size-7">Sala: {$ap->getSala()->getNome()}</div>
                                                            <div class="is-size-7">PT: {$ap->getAllenatore()->getNome()}</div>
                                                            <div class="is-size-7 is-flex is-justify-content-between">
                                                                <span><i class="fas fa-users mr-1"></i>{$ap->getPrenotati()}/{$ap->getMaxPartecipanti()}</span>
                                                            </div>
                                                        </a>
                                                    {/foreach}
                                                {/if}
                                            </td>
                                        {/for}
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- COLONNA DETTAGLIO / NUOVO INSERIMENTO (SIDEBAR PLANNER) -->
                {if $selectedAp || $nuovo}
                    <div class="column is-4">
                        <div class="card p-5" style="border: 2px solid var(--gymfly-primary); background-color: var(--gymfly-card-bg); height: 100%;">
                            
                            <!-- SE SELEZIONATA ATTIVITÀ ESISTENTE (DETTAGLIO/MODIFICA/ELIMINA) -->
                            {if $selectedAp}
                                <div class="is-flex is-justify-content-between is-align-items-center mb-4">
                                    <h2 class="title is-4 mb-0 style-theme-text">Dettaglio Attività</h2>
                                    <a href="calendario" class="delete is-medium" style="margin-left: auto;" title="Chiudi dettaglio"></a>
                                </div>

                                <div class="box" style="background-color: rgba(175, 175, 226, 0.05); border: 1px solid var(--gymfly-primary);">
                                    <p class="is-size-5 mb-2"><strong>{$selectedAp->getAttivita()->getNome()}</strong></p>
                                    <p class="mb-2"><i class="fas fa-calendar-day mr-2"></i>Data: <strong>{$selectedAp->getGiorno()->format('d/m/Y')}</strong></p>
                                    <p class="mb-2"><i class="fas fa-clock mr-2"></i>Orario: <strong>{$selectedAp->getOrario()}:00</strong></p>
                                    <p class="mb-2"><i class="fas fa-door-open mr-2"></i>Sala: <strong>{$selectedAp->getSala()->getNome()}</strong></p>
                                    <p class="mb-3"><i class="fas fa-user-ninja mr-2"></i>Coach: <strong>{$selectedAp->getAllenatore()->getNome()} {$selectedAp->getAllenatore()->getCognome()}</strong></p>
                                    
                                    <div class="mb-2 is-size-7 is-flex is-justify-content-between">
                                        <span class="mr-2">Prenotati</span>
                                        <span><strong>{$selectedAp->getPrenotati()}</strong> / {$selectedAp->getMaxPartecipanti()}</span>
                                    </div>
                                    <progress class="progress is-link" value="{$selectedAp->getPrenotati()}" max="{$selectedAp->getMaxPartecipanti()}" style="height: 6px;"></progress>
                                </div>

                                <div class="mt-5">
                                    <h3 class="title is-5 style-theme-text mb-3">Lista Iscritti</h3>
                                    {if $selectedAp->getUtenti()|@count === 0}
                                        <p class="is-size-7 has-text-grey mb-4 is-italic">Nessun utente iscritto a questa classe.</p>
                                    {else}
                                        <ul class="mb-4" style="max-height: 180px; overflow-y: auto; background-color: rgba(0,0,0,0.05); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                                            {foreach from=$selectedAp->getUtenti() item=ut}
                                                <li class="mb-2 p-2" style="position: relative; background: rgba(255,255,255,0.05); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); {if $smarty.session.ruolo_utente === 'amministratore' || $smarty.session.id_utente === $ut->getId()}padding-right: 32px !important;{else}padding-right: 12px !important;{/if} padding-left: 12px !important;">
                                                    {if $smarty.session.ruolo_utente === 'amministratore' || $smarty.session.id_utente === $ut->getId()}
                                                        <a href="disdici-prenotazione?id_attivita_pianificata={$selectedAp->getId()}&id_cliente={$ut->getId()}" class="delete is-small" title="Rimuovi iscrizione" style="position: absolute; right: 8px; top: 8px; background-color: #ff3860;"></a>
                                                    {/if}
                                                    <span class="is-size-7 style-theme-text" style="display: block; font-weight: 500;">
                                                        {$ut->getNome()} {$ut->getCognome()}
                                                        {if $smarty.session.id_utente === $ut->getId()} <span class="tag is-info is-light is-small py-0 px-1 ml-1">Tu</span>{/if}
                                                    </span>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    {/if}

                                    <!-- LISTA D'ATTESA -->
                                    <h3 class="title is-5 style-theme-text mb-3 mt-4">
                                        <i class="fas fa-hourglass-half mr-2" style="color: var(--gymfly-primary);"></i>Lista d'Attesa
                                    </h3>
                                    {if $codaAttesa|@count === 0}
                                        <p class="is-size-7 has-text-grey mb-4 is-italic">Nessun utente in lista d'attesa.</p>
                                    {else}
                                        <ul class="mb-4" style="max-height: 180px; overflow-y: auto; background-color: rgba(0,0,0,0.05); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
                                            {foreach from=$codaAttesa item=coda}
                                                <li class="mb-2 p-2" style="position: relative; background: rgba(255,255,255,0.03); border-radius: 6px; border: 1px dashed rgba(255,255,255,0.1); {if $smarty.session.ruolo_utente === 'amministratore' || $smarty.session.id_utente === $coda->getCliente()->getId()}padding-right: 32px !important;{else}padding-right: 12px !important;{/if} padding-left: 12px !important;">
                                                    {if $smarty.session.ruolo_utente === 'amministratore' || $smarty.session.id_utente === $coda->getCliente()->getId()}
                                                        <a href="disdici-prenotazione?id_attivita_pianificata={$selectedAp->getId()}&id_cliente={$coda->getCliente()->getId()}" class="delete is-small" title="Rimuovi dalla lista d'attesa" style="position: absolute; right: 8px; top: 8px; background-color: #ff3860;"></a>
                                                    {/if}
                                                    <div style="line-height: 1.2;">
                                                        <span class="is-size-7 style-theme-text" style="display: block; font-weight: 500;">
                                                            {$coda->getCliente()->getNome()} {$coda->getCliente()->getCognome()}
                                                            {if $smarty.session.id_utente === $coda->getCliente()->getId()} <span class="tag is-info is-light is-small py-0 px-1 ml-1">Tu</span>{/if}
                                                        </span>
                                                        <span class="is-size-7 has-text-grey" style="font-size: 0.65rem !important;">Inserito il {$coda->getDataInserimento()->format('d/m/Y H:i')}</span>
                                                    </div>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    {/if}

                                    <!-- ISCRIVI NUOVO CLIENTE DA PARTE DELL'ADMIN -->
                                    <div class="box p-3 mb-4" style="background-color: rgba(255,255,255,0.02);">
                                        <form action="prenota-attivita" method="POST">
                                            <input type="hidden" name="id_attivita_pianificata" value="{$selectedAp->getId()}">
                                            <div class="field">
                                                <label class="label is-small style-theme-text">Aggiungi Cliente</label>
                                                <div class="field has-addons">
                                                    <div class="control is-expanded">
                                                        <div class="select is-small is-fullwidth">
                                                            <select name="id_cliente" required>
                                                                <option value="">Seleziona cliente...</option>
                                                                {foreach from=$clienti item=cl}
                                                                    <option value="{$cl->getId()}">{$cl->getNome()} {$cl->getCognome()}</option>
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="control">
                                                        <button type="submit" class="button is-gymfly is-small">Aggiungi</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- RIMOZIONE EVENTO -->
                                    <a href="rimuovi-attivita-pianificata?id_attivita_pianificata={$selectedAp->getId()}" class="button is-danger is-light is-fullwidth mt-4" onclick="return confirm('Sei sicuro di voler rimuovere questa attività pianificata dal calendario? Tutti gli iscritti saranno rimossi.');">
                                        <span class="icon"><i class="fas fa-trash-alt"></i></span>
                                        <span>Elimina Attività Pianificata</span>
                                    </a>
                                </div>

                            <!-- SE MODALITÀ NUOVO INSERIMENTO (CREA ATTIVITÀ PIANIFICATA) -->
                            {elseif $nuovo}
                                <div class="is-flex is-justify-content-between is-align-items-center mb-4">
                                    <h2 class="title is-4 mb-0 style-theme-text">Pianifica Attività</h2>
                                    <a href="calendario" class="delete is-medium" style="margin-left: auto;" title="Chiudi inserimento"></a>
                                </div>

                                <form action="crea-attivita-pianificata" method="POST">
                                    
                                    <!-- NOME ATTIVITÀ -->
                                    <div class="field mb-3">
                                        <label class="label is-small style-theme-text">Attività da pianificare *</label>
                                        <div class="control">
                                            <div class="select is-fullwidth is-small">
                                                <select name="id_attivita" id="select-corso" onchange="toggleNuovoCorsoForm(this.value)">
                                                    <option value="">-- Seleziona attività esistente --</option>
                                                    {foreach from=$attivita item=att}
                                                        <option value="{$att->getId()}">{$att->getNome()} (max: {$att->getMaxPartecipanti()})</option>
                                                    {/foreach}
                                                    <option value="0">+ Registra nuova attività nel catalogo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- MINI FORM PER NUOVA ATTIVITÀ NEL CATALOGO (MOSTRATO SOLO SE SELEZIONATO '0') -->
                                    <div id="nuovo-corso-box" class="box p-3 mb-3" style="display: none; background-color: rgba(255,255,255,0.02); border: 1px dashed var(--gymfly-primary);">
                                        <h4 class="title is-6 mb-2 style-theme-text">Dettagli Nuova Attività</h4>
                                        <div class="field mb-2">
                                            <input class="input is-small" type="text" name="nuova_attivita_nome" placeholder="Nome Attività (es: Spinning)">
                                        </div>
                                        <div class="field mb-2">
                                            <input class="input is-small" type="text" name="nuova_attivita_desc" placeholder="Descrizione dell'attività">
                                        </div>
                                        <div class="field">
                                            <input class="input is-small" type="number" name="nuova_attivita_max" placeholder="Posti Max Consentiti (es: 15)">
                                        </div>
                                    </div>

                                    <!-- DATA DI INIZIO -->
                                    <div class="field mb-3">
                                        <label class="label is-small style-theme-text">Data Inizio *</label>
                                        <div class="control">
                                            <input class="input is-small" type="date" name="data" required min="{$smarty.now|date_format:'%Y-%m-%d'}">
                                        </div>
                                    </div>

                                    <!-- ORARIO (FASCIA ORARIA) -->
                                    <div class="field mb-3">
                                        <label class="label is-small style-theme-text">Ora Inizio (Ora Fine = Ora + 1) *</label>
                                        <div class="control">
                                            <div class="select is-fullwidth is-small">
                                                <select name="orario" required>
                                                    {foreach from=$fasceOrarie item=ora}
                                                        <option value="{$ora}">{$ora}:00</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SALA -->
                                    <div class="field mb-3">
                                         <label class="label is-small style-theme-text">Sala *</label>
                                         <div class="control">
                                             <div class="select is-fullwidth is-small">
                                                 <select name="id_sala" id="select-sala" onchange="toggleNuovaSalaForm(this.value)" required>
                                                     <option value="">Seleziona sala...</option>
                                                     {foreach from=$sale item=sa}
                                                         <option value="{$sa->getId()}">{$sa->getNome()} (max: {$sa->getMaxPartecipanti()})</option>
                                                     {/foreach}
                                                     <option value="0">+ Registra nuova sala nella palestra</option>
                                                 </select>
                                             </div>
                                         </div>
                                     </div>

                                     <!-- MINI FORM PER NUOVA SALA (MOSTRATO SOLO SE SELEZIONATO '0') -->
                                     <div id="nuova-sala-box" class="box p-3 mb-3" style="display: none; background-color: rgba(255,255,255,0.02); border: 1px dashed var(--gymfly-primary);">
                                         <h4 class="title is-6 mb-2 style-theme-text">Dettagli Nuova Sala</h4>
                                         <div class="field mb-2">
                                             <input class="input is-small" type="text" name="nuova_sala_nome" placeholder="Nome Sala (es: Sala C)">
                                         </div>
                                         <div class="field">
                                             <input class="input is-small" type="number" name="nuova_sala_max" placeholder="Posti Max Sala (es: 20)">
                                         </div>
                                     </div>

                                    <!-- ALLENATORE (ADD PT) -->
                                    <div class="field mb-3">
                                        <label class="label is-small style-theme-text">Allenatore / PT *</label>
                                        <div class="control">
                                            <div class="select is-fullwidth is-small">
                                                <select name="id_allenatore" required>
                                                    <option value="">Seleziona PT...</option>
                                                    {foreach from=$allenatori item=pt}
                                                        <option value="{$pt->getId()}">{$pt->getNome()} {$pt->getCognome()}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- RIPETIZIONI SETTIMANALI -->
                                    <div class="field mb-4">
                                        <label class="label is-small style-theme-text">Ripeti settimanalmente nei giorni:</label>
                                        <div class="weekday-checkboxes">
                                            <label><input type="checkbox" name="ripetizione[]" value="L"><span>L</span></label>
                                            <label><input type="checkbox" name="ripetizione[]" value="M"><span>M</span></label>
                                            <label><input type="checkbox" name="ripetizione[]" value="M"><span>M</span></label>
                                            <label><input type="checkbox" name="ripetizione[]" value="G"><span>G</span></label>
                                            <label><input type="checkbox" name="ripetizione[]" value="V"><span>V</span></label>
                                            <label><input type="checkbox" name="ripetizione[]" value="S"><span>S</span></label>
                                            <label><input type="checkbox" name="ripetizione[]" value="D"><span>D</span></label>
                                        </div>
                                        <p class="help has-text-grey-light">Se selezionate delle ripetizioni, l'evento verrà pianificato per le prossime 4 settimane nei giorni selezionati.</p>
                                    </div>

                                    <!-- ACTIONS GROUP -->
                                    <div class="field is-grouped is-grouped-right mt-5">
                                        <p class="control">
                                            <a href="calendario" class="button is-light">Chiudi</a>
                                        </p>
                                        <p class="control">
                                            <button type="submit" class="button is-gymfly">
                                                <span class="icon"><i class="fas fa-save"></i></span>
                                                <span>Pianifica Attività</span>
                                            </button>
                                        </p>
                                    </div>

                                </form>
                            {/if}

                        </div>
                    </div>
                {/if}

            </div>

        </main>
    </div>

    <script>
        function toggleNuovoCorsoForm(val) {
            var box = document.getElementById('nuovo-corso-box');
            if (val === '0') {
                box.style.display = 'block';
                box.querySelectorAll('input').forEach(input => input.setAttribute('required', 'true'));
            } else {
                box.style.display = 'none';
                box.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
            }
        }

        function toggleNuovaSalaForm(val) {
            var box = document.getElementById('nuova-sala-box');
            if (val === '0') {
                box.style.display = 'block';
                box.querySelectorAll('input').forEach(input => input.setAttribute('required', 'true'));
            } else {
                box.style.display = 'none';
                box.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
            }
        }
    </script>

</body>
</html>
