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

            <!-- HEADER -->
            <div class="mb-5">
                <div class="is-flex is-align-items-center is-flex-wrap-wrap mb-2">
                    <h1 class="title is-2 style-theme-text mb-0 mr-3">Gestione utenti - Supervisione Allenatori</h1>
                </div>
                <p class="subtitle is-6 has-text-grey">Elenco dei preparatori atletici abilitati per la palestra</p>
            </div>

            <!-- CONTROLS / TOOLBAR -->
            <div class="is-flex is-justify-content-between is-align-items-center mb-5 is-flex-wrap-wrap" style="gap: 15px;">
                <div class="buttons mb-0">
                    <a href="crea-allenatore" class="button is-gymfly mr-2">
                        <span>+ Nuovo</span>
                    </a>
                    
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
                <div class="is-flex is-align-items-center" style="gap: 12px; flex-grow: 1; max-width: 700px; justify-content: flex-end; flex-wrap: wrap;">
                    
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

                    <!-- Stato Filtri Attivi -->
                    <div id="active-filters-tags" class="tags mb-0 is-flex is-align-items-center is-hidden" style="gap: 5px;">
                        <span id="tag-activity" class="tag is-info font-weight-bold is-hidden"></span>
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
                         data-search="{$a.nome} {$a.cognome} {$a.email} {$a.cf}">
                        <a href="visualizza-profilo?id={$a.id}" class="box customer-card">
                            <div class="customer-avatar mb-3">
                                <span class="icon is-large">
                                    <i class="fas fa-user-ninja fa-4x"></i>
                                </span>
                            </div>
                            <h3 class="title is-5 mb-2 has-text-centered">{$a.nome} {$a.cognome}</h3>
                            <p class="subtitle is-6 has-text-grey-dark mb-1 has-text-centered" style="word-break: break-all;">{$a.email}</p>
                            <p class="is-size-7 has-text-grey has-text-centered">{$a.cf}</p>
                            {if isset($a.attivita) && $a.attivita !== ''}
                                <div class="has-text-centered mt-2">
                                    {assign var="atts" value=explode(',', $a.attivita)}
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
                       data-search="{$a.nome} {$a.cognome} {$a.email} {$a.cf}">
                        <div class="customer-avatar mr-4">
                            <span class="icon is-medium">
                                <i class="fas fa-user-ninja fa-2x"></i>
                            </span>
                        </div>
                        <div class="is-flex-grow-1">
                            <h3 class="title is-5 mb-1 style-theme-text">{$a.nome} {$a.cognome}</h3>
                            <p class="subtitle is-6 has-text-grey-dark mb-1" style="word-break: break-all;">
                                {$a.email} &bull; <span class="tag is-light">{$a.cf}</span>
                            </p>
                            {if isset($a.attivita) && $a.attivita !== ''}
                                <div class="tags mb-0 mt-1">
                                    {assign var="atts" value=explode(',', $a.attivita)}
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
            const gridView = document.getElementById('allenatori-grid');
            const listView = document.getElementById('allenatori-list');
            const btnGrid = document.getElementById('btn-grid');
            const btnList = document.getElementById('btn-list');

            if (viewType === 'grid') {
                gridView.classList.remove('is-hidden');
                listView.classList.add('is-hidden');
                
                btnGrid.classList.add('is-gymfly');
                btnGrid.classList.remove('is-light');
                
                btnList.classList.remove('is-gymfly');
                btnList.classList.add('is-light');
                
                localStorage.setItem('allenatori-view-preference', 'grid');
            } else {
                gridView.classList.add('is-hidden');
                listView.classList.remove('is-hidden');
                
                btnList.classList.add('is-gymfly');
                btnList.classList.remove('is-light');
                
                btnGrid.classList.remove('is-gymfly');
                btnGrid.classList.add('is-light');
                
                localStorage.setItem('allenatori-view-preference', 'list');
            }

            // Ricalcola no-results per la vista attiva
            filterTrainers();
        }

        function filterTrainers() {
            const query = document.getElementById('search-input').value.toLowerCase();
            
            // Filtra in griglia
            const gridItems = document.querySelectorAll('#allenatori-grid .trainer-grid-item');
            let visibleGridCount = 0;
            gridItems.forEach(item => {
                const searchTxt = item.getAttribute('data-search').toLowerCase();
                const attivitaStr = item.getAttribute('data-attivita');
                const attivitaList = attivitaStr ? attivitaStr.split(',') : [];

                const matchesSearch = searchTxt.includes(query);
                const matchesActivity = (activeActivityFilter === 'ALL' || attivitaList.includes(activeActivityFilter));

                if (matchesSearch && matchesActivity) {
                    item.classList.remove('is-hidden');
                    visibleGridCount++;
                } else {
                    item.classList.add('is-hidden');
                }
            });

            // Filtra in lista
            const listItems = document.querySelectorAll('#allenatori-list .trainer-list-item');
            let visibleListCount = 0;
            listItems.forEach(item => {
                const searchTxt = item.getAttribute('data-search').toLowerCase();
                const attivitaStr = item.getAttribute('data-attivita');
                const attivitaList = attivitaStr ? attivitaStr.split(',') : [];

                const matchesSearch = searchTxt.includes(query);
                const matchesActivity = (activeActivityFilter === 'ALL' || attivitaList.includes(activeActivityFilter));

                if (matchesSearch && matchesActivity) {
                    item.classList.remove('is-hidden');
                    visibleListCount++;
                } else {
                    item.classList.add('is-hidden');
                }
            });

            updateNoResultsMessage(visibleGridCount, visibleListCount);
            updateFilterTags();
        }

        function populateActivityFilters() {
            const activities = new Set();
            const gridItems = document.querySelectorAll('#allenatori-grid .trainer-grid-item');
            
            gridItems.forEach(item => {
                const atts = item.getAttribute('data-attivita');
                if (atts) {
                    atts.split(',').forEach(a => {
                        const trimmed = a.trim();
                        if (trimmed) activities.add(trimmed);
                    });
                }
            });

            const container = document.getElementById('activity-filters-container');
            const allFilter = document.getElementById('filter-activity-all');
            container.innerHTML = '';
            container.appendChild(allFilter);

            Array.from(activities).sort().forEach(activity => {
                const link = document.createElement('a');
                link.href = '#';
                link.className = 'dropdown-item';
                link.id = 'filter-activity-' + activity.replace(/\s+/g, '-');
                link.textContent = activity;
                link.onclick = (e) => {
                    e.preventDefault();
                    setActivityFilter(activity);
                };
                container.appendChild(link);
            });
        }

        function setActivityFilter(activity) {
            activeActivityFilter = activity;
            
            const links = document.querySelectorAll('#activity-filters-container .dropdown-item');
            links.forEach(link => link.classList.remove('is-active'));
            
            if (activity === 'ALL') {
                document.getElementById('filter-activity-all').classList.add('is-active');
            } else {
                const targetId = 'filter-activity-' + activity.replace(/\s+/g, '-');
                const targetLink = document.getElementById(targetId);
                if (targetLink) {
                    targetLink.classList.add('is-active');
                }
            }
            
            filterTrainers();
        }

        function resetAllFilters() {
            document.getElementById('search-input').value = '';
            setActivityFilter('ALL');
        }

        function updateFilterTags() {
            const tagsContainer = document.getElementById('active-filters-tags');
            const tagActivity = document.getElementById('tag-activity');
            
            let hasFilters = false;
            
            if (activeActivityFilter !== 'ALL') {
                tagActivity.textContent = activeActivityFilter;
                tagActivity.classList.remove('is-hidden');
                hasFilters = true;
            } else {
                tagActivity.classList.add('is-hidden');
            }
            
            if (hasFilters) {
                tagsContainer.classList.remove('is-hidden');
            } else {
                tagsContainer.classList.add('is-hidden');
            }
        }

        function updateNoResultsMessage(visibleGridCount, visibleListCount) {
            const noResults = document.getElementById('no-results-msg');
            const gridView = document.getElementById('allenatori-grid');
            const listView = document.getElementById('allenatori-list');
            const isGridActive = !gridView.classList.contains('is-hidden');
            const isListActive = !listView.classList.contains('is-hidden');
            
            const count = isGridActive ? visibleGridCount : visibleListCount;
            const hasTotalItems = document.querySelectorAll('.trainer-grid-item').length > 0;
            
            if (count === 0 && hasTotalItems) {
                noResults.classList.remove('is-hidden');
            } else {
                noResults.classList.add('is-hidden');
            }
        }

        function setSortOrder(order) {
            activeSortOrder = order;
            
            document.getElementById('sort-cognome_asc').classList.remove('is-active');
            document.getElementById('sort-cognome_desc').classList.remove('is-active');
            document.getElementById('sort-nome_asc').classList.remove('is-active');
            document.getElementById('sort-nome_desc').classList.remove('is-active');
            
            const activeLink = document.getElementById('sort-' + order);
            if (activeLink) {
                activeLink.classList.add('is-active');
            }
            
            applySort();
        }

        function applySort() {
            // Ordina la griglia
            const gridContainer = document.getElementById('allenatori-grid');
            const gridItems = Array.from(gridContainer.querySelectorAll('.trainer-grid-item'));
            
            gridItems.sort((a, b) => {
                return compareItems(a, b, activeSortOrder);
            });
            
            gridItems.forEach(item => gridContainer.appendChild(item));
            
            // Ordina la lista
            const listContainer = document.getElementById('allenatori-list');
            const listItems = Array.from(listContainer.querySelectorAll('.trainer-list-item'));
            
            listItems.sort((a, b) => {
                return compareItems(a, b, activeSortOrder);
            });
            
            listItems.forEach(item => listContainer.appendChild(item));
        }

        function compareItems(a, b, order) {
            let valA, valB;
            
            if (order.startsWith('cognome')) {
                valA = (a.getAttribute('data-cognome') || '').toLowerCase();
                valB = (b.getAttribute('data-cognome') || '').toLowerCase();
            } else {
                valA = (a.getAttribute('data-nome') || '').toLowerCase();
                valB = (b.getAttribute('data-nome') || '').toLowerCase();
            }
            
            if (order.endsWith('asc')) {
                return valA.localeCompare(valB);
            } else {
                return valB.localeCompare(valA);
            }
        }

        // Ripristina preferenza
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
