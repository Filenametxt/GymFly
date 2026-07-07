<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Lista Allenatori</title>
    <link class="css-link" rel="stylesheet" href="css/bulma.min.css">
    <link class="css-link" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link class="css-link" rel="stylesheet" href="css/style.css?v=1.4">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- HEADER -->
            <div class="mb-5">
                <h1 class="title is-2 style-theme-text mb-2">Gestione utenti - Supervisione Allenatori</h1>
                <p class="subtitle is-6 has-text-grey">Elenco dei preparatori atletici abilitati per la palestra</p>
            </div>

            <!-- CONTROLS / TOOLBAR -->
            <div class="is-flex is-justify-content-between is-align-items-center mb-5 is-flex-wrap-wrap" style="gap: 15px;">
                <div class="buttons mb-0">
                    <a href="#" class="button is-gymfly mr-2" onclick="alert('La registrazione dei singoli allenatori è in fase di implementazione.'); return false;">
                        <span>+ Nuovo</span>
                    </a>
                    <button class="button is-light mr-1" onclick="alert('Funzione di filtro in fase di sviluppo.'); return false;">
                        <span class="icon"><i class="fas fa-filter"></i></span>
                        <span>filtra</span>
                    </button>
                    <button class="button is-light mr-3" onclick="alert('Funzione di ordinamento in fase di sviluppo.'); return false;">
                        <span class="icon"><i class="fas fa-sort"></i></span>
                        <span>ordina</span>
                    </button>
                    <!-- Toggles per Vista Griglia / Lista -->
                    <div class="field has-addons">
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
                </div>
                
                <div class="search-container" style="flex-grow: 1; max-width: 300px; width: 100%;">
                    <div class="field">
                        <div class="control has-icons-left">
                            <input id="search-input" class="input" type="text" placeholder="Search" oninput="filterTrainers()">
                            <span class="icon is-left">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARDS GRID VIEW -->
            <div id="allenatori-grid" class="columns is-multiline">
                {foreach $allenatori as $a}
                    <div class="column is-3-desktop is-4-tablet is-12-mobile trainer-grid-item" data-search="{$a.nome} {$a.cognome} {$a.email} {$a.cf}">
                        <a href="visualizza-profilo?id={$a.id}" class="box customer-card">
                            <div class="customer-avatar mb-3" style="color: var(--gymfly-secondary);">
                                <span class="icon is-large">
                                    <i class="fas fa-user-ninja fa-4x"></i>
                                </span>
                            </div>
                            <h3 class="title is-5 mb-2 has-text-centered">{$a.nome} {$a.cognome}</h3>
                            <p class="subtitle is-6 has-text-grey-dark mb-1 has-text-centered" style="word-break: break-all;">{$a.email}</p>
                            <p class="is-size-7 has-text-grey has-text-centered">{$a.cf}</p>
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
                    <a href="visualizza-profilo?id={$a.id}" class="box customer-list-item mb-3 trainer-list-item" data-search="{$a.nome} {$a.cognome} {$a.email} {$a.cf}">
                        <div class="customer-avatar mr-4" style="color: var(--gymfly-secondary);">
                            <span class="icon is-medium">
                                <i class="fas fa-user-ninja fa-2x"></i>
                            </span>
                        </div>
                        <div class="is-flex-grow-1">
                            <h3 class="title is-5 mb-1 style-theme-text">{$a.nome} {$a.cognome}</h3>
                            <p class="subtitle is-6 has-text-grey-dark mb-0" style="word-break: break-all;">
                                {$a.email} &bull; <span class="tag is-light">{$a.cf}</span>
                            </p>
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
        }

        function filterTrainers() {
            const query = document.getElementById('search-input').value.toLowerCase();
            
            // Filtra in griglia
            const gridItems = document.querySelectorAll('#allenatori-grid .trainer-grid-item');
            gridItems.forEach(item => {
                const text = item.getAttribute('data-search').toLowerCase();
                if (text.includes(query)) {
                    item.classList.remove('is-hidden');
                } else {
                    item.classList.add('is-hidden');
                }
            });

            // Filtra in lista
            const listItems = document.querySelectorAll('#allenatori-list .trainer-list-item');
            listItems.forEach(item => {
                const text = item.getAttribute('data-search').toLowerCase();
                if (text.includes(query)) {
                    item.classList.remove('is-hidden');
                } else {
                    item.classList.add('is-hidden');
                }
            });
        }

        // Ripristina preferenza
        document.addEventListener('DOMContentLoaded', () => {
            const pref = localStorage.getItem('allenatori-view-preference') || 'grid';
            switchView(pref);
        });
    </script>

</body>
</html>
