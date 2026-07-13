<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Calendario Corsi</title>
    <link rel="stylesheet" href="css/bulma.min.css">
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
        .sp-block {
            background: linear-gradient(135deg, #ffe0cc 0%, #ffd0b0 100%);
            color: #8a3c00;
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            margin-bottom: 4px;
            box-shadow: 0 2px 5px rgba(138, 60, 0, 0.08);
            border-left: 3px solid #e65100;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: block;
        }
        .sp-block:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(138, 60, 0, 0.15);
        }
        .sp-block.is-selected {
            border: 2px solid #8a3c00;
            box-shadow: 0 0 10px rgba(230, 81, 0, 0.3);
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
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
                            Calendario Corsi
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Esplora la programmazione settimanale, iscriviti ai corsi o gestisci le tue sessioni private
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
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px; flex-grow: 1;">CALENDARIO</strong>
            </div>

            <!-- ACTION & NAVIGATION TOOLBAR -->
            <div class="is-flex is-justify-content-between is-align-items-center mb-5 is-flex-wrap-wrap" style="gap: 12px;">
                <!-- Bottone azione (solo allenatore) -->
                <div>
                    {if $ruolo_utente === 'allenatore'}
                        <a href="calendario?nuova_sessione=1&data={$dataCorrente}" class="button is-gymfly" title="Pianifica Sessione Privata" style="border-radius: 10px;">
                            <span class="icon"><i class="fas fa-calendar-plus"></i></span>
                            <span>Pianifica Sessione Privata</span>
                        </a>
                    {/if}
                </div>

                <!-- Navigazione Settimana -->
                <div class="is-flex is-align-items-center calendar-nav-container" style="gap: 15px; margin-left: auto;">
                    <span class="is-size-4 has-text-weight-bold style-theme-text mr-2" style="letter-spacing: 0.5px;">
                        {$meseAnno}
                    </span>
                    
                    <div class="buttons has-addons mb-0">
                        <a href="calendario?data={$dataPrecedente}" class="button is-light" title="Settimana Precedente">
                            <span class="icon"><i class="fas fa-chevron-left"></i></span>
                        </a>
                        <a href="calendario" class="button is-light font-weight-bold" title="Settimana Corrente">
                            Oggi
                        </a>
                        <a href="calendario?data={$dataSuccessiva}" class="button is-light" title="Settimana Successiva">
                            <span class="icon"><i class="fas fa-chevron-right"></i></span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- CONTROLLO REQUISITI CLIENTE -->
            {if $ruolo_utente === 'cliente'}
                {if isset($puoPrenotare) && !$puoPrenotare}
                    <div class="notification is-danger is-light mb-5">
                        <span class="icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <strong>Attenzione:</strong>
                        Non puoi prenotare corsi. Assicurati che l'iscrizione annuale sia valida, che l'abbonamento sia attivo e che il certificato medico non sia scaduto.
                    </div>
                {/if}
            {/if}

            <!-- LAYOUT SPLIT PANEL -->
            <div class="columns">
                
                <!-- COLONNA GRID CALENDARIO -->
                <div class="column {if $selectedAp || $selectedSp || $nuova_sessione}is-8{else}is-12{/if}">
                    <div class="table-container">
                        <table class="planner-table">
                            <thead>
                                <tr>
                                    <th class="hour-col">Ora</th>
                                    <th>Lun <br><small class="has-text-grey">{$giorniSettimana[0]->format('d/m')}</small></th>
                                    <th>Mar <br><small class="has-text-grey">{$giorniSettimana[1]->format('d/m')}</small></th>
                                    <th>Mer <br><small class="has-text-grey">{$giorniSettimana[2]->format('d/m')}</small></th>
                                    <th>Gio <br><small class="has-text-grey">{$giorniSettimana[3]->format('d/m')}</small></th>
                                    <th>Ven <br><small class="has-text-grey">{$giorniSettimana[4]->format('d/m')}</small></th>
                                    <th>Sab <br><small class="has-text-grey">{$giorniSettimana[5]->format('d/m')}</small></th>
                                    <th>Dom <br><small class="has-text-grey">{$giorniSettimana[6]->format('d/m')}</small></th>
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
                                                    {foreach from=$grid[$ora][$giorno] item=item}
                                                        {if $item instanceof \App\Entity\SessionePrivata}
                                                            <!-- Sessione Privata (Gialla/Rossa) -->
                                                            <a href="calendario?sel_allenatore={$item->getAllenatore()->getId()}&sel_ora_inizio={$item->getOraInizio()->format('H:i:s')}&sel_ora_fine={$item->getOraFine()->format('H:i:s')}&data={$dataCorrente}" class="sp-block {if $selectedSp && $selectedSp->getAllenatore()->getId() === $item->getAllenatore()->getId() && $selectedSp->getOraInizio()->format('H:i:s') === $item->getOraInizio()->format('H:i:s')}is-selected{/if}">
                                                                <div class="has-text-weight-bold"><i class="fas fa-lock mr-1"></i>Sessione Privata</div>
                                                                {if $ruolo_utente === 'cliente'}
                                                                    <div class="is-size-7">Coach: {$item->getAllenatore()->getNome()}</div>
                                                                {else}
                                                                    <div class="is-size-7">Atleta: {$item->getAtleta()->getNome()}</div>
                                                                {/if}
                                                            </a>
                                                        {else}
                                                            <!-- Attività Pianificata (Blu) -->
                                                            <a href="calendario?id_ap={$item->getId()}&data={$dataCorrente}" class="ap-block {if $selectedAp && $selectedAp->getId() === $item->getId()}is-selected{/if}">
                                                                <div class="has-text-weight-bold">{$item->getAttivita()->getNome()}</div>
                                                                <div class="is-size-7">Sala: {$item->getSala()->getNome()}</div>
                                                                <div class="is-size-7">PT: {$item->getAllenatore()->getNome()}</div>
                                                                <div class="is-size-7"><i class="fas fa-users mr-1"></i>{$item->getPrenotati()}/{$item->getMaxPartecipanti()}</div>
                                                            </a>
                                                        {/if}
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

                <!-- COLONNA DETTAGLIO PLANNER SIDEBAR (Se selezionato un corso o una sessione privata o nuova sessione) -->
                {if $selectedAp || $selectedSp || $nuova_sessione}
                    <div class="column is-4 calendar-details-panel">
                        <div class="card p-5" style="border: 2px solid var(--gymfly-primary); background-color: var(--gymfly-card-bg); height: 100%;">
                            
                            <!-- DETTAGLIO ATTIVITÀ PIANIFICATA -->
                            {if $selectedAp}
                                <div class="is-flex is-justify-content-between is-align-items-center mb-4">
                                    <h2 class="title is-4 mb-0 style-theme-text">Dettaglio Corso</h2>
                                    <a href="calendario?data={$dataCorrente}" class="delete is-medium" title="Chiudi dettaglio" style="margin-left: auto;"></a>
                                </div>

                                <div class="box mb-4" style="background-color: rgba(175, 175, 226, 0.05); border: 1px solid var(--gymfly-primary);">
                                    <p class="is-size-5 mb-2"><strong>{$selectedAp->getAttivita()->getNome()}</strong></p>
                                    <p class="mb-2"><i class="fas fa-calendar-day mr-2"></i>Data: <strong>{$selectedAp->getGiorno()->format('d/m/Y')}</strong></p>
                                    <p class="mb-2"><i class="fas fa-clock mr-2"></i>Orario: <strong>{$selectedAp->getOrario()}:00</strong></p>
                                    <p class="mb-2"><i class="fas fa-door-open mr-2"></i>Sala: <strong>{$selectedAp->getSala()->getNome()}</strong></p>
                                    <p class="mb-3"><i class="fas fa-user-ninja mr-2"></i>Coach: <strong>{$selectedAp->getAllenatore()->getNome()} {$selectedAp->getAllenatore()->getCognome()}</strong></p>
                                    
                                    <div class="mb-2 is-size-7 is-flex is-justify-content-between">
                                        <span>Prenotati</span>
                                        <span><strong>{$selectedAp->getPrenotati()}</strong> / {$selectedAp->getMaxPartecipanti()}</span>
                                    </div>
                                    <progress class="progress is-link" value="{$selectedAp->getPrenotati()}" max="{$selectedAp->getMaxPartecipanti()}" style="height: 6px;"></progress>
                                </div>

                                {if $ruolo_utente === 'cliente'}
                                    <div class="mt-4">
                                        {if isset($iscrittoMap[$selectedAp->getId()]) && $iscrittoMap[$selectedAp->getId()]}
                                            <a href="disdici-prenotazione?id_attivita_pianificata={$selectedAp->getId()}" class="button is-danger is-light is-fullwidth">
                                                <span class="icon"><i class="fas fa-calendar-minus"></i></span>
                                                <span>Disdici la mia Prenotazione</span>
                                            </a>
                                        {elseif isset($inQueueMap[$selectedAp->getId()]) && $inQueueMap[$selectedAp->getId()]}
                                            <a href="disdici-prenotazione?id_attivita_pianificata={$selectedAp->getId()}" class="button is-danger is-light is-fullwidth">
                                                <span class="icon"><i class="fas fa-calendar-minus"></i></span>
                                                <span>Disdici Coda di Attesa</span>
                                            </a>
                                        {elseif $selectedAp->getPrenotati() >= $selectedAp->getMaxPartecipanti()}
                                            <a href="prenota-attivita?id_attivita_pianificata={$selectedAp->getId()}" class="button is-warning is-fullwidth" {if isset($puoPrenotare) && !$puoPrenotare}disabled onclick="return false;"{/if}>
                                                <span class="icon"><i class="fas fa-hourglass-half"></i></span>
                                                <span>Prenota</span>
                                            </a>
                                        {else}
                                            <a href="prenota-attivita?id_attivita_pianificata={$selectedAp->getId()}" class="button is-gymfly is-fullwidth" {if isset($puoPrenotare) && !$puoPrenotare}disabled onclick="return false;"{/if}>
                                                <span class="icon"><i class="fas fa-calendar-check"></i></span>
                                                <span>Iscriviti al corso</span>
                                            </a>
                                        {/if}
                                    </div>
                                {/if}

                            <!-- DETTAGLIO SESSIONE PRIVATA -->
                            {elseif $selectedSp}
                                <div class="is-flex is-justify-content-between is-align-items-center mb-4">
                                     <h2 class="title is-4 mb-0 style-theme-text" style="color: #e65100;">Sessione Privata</h2>
                                    <a href="calendario?data={$dataCorrente}" class="delete is-medium" title="Chiudi dettaglio" style="margin-left: auto;"></a>
                                </div>

                                 <div class="box mb-4" style="background-color: rgba(230, 81, 0, 0.04); border: 1px solid #e65100; border-radius: 10px;">
                                    <p class="is-size-5 mb-3"><strong>Incontro Individuale</strong></p>
                                    <p class="mb-2"><i class="fas fa-calendar-day mr-2"></i>Data: <strong>{$selectedSp->getData()->format('d/m/Y')}</strong></p>
                                    <p class="mb-2"><i class="fas fa-clock mr-2"></i>Inizio: <strong>{$selectedSp->getOraInizio()->format('H:i')}</strong></p>
                                    <p class="mb-2"><i class="fas fa-clock mr-2"></i>Fine: <strong>{$selectedSp->getOraFine()->format('H:i')}</strong></p>
                                    
                                    {if $ruolo_utente === 'cliente'}
                                        <p class="mb-2"><i class="fas fa-user-ninja mr-2"></i>Coach: <strong>{$selectedSp->getAllenatore()->getNome()} {$selectedSp->getAllenatore()->getCognome()}</strong></p>
                                    {else}
                                        <p class="mb-2"><i class="fas fa-user mr-2"></i>Atleta: <strong>{$selectedSp->getAtleta()->getNome()} {$selectedSp->getAtleta()->getCognome()}</strong></p>
                                    {/if}
                                </div>

                                <div class="mt-4">
                                    <a href="disdici-sessione-privata?id_allenatore={$selectedSp->getAllenatore()->getId()}&ora_inizio={$selectedSp->getOraInizio()->format('H:i:s')}&ora_fine={$selectedSp->getOraFine()->format('H:i:s')}" class="button is-danger is-light is-fullwidth" onclick="return confirm('Sei sicuro di voler annullare questa sessione privata?');">
                                        <span class="icon"><i class="fas fa-calendar-times"></i></span>
                                        <span>Disdici Sessione</span>
                                    </a>
                                </div>
                            {elseif $nuova_sessione}
                                <div class="is-flex is-justify-content-between is-align-items-center mb-4">
                                     <h2 class="title is-4 mb-0 style-theme-text" style="color: #e65100;">Pianifica Sessione</h2>
                                    <a href="calendario?data={$dataCorrente}" class="delete is-medium" title="Chiudi inserimento" style="margin-left: auto;"></a>
                                </div>

                                <form action="prenota-sessione-privata" method="POST">
                                    
                                    <!-- SELEZIONE ATLETA -->
                                    <div class="field mb-3">
                                        <label class="label is-small style-theme-text">Atleta / Cliente *</label>
                                        <div class="control">
                                            <div class="select is-fullwidth is-small">
                                                <select name="id_cliente" required>
                                                    <option value="">Scegli l'atleta...</option>
                                                    {foreach from=$clienti item=cl}
                                                        <option value="{$cl->getId()}">{$cl->getNome()} {$cl->getCognome()} ({$cl->getEmail()})</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- GIORNO -->
                                    <div class="field mb-3">
                                        <label class="label is-small style-theme-text">Giorno della sessione *</label>
                                        <div class="control">
                                            <input class="input is-small" type="date" name="data" required min="{$smarty.now|date_format:'%Y-%m-%d'}">
                                        </div>
                                    </div>

                                    <div class="columns mb-4">
                                        <!-- ORA INIZIO -->
                                        <div class="column">
                                            <div class="field">
                                                <label class="label is-small style-theme-text">Ora Inizio *</label>
                                                <div class="control">
                                                    <input class="input is-small" type="time" name="ora_inizio" required>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ORA FINE -->
                                        <div class="column">
                                            <div class="field">
                                                <label class="label is-small style-theme-text">Ora Fine *</label>
                                                <div class="control">
                                                    <input class="input is-small" type="time" name="ora_fine" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <!-- BOTTONI AZIONE -->
                                     <div class="field is-grouped is-grouped-right mt-5">
                                         <div class="control">
                                             <button type="submit" class="button is-gymfly">
                                                 <span class="icon"><i class="fas fa-save"></i></span>
                                                 <span>Salva Prenotazione</span>
                                             </button>
                                         </div>
                                         <div class="control">
                                             <a href="calendario" class="button is-light">Chiudi</a>
                                         </div>
                                     </div>

                                </form>
                            {/if}

                        </div>
                    </div>
                {/if}

            </div>

        </main>
    </div>

</body>
</html>
