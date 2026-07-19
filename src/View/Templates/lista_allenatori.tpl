<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Lista Allenatori</title>
    <link class="css-link" rel="stylesheet" href="css/bulma.min.css">
    <link class="css-link" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link class="css-link" rel="stylesheet" href="css/style.css">
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
                            Supervisione Allenatori
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Visualizza e supervisiona i profili degli allenatori della palestra
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-user-ninja fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">ALLENATORI</strong>
            </div>

            <!-- CONTROLS / TOOLBAR -->
            <div id="controls-toolbar" class="is-flex is-justify-content-between is-align-items-center mb-5 is-flex-wrap-wrap" style="gap: 15px;">
                <div class="buttons mb-0" style="align-items: flex-start;">
                    
                    <!-- NUOVO CON TESTO FILTRI SOTTO -->
                    <div class="is-relative mr-2" style="display: inline-block;">
                        <a href="crea-allenatore" class="button is-gymfly mr-0">
                            <span>+ Nuovo</span>
                        </a>

                        <!-- Stato Filtri Attivi (sotto il tasto Nuovo) -->
                        <div id="active-filters-tags" class="is-flex is-align-items-center mt-2 is-hidden" style="position: absolute; top: 40px; left: 0; z-index: 5; gap: 5px; white-space: nowrap;">
                            <span id="tag-activity" class="tag is-info font-weight-bold"></span>
                            <a id="btn-reset-filters-shortcut" href="#" onclick="resetAllFilters(); return false;" class="delete is-small" style="margin-left: 2px;" title="Rimuovi Filtri"></a>
                        </div>
                    </div>
                    
                    <!-- DROPDOWN FILTRA -->
                    <div class="dropdown is-hoverable">
                        <div class="dropdown-trigger">
                            <button class="button is-light mr-1" aria-haspopup="true" aria-controls="dropdown-menu-filter">
                                <span class="icon"><i class="fas fa-filter"></i></span>
                                <span>Filtra</span>
                            </button>
                        </div>
                        <div class="dropdown-menu" id="dropdown-menu-filter" role="menu">
                            <div class="dropdown-content">
                                <p class="dropdown-item font-weight-bold" style="font-size: 0.85rem; color: var(--gymfly-primary) !important; margin-bottom: 0;">ATTIVITÀ</p>
                                <div id="activity-filters-container">
                                    <a href="#" onclick="setActivityFilter('ALL'); return false;" id="filter-activity-all" class="dropdown-item is-active">Tutte</a>
                                    <!-- Dynamic activity filters will be inserted here via JS -->
                                </div>
                                <div id="reset-filters-container" class="is-hidden">
                                    <hr class="dropdown-divider">
                                    <a href="#" onclick="resetAllFilters(); return false;" class="dropdown-item has-text-danger font-weight-bold">Rimuovi Filtri</a>
                                </div>
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
                                <a href="#" onclick="setSortOrder('cognome_asc'); return false;" id="sort-cognome_asc" class="dropdown-item is-active">Cognome (A-Z)</a>
                                <a href="#" onclick="setSortOrder('cognome_desc'); return false;" id="sort-cognome_desc" class="dropdown-item">Cognome (Z-A)</a>
                                <a href="#" onclick="setSortOrder('nome_asc'); return false;" id="sort-nome_asc" class="dropdown-item">Nome (A-Z)</a>
                                <a href="#" onclick="setSortOrder('nome_desc'); return false;" id="sort-nome_desc" class="dropdown-item">Nome (Z-A)</a>
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
                        <div class="field mb-0">
                            <div class="control has-icons-left">
                                <input id="search-input" class="input" type="text" placeholder="Search" oninput="filterTrainers()">
                                <span class="icon is-left">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- No results message -->
            <div id="no-results-msg" class="box has-text-centered py-6 is-hidden mb-5">
                <span class="icon is-large mb-3 has-text-grey"><i class="fas fa-search fa-3x"></i></span>
                <p class="has-text-grey is-size-5">Nessun allenatore soddisfa i filtri selezionati.</p>
            </div>

            <!-- CARDS GRID VIEW -->
            <div id="allenatori-grid" class="columns is-multiline">
                {foreach $allenatori as $a}
                    <div class="column is-3-desktop is-4-tablet is-12-mobile trainer-grid-item" 
                         data-nome="{$a.nome|escape:'html'}" 
                         data-cognome="{$a.cognome|escape:'html'}" 
                         data-sesso="{$a.sesso}" 
                         data-attivita="{$a.attivita|escape:'html'}" 
                         data-search="{$a.nome} {$a.cognome}">
                        <a href="visualizza-profilo?id={$a.id}" class="box customer-card">
                            <div class="customer-avatar mb-3" style="width: 96px; height: 96px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                {if isset($a.fotoProfilo) && $a.fotoProfilo !== null}
                                    <img src="data:{$a.tipoImmagine};base64,{$a.fotoProfilo}" alt="Foto Profilo" style="width: 100%; height: 100%; object-fit: cover;">
                                {else}
                                    <span class="icon is-large">
                                        <i class="fas fa-user-ninja fa-4x"></i>
                                    </span>
                                {/if}
                            </div>
                            <h3 class="title is-5 mb-2 has-text-centered">{$a.nome} {$a.cognome}</h3>
                            <p class="subtitle is-6 has-text-grey-dark mb-1 has-text-centered" style="word-break: break-all;">{$a.email}</p>
                            <p class="is-size-7 has-text-grey has-text-centered">{$a.cf}</p>
                            {if isset($a.attivita) && $a.attivita !== ''}
                                <div class="has-text-centered mt-2">
                                    {assign var="atts" value=$a.attivita|split:','}
                                    {foreach $atts as $att}
                                        <span class="tag is-light is-rounded is-size-7 mb-1" style="margin: 2px;">{$att}</span>
                                    {/foreach}
                                </div>
                            {/if}
                        </a>
                    </div>
                {foreachelse}
                    <div class="column is-12">
                        <div class="box has-text-centered py-6">
                            <span class="icon is-large mb-3 has-text-grey"><i class="fas fa-search fa-3x"></i></span>
                            <p class="has-text-grey is-size-5">Nessun allenatore registrato in questa palestra.</p>
                        </div>
                    </div>
                {/foreach}
            </div>

            <!-- LIST VIEW -->
            <div id="allenatori-list" class="is-hidden">
                {foreach $allenatori as $a}
                    <a href="visualizza-profilo?id={$a.id}" class="box customer-list-item mb-3 trainer-list-item" 
                       data-nome="{$a.nome|escape:'html'}" 
                       data-cognome="{$a.cognome|escape:'html'}" 
                       data-sesso="{$a.sesso}" 
                       data-attivita="{$a.attivita|escape:'html'}" 
                       data-search="{$a.nome} {$a.cognome}">
                        <div class="customer-avatar mr-4" style="width: 48px; height: 48px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            {if isset($a.fotoProfilo) && $a.fotoProfilo !== null}
                                <img src="data:{$a.tipoImmagine};base64,{$a.fotoProfilo}" alt="Foto Profilo" style="width: 100%; height: 100%; object-fit: cover;">
                            {else}
                                <span class="icon is-medium">
                                    <i class="fas fa-user-ninja fa-2x"></i>
                                </span>
                            {/if}
                        </div>
                        <div class="is-flex-grow-1">
                            <h3 class="title is-5 mb-1 style-theme-text">{$a.nome} {$a.cognome}</h3>
                            <p class="subtitle is-6 has-text-grey-dark mb-1" style="word-break: break-all;">
                                {$a.email} &bull; <span class="tag is-light">{$a.cf}</span>
                            </p>
                            {if isset($a.attivita) && $a.attivita !== ''}
                                <div class="tags mb-0 mt-1">
                                    {assign var="atts" value=$a.attivita|split:','}
                                    {foreach $atts as $att}
                                        <span class="tag is-light is-rounded is-size-7" style="margin-right: 4px; margin-bottom: 2px;">{$att}</span>
                                    {/foreach}
                                </div>
                            {/if}
                        </div>
                        <div>
                            <span class="icon has-text-grey-light"><i class="fas fa-chevron-right"></i></span>
                        </div>
                    </a>
                {foreachelse}
                    <div class="box has-text-centered py-6">
                        <span class="icon is-large mb-3 has-text-grey"><i class="fas fa-search fa-3x"></i></span>
                        <p class="has-text-grey is-size-5">Nessun allenatore registrato in questa palestra.</p>
                    </div>
                {/foreach}
            </div>

        </main>
    </div>

    <!-- SCRIPT PER TOGGLE VISUALIZZAZIONE E FILTRO -->
    <script>
        let activeActivityFilter = 'ALL';
        let activeSortOrder = 'cognome_asc';

        function switchView(viewType) {
            const isGrid = viewType === 'grid';
            document.getElementById('allenatori-grid').classList.toggle('is-hidden', !isGrid);
            document.getElementById('allenatori-list').classList.toggle('is-hidden', isGrid);
            document.getElementById('btn-grid').classList.toggle('is-gymfly', isGrid);
            document.getElementById('btn-grid').classList.toggle('is-light', !isGrid);
            document.getElementById('btn-list').classList.toggle('is-gymfly', !isGrid);
            document.getElementById('btn-list').classList.toggle('is-light', isGrid);
            
            localStorage.setItem('allenatori-view-preference', viewType);
            filterTrainers();
        }

        function filterTrainers() {
            const query = document.getElementById('search-input').value.toLowerCase();
            const items = document.querySelectorAll('.trainer-grid-item, .trainer-list-item');
            let visibleGrid = 0, visibleList = 0;

            items.forEach(item => {
                const searchTxt = item.getAttribute('data-search').toLowerCase();
                const atts = item.getAttribute('data-attivita');
                const matchesSearch = searchTxt.includes(query);
                const matchesActivity = activeActivityFilter === 'ALL' || (atts && atts.split(',').includes(activeActivityFilter));

                if (matchesSearch && matchesActivity) {
                    item.classList.remove('is-hidden');
                    if (item.classList.contains('trainer-grid-item')) visibleGrid++;
                    else visibleList++;
                } else {
                    item.classList.add('is-hidden');
                }
            });

            updateNoResultsMessage(visibleGrid, visibleList);
            updateFilterTags();
        }

        function populateActivityFilters() {
            const activities = new Set();
            document.querySelectorAll('.trainer-grid-item').forEach(item => {
                const atts = item.getAttribute('data-attivita');
                if (atts) atts.split(',').forEach(a => activities.add(a.trim()));
            });

            const container = document.getElementById('activity-filters-container');
            const allFilter = document.getElementById('filter-activity-all');
            container.replaceChildren(allFilter);

            Array.from(activities).sort().forEach(activity => {
                const link = document.createElement('a');
                link.href = '#';
                link.className = 'dropdown-item';
                link.textContent = activity;
                link.onclick = (e) => {
                    e.preventDefault();
                    setActivityFilter(activity, link);
                };
                container.appendChild(link);
            });
        }

        function setActivityFilter(activity, clickedElement) {
            activeActivityFilter = activity;
            document.querySelectorAll('#activity-filters-container .dropdown-item').forEach(l => l.classList.remove('is-active'));
            if (clickedElement) {
                clickedElement.classList.add('is-active');
            } else {
                document.getElementById('filter-activity-all').classList.add('is-active');
            }
            filterTrainers();
        }

        function resetAllFilters() {
            document.getElementById('search-input').value = '';
            setActivityFilter('ALL');
        }

        function updateFilterTags() {
            const hasFilters = activeActivityFilter !== 'ALL';
            document.getElementById('tag-activity').textContent = activeActivityFilter;
            document.getElementById('tag-activity').classList.toggle('is-hidden', !hasFilters);
            document.getElementById('active-filters-tags').classList.toggle('is-hidden', !hasFilters);
            
            const resetBtn = document.getElementById('btn-reset-filters-shortcut');
            if (resetBtn) resetBtn.classList.toggle('is-hidden', !hasFilters);
            
            const toolbar = document.getElementById('controls-toolbar');
            if (toolbar) {
                toolbar.classList.toggle('mb-5', !hasFilters);
                toolbar.classList.toggle('mb-6', hasFilters);
                toolbar.classList.toggle('pb-2', hasFilters);
            }
        }

        function updateNoResultsMessage(visibleGridCount, visibleListCount) {
            const isGridActive = !document.getElementById('allenatori-grid').classList.contains('is-hidden');
            const count = isGridActive ? visibleGridCount : visibleListCount;
            const hasTotalItems = document.querySelectorAll('.trainer-grid-item').length > 0;
            
            document.getElementById('no-results-msg').classList.toggle('is-hidden', count > 0 || !hasTotalItems);
        }

        function setSortOrder(order) {
            activeSortOrder = order;
            document.querySelectorAll('[id^="sort-"]').forEach(link => {
                link.classList.toggle('is-active', link.id === 'sort-' + order);
            });
            applySort();
        }

        function applySort() {
            const sortAndAppend = (containerId, selector) => {
                const container = document.getElementById(containerId);
                const items = Array.from(container.querySelectorAll(selector));
                items.sort((a, b) => compareItems(a, b, activeSortOrder));
                items.forEach(item => container.appendChild(item));
            };
            sortAndAppend('allenatori-grid', '.trainer-grid-item');
            sortAndAppend('allenatori-list', '.trainer-list-item');
        }

        function compareItems(a, b, order) {
            const isCognome = order.startsWith('cognome');
            const attrName = isCognome ? 'data-cognome' : 'data-nome';
            const valA = (a.getAttribute(attrName) || '').toLowerCase();
            const valB = (b.getAttribute(attrName) || '').toLowerCase();
            return order.endsWith('asc') ? valA.localeCompare(valB) : valB.localeCompare(valA);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const pref = localStorage.getItem('allenatori-view-preference') || 'grid';
            switchView(pref);
            populateActivityFilters();
            applySort();
            filterTrainers();
        });
    </script>

</body>
</html>
