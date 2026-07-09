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
            background-color: var(--gymfly-card-bg);
            color: #AFAFE2;
            border-radius: 8px;
            border: 1px solid var(--gymfly-primary);
            font-size: 0.9rem;
        }
        .planner-table td {
            height: 90px;
            vertical-align: top;
            padding: 6px;
            background-color: rgba(26, 26, 46, 0.4);
            border-radius: 8px;
            border: 1px dashed rgba(175, 175, 226, 0.2);
            position: relative;
        }
        .planner-table .hour-col {
            width: 65px;
            vertical-align: middle;
            text-align: center;
            background-color: var(--gymfly-card-bg);
            color: var(--gymfly-text);
            font-weight: bold;
            font-size: 0.85rem;
            border: 1px solid var(--gymfly-primary);
            height: auto;
        }
        .ap-block {
            background: linear-gradient(135deg, var(--gymfly-primary) 0%, var(--gymfly-accent) 100%);
            color: white;
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            margin-bottom: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: block;
        }
        .ap-block:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(175, 175, 226, 0.25);
        }
        .ap-block.is-selected {
            border: 2px solid white;
            box-shadow: 0 0 10px var(--gymfly-secondary);
        }
        .sp-block {
            background: linear-gradient(135deg, #f5af19 0%, #f12711 100%);
            color: white;
            padding: 6px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            margin-bottom: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: block;
        }
        .sp-block:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(245, 175, 25, 0.25);
        }
        .sp-block.is-selected {
            border: 2px solid white;
            box-shadow: 0 0 10px #f5af19;
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- HEADER -->
            <div class="mb-5">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-2 style-theme-text mb-2">Calendario Corsi</h1>
                        <p class="subtitle is-6 has-text-grey">Esplora la programmazione settimanale, iscriviti ai corsi o gestisci le tue sessioni private</p>
                    </div>
                    <div class="column is-narrow">
                        {if $ruolo_utente === 'allenatore'}
                            <a href="prenota-sessione-privata" class="button is-gymfly">
                                <span class="icon"><i class="fas fa-calendar-plus"></i></span>
                                <span>Pianifica Sessione Privata</span>
                            </a>
                        {/if}
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
                <div class="column {if $selectedAp || $selectedSp}is-8{else}is-12{/if}">
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
                                                        {if $item->isPrivate}
                                                            <!-- Sessione Privata (Gialla/Rossa) -->
                                                            <a href="calendario?sel_allenatore={$item->getAllenatore()->getId()}&sel_ora_inizio={$item->getOraInizio()->format('H:i:s')}&sel_ora_fine={$item->getOraFine()->format('H:i:s')}" class="sp-block {if $selectedSp && $selectedSp->getAllenatore()->getId() === $item->getAllenatore()->getId() && $selectedSp->getOraInizio()->format('H:i:s') === $item->getOraInizio()->format('H:i:s')}is-selected{/if}">
                                                                <div class="has-text-weight-bold"><i class="fas fa-lock mr-1"></i>Sessione Privata</div>
                                                                {if $ruolo_utente === 'cliente'}
                                                                    <div class="is-size-7">Coach: {$item->getAllenatore()->getNome()}</div>
                                                                {else}
                                                                    <div class="is-size-7">Atleta: {$item->getAtleta()->getNome()}</div>
                                                                {/if}
                                                            </a>
                                                        {else}
                                                            <!-- Attività Pianificata (Blu) -->
                                                            <a href="calendario?id_ap={$item->getId()}" class="ap-block {if $selectedAp && $selectedAp->getId() === $item->getId()}is-selected{/if}">
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

                <!-- COLONNA DETTAGLIO PLANNER SIDEBAR (Se selezionato un corso o una sessione privata) -->
                {if $selectedAp || $selectedSp}
                    <div class="column is-4">
                        <div class="card p-5" style="border: 2px solid var(--gymfly-primary); background-color: var(--gymfly-card-bg); height: 100%;">
                            
                            <!-- DETTAGLIO ATTIVITÀ PIANIFICATA -->
                            {if $selectedAp}
                                <div class="is-flex is-justify-content-between is-align-items-center mb-4">
                                    <h2 class="title is-4 mb-0 style-theme-text">Dettaglio Corso</h2>
                                    <a href="calendario" class="delete" title="Chiudi dettaglio"></a>
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
                                        {elseif $selectedAp->getPrenotati() >= $selectedAp->getMaxPartecipanti()}
                                            <button class="button is-static is-fullwidth" disabled>Al completo</button>
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
                                    <h2 class="title is-4 mb-0 style-theme-text" style="color: #f5af19;">Sessione Privata</h2>
                                    <a href="calendario" class="delete" title="Chiudi dettaglio"></a>
                                </div>

                                <div class="box mb-4" style="background-color: rgba(245, 175, 25, 0.05); border: 1px solid #f5af19;">
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
                            {/if}

                        </div>
                    </div>
                {/if}

            </div>

        </main>
    </div>

</body>
</html>
