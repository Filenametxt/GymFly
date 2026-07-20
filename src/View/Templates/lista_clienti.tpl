<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Lista Clienti</title>
    <link class="css-link" rel="stylesheet" href="css/bulma.min.css">
    <link class="css-link" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link class="css-link" rel="stylesheet" href="css/style.css?v=1.2">
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
                            Gestione Clienti
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Visualizza e gestisci le schede anagrafiche dei clienti della palestra
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-users fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">CLIENTI</strong>
            </div>

            {assign var="filtro_certificato" value=(isset($smarty.request.filtro_certificato) && $smarty.request.filtro_certificato !== '' ? $smarty.request.filtro_certificato : null)}
            {assign var="filtro_abbonamento" value=(isset($smarty.request.filtro_abbonamento) && $smarty.request.filtro_abbonamento !== '' ? $smarty.request.filtro_abbonamento : null)}
            {assign var="filtro_scheda" value=(isset($smarty.request.filtro_scheda) && $smarty.request.filtro_scheda !== '' ? $smarty.request.filtro_scheda : null)}
            {assign var="ordine" value=(isset($smarty.request.ordine) && $smarty.request.ordine !== '' ? $smarty.request.ordine : null)}
            {assign var="search_query" value=(isset($smarty.request.search_query) && $smarty.request.search_query !== '' ? $smarty.request.search_query : null)}

            {assign var="hasFilters" value=($filtro_certificato !== null) || ($filtro_abbonamento !== null) || ($filtro_scheda !== null)}

            <!-- CONTROLS / TOOLBAR -->
            <div class="is-flex is-justify-content-between is-align-items-center {if $hasFilters}mb-6 pb-2{else}mb-5{/if} is-flex-wrap-wrap" style="gap: 15px;">
                <div class="buttons mb-0" style="align-items: flex-start;">
                    
                    <!-- NUOVO CON TESTO FILTRI SOTTO -->
                    <div class="is-relative mr-2" style="display: inline-block;">
                        <a href="crea-cliente" class="button is-gymfly mr-0">
                            <span>+ Nuovo</span>
                        </a>

                        <!-- Stato Filtri Attivi (sotto il tasto Nuovo) -->
                        {if $hasFilters}
                            <div class="is-flex is-align-items-center mt-2" style="position: absolute; top: 40px; left: 0; z-index: 5; gap: 5px; white-space: nowrap;">
                                {if isset($filtro_certificato) && $filtro_certificato !== null}
                                    {if $filtro_certificato === 'scaduti'}
                                        <span class="tag is-danger font-weight-bold">Certificato Scaduto</span>
                                    {elseif $filtro_certificato === 'in_scadenza'}
                                        <span class="tag is-warning font-weight-bold">Certificato in Scadenza</span>
                                    {elseif $filtro_certificato === 'in_regola'}
                                        <span class="tag is-success font-weight-bold">Certificato Valido</span>
                                    {/if}
                                {/if}
                                {if isset($filtro_abbonamento) && $filtro_abbonamento !== null}
                                    {if $filtro_abbonamento === 'attivo'}
                                        <span class="tag is-success font-weight-bold">Abbonamento Attivo</span>
                                    {elseif $filtro_abbonamento === 'scaduto'}
                                        <span class="tag is-danger font-weight-bold">Abbonamento Scaduto</span>
                                    {/if}
                                {/if}
                                {if isset($filtro_scheda) && $filtro_scheda !== null}
                                    {if $filtro_scheda === 'scadute'}
                                        <span class="tag is-danger font-weight-bold">Scheda Scaduta</span>
                                    {elseif $filtro_scheda === 'assenti'}
                                        <span class="tag is-warning font-weight-bold">Scheda Assente</span>
                                    {elseif $filtro_scheda === 'in_regola'}
                                        <span class="tag is-success font-weight-bold">Scheda in Regola</span>
                                    {/if}
                                {/if}
                                <!-- Tasto X di rimozione filtro direttamente dopo i tag del filtro applicato -->
                                <a href="clienti{if isset($ordine)}?ordine={$ordine}{/if}" class="delete is-small" style="margin-left: 2px;" title="Rimuovi Filtri"></a>
                            </div>
                        {/if}
                    </div>

                    <!-- DROPDOWN FILTRA -->
                    <div class="dropdown is-hoverable mr-1">
                        <div class="dropdown-trigger">
                            <button class="button is-light mr-0" aria-haspopup="true" aria-controls="dropdown-menu-filter">
                                <span class="icon"><i class="fas fa-filter"></i></span>
                                <span>Filtra</span>
                            </button>
                        </div>
                        <div class="dropdown-menu" id="dropdown-menu-filter" role="menu">
                            <div class="dropdown-content">
                                {if $smarty.session.ruolo_utente === 'allenatore'}
                                    <p class="dropdown-item font-weight-bold" style="font-size: 0.85rem; color: var(--gymfly-primary) !important; margin-bottom: 0;">SCHEDA ALLENAMENTO</p>
                                    <a href="clienti?filtro_scheda=scadute{if isset($ordine)}&ordine={$ordine}{/if}" class="dropdown-item {if isset($filtro_scheda) && $filtro_scheda === 'scadute'}is-active{/if}">Scaduta</a>
                                    <a href="clienti?filtro_scheda=assenti{if isset($ordine)}&ordine={$ordine}{/if}" class="dropdown-item {if isset($filtro_scheda) && $filtro_scheda === 'assenti'}is-active{/if}">Assente</a>
                                    <a href="clienti?filtro_scheda=in_regola{if isset($ordine)}&ordine={$ordine}{/if}" class="dropdown-item {if isset($filtro_scheda) && $filtro_scheda === 'in_regola'}is-active{/if}">In regola</a>
                                {else}
                                    <p class="dropdown-item font-weight-bold" style="font-size: 0.85rem; color: var(--gymfly-primary) !important; margin-bottom: 0;">CERTIFICATO MEDICO</p>
                                    <a href="clienti?filtro_certificato=scaduti{if isset($filtro_abbonamento)}&filtro_abbonamento={$filtro_abbonamento}{/if}{if isset($ordine)}&ordine={$ordine}{/if}" class="dropdown-item {if isset($filtro_certificato) && $filtro_certificato === 'scaduti'}is-active{/if}">Scaduti / Assenti</a>
                                    <a href="clienti?filtro_certificato=in_scadenza{if isset($filtro_abbonamento)}&filtro_abbonamento={$filtro_abbonamento}{/if}{if isset($ordine)}&ordine={$ordine}{/if}" class="dropdown-item {if isset($filtro_certificato) && $filtro_certificato === 'in_scadenza'}is-active{/if}">In scadenza</a>
                                    <a href="clienti?filtro_certificato=in_regola{if isset($filtro_abbonamento)}&filtro_abbonamento={$filtro_abbonamento}{/if}{if isset($ordine)}&ordine={$ordine}{/if}" class="dropdown-item {if isset($filtro_certificato) && $filtro_certificato === 'in_regola'}is-active{/if}">In regola</a>
                                    <hr class="dropdown-divider">
                                    <p class="dropdown-item font-weight-bold" style="font-size: 0.85rem; color: var(--gymfly-primary) !important; margin-bottom: 0;">ABBONAMENTO</p>
                                    <a href="clienti?filtro_abbonamento=attivo{if isset($filtro_certificato)}&filtro_certificato={$filtro_certificato}{/if}{if isset($ordine)}&ordine={$ordine}{/if}" class="dropdown-item {if isset($filtro_abbonamento) && $filtro_abbonamento === 'attivo'}is-active{/if}">Attivo</a>
                                    <a href="clienti?filtro_abbonamento=scaduto{if isset($filtro_certificato)}&filtro_certificato={$filtro_certificato}{/if}{if isset($ordine)}&ordine={$ordine}{/if}" class="dropdown-item {if isset($filtro_abbonamento) && $filtro_abbonamento === 'scaduto'}is-active{/if}">Scaduto / Assente</a>
                                {/if}
                                {if $hasFilters}
                                    <hr class="dropdown-divider">
                                    <a href="clienti{if isset($ordine)}?ordine={$ordine}{/if}" class="dropdown-item has-text-danger font-weight-bold">Rimuovi Filtri</a>
                                {/if}
                            </div>
                        </div>
                    </div>

                    <!-- DROPDOWN ORDINA -->
                    <div class="dropdown is-hoverable">
                        <div class="dropdown-trigger">
                            <button class="button is-light mr-3" aria-haspopup="true" aria-controls="dropdown-menu-sort">
                                <span class="icon"><i class="fas fa-sort"></i></span>
                                <span>Ordina</span>
                            </button>
                        </div>
                        <div class="dropdown-menu" id="dropdown-menu-sort" role="menu">
                            <div class="dropdown-content">
                                <a href="clienti?ordine=cognome_asc{if isset($filtro_certificato)}&filtro_certificato={$filtro_certificato}{/if}{if isset($filtro_abbonamento)}&filtro_abbonamento={$filtro_abbonamento}{/if}{if isset($filtro_scheda)}&filtro_scheda={$filtro_scheda}{/if}" class="dropdown-item {if !isset($ordine) || $ordine === 'cognome_asc'}is-active{/if}">Cognome (A-Z)</a>
                                <a href="clienti?ordine=cognome_desc{if isset($filtro_certificato)}&filtro_certificato={$filtro_certificato}{/if}{if isset($filtro_abbonamento)}&filtro_abbonamento={$filtro_abbonamento}{/if}{if isset($filtro_scheda)}&filtro_scheda={$filtro_scheda}{/if}" class="dropdown-item {if isset($ordine) && $ordine === 'cognome_desc'}is-active{/if}">Cognome (Z-A)</a>
                                <a href="clienti?ordine=nome_asc{if isset($filtro_certificato)}&filtro_certificato={$filtro_certificato}{/if}{if isset($filtro_abbonamento)}&filtro_abbonamento={$filtro_abbonamento}{/if}{if isset($filtro_scheda)}&filtro_scheda={$filtro_scheda}{/if}" class="dropdown-item {if isset($ordine) && $ordine === 'nome_asc'}is-active{/if}">Nome (A-Z)</a>
                                <a href="clienti?ordine=nome_desc{if isset($filtro_certificato)}&filtro_certificato={$filtro_certificato}{/if}{if isset($filtro_abbonamento)}&filtro_abbonamento={$filtro_abbonamento}{/if}{if isset($filtro_scheda)}&filtro_scheda={$filtro_scheda}{/if}" class="dropdown-item {if isset($ordine) && $ordine === 'nome_desc'}is-active{/if}">Nome (Z-A)</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- CONTAINER RICERCA E STATO FILTRI -->
                <div class="is-flex is-align-items-center" style="gap: 12px; margin-left: auto; flex-wrap: wrap;">
                    
                    <!-- Toggles per Vista Griglia / Lista -->
                    <div class="field has-addons mb-0">
                        <p class="control">
                            <button id="btn-grid" class="button is-gymfly" onclick="switchView('grid')" title="Visualizzazione Griglia">
                                <span class="icon"><i class="fas fa-th"></i></span>
                            </button>
                        </p>
                        <p class="control">
                            <button id="btn-list" class="button is-light" onclick="switchView('list')" title="Visualizzazione Elenco">
                                <span class="icon"><i class="fas fa-list"></i></span>
                            </button>
                        </p>
                    </div>

                    <!-- Barra di Ricerca -->
                    <div class="search-container" style="max-width: 200px; width: 100%;">
                        <form action="clienti" method="POST">
                            {if isset($filtro_certificato)}<input type="hidden" name="filtro_certificato" value="{$filtro_certificato}">{/if}
                            {if isset($filtro_abbonamento)}<input type="hidden" name="filtro_abbonamento" value="{$filtro_abbonamento}">{/if}
                            {if isset($filtro_scheda)}<input type="hidden" name="filtro_scheda" value="{$filtro_scheda}">{/if}
                            {if isset($ordine)}<input type="hidden" name="ordine" value="{$ordine}">{/if}
                            <div class="field mb-0">
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="search_query" placeholder="Search" value="{$search_query}">
                                    <span class="icon is-left">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <!-- CARDS GRID VIEW -->
            <div id="clienti-grid" class="columns is-multiline">
                {foreach $clienti as $c}
                    <div class="column is-3-desktop is-4-tablet is-12-mobile">
                        <a href="visualizza-profilo?id={$c.id}" class="box customer-card">
                            <div class="customer-avatar mb-3" style="width: 96px; height: 96px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                {if isset($c.fotoProfilo) && $c.fotoProfilo !== null}
                                    <img src="data:{$c.tipoImmagine};base64,{$c.fotoProfilo}" alt="Foto Profilo" style="width: 100%; height: 100%; object-fit: cover;">
                                {else}
                                    <span class="icon is-large">
                                        <i class="fas fa-user-circle fa-4x"></i>
                                    </span>
                                {/if}
                            </div>
                            <h3 class="title is-5 mb-2 has-text-centered">{$c.nome} {$c.cognome}</h3>
                            <p class="subtitle is-6 has-text-grey-dark mb-1 has-text-centered" style="word-break: break-all;">{$c.email}</p>
                            <p class="is-size-7 has-text-grey has-text-centered">{$c.cf}</p>
                        </a>
                    </div>
                {foreachelse}
                    <div class="column is-12">
                        <div class="box has-text-centered py-6">
                            <span class="icon is-large mb-3 has-text-grey"><i class="fas fa-search fa-3x"></i></span>
                            <p class="has-text-grey is-size-5">Nessun cliente corrispondente ai criteri di ricerca.</p>
                        </div>
                    </div>
                {/foreach}
            </div>

            <!-- LIST VIEW -->
            <div id="clienti-list" class="is-hidden">
                {foreach $clienti as $c}
                    <a href="visualizza-profilo?id={$c.id}" class="box customer-list-item mb-3">
                        <div class="customer-avatar mr-4" style="width: 48px; height: 48px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            {if isset($c.fotoProfilo) && $c.fotoProfilo !== null}
                                <img src="data:{$c.tipoImmagine};base64,{$c.fotoProfilo}" alt="Foto Profilo" style="width: 100%; height: 100%; object-fit: cover;">
                            {else}
                                <span class="icon is-medium">
                                    <i class="fas fa-user-circle fa-2x"></i>
                                </span>
                            {/if}
                        </div>
                        <div class="is-flex-grow-1">
                            <h3 class="title is-5 mb-1 style-theme-text">{$c.nome} {$c.cognome}</h3>
                            <p class="subtitle is-6 has-text-grey-dark mb-0" style="word-break: break-all;">
                                {$c.email} &bull; <span class="tag is-light">{$c.cf}</span>
                            </p>
                        </div>
                        <div>
                            <span class="icon has-text-grey-light"><i class="fas fa-chevron-right"></i></span>
                        </div>
                    </a>
                {foreachelse}
                    <div class="box has-text-centered py-6">
                        <span class="icon is-large mb-3 has-text-grey"><i class="fas fa-search fa-3x"></i></span>
                        <p class="has-text-grey is-size-5">Nessun cliente corrispondente ai criteri di ricerca.</p>
                    </div>
                {/foreach}
            </div>

        </main>
    </div>

    <!-- SCRIPT PER TOGGLE VISUALIZZAZIONE -->
    <script>
        function switchView(viewType) {
            const gridView = document.getElementById('clienti-grid');       //visualizzazione
            const listView = document.getElementById('clienti-list');
            const btnGrid = document.getElementById('btn-grid');            //bottone
            const btnList = document.getElementById('btn-list');

            if (viewType === 'grid') {
                gridView.classList.remove('is-hidden');
                listView.classList.add('is-hidden');
                
                btnGrid.classList.add('is-gymfly');               
                btnGrid.classList.remove('is-light');
                
                btnList.classList.remove('is-gymfly');
                btnList.classList.add('is-light');
                
                localStorage.setItem('clienti-view-preference', 'grid');
            } else {
                gridView.classList.add('is-hidden');
                listView.classList.remove('is-hidden');
                
                btnList.classList.add('is-gymfly');
                btnList.classList.remove('is-light');
                
                btnGrid.classList.remove('is-gymfly');
                btnGrid.classList.add('is-light');
                
                localStorage.setItem('clienti-view-preference', 'list');
            }
        }

        // Ripristina preferenza
        document.addEventListener('DOMContentLoaded', () => {
            const pref = localStorage.getItem('clienti-view-preference') || 'grid';
            switchView(pref);
        });
    </script>

</body>
</html>
