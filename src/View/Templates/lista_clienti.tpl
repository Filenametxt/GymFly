<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Lista Clienti</title>
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
                    <h1 class="title is-2 style-theme-text mb-0 mr-3">Gestione utenti - Visualizzazione Clienti</h1>
                    {if isset($filtro_certificato) && $filtro_certificato !== null}
                        {if $filtro_certificato === 'scaduti'}
                            <span class="tag is-danger is-medium font-weight-bold mr-2">Filtro: Certificati Scaduti / Assenti</span>
                        {elseif $filtro_certificato === 'in_scadenza'}
                            <span class="tag is-warning is-medium font-weight-bold mr-2">Filtro: Certificati in Scadenza</span>
                        {elseif $filtro_certificato === 'in_regola'}
                            <span class="tag is-success is-medium font-weight-bold mr-2">Filtro: Certificati in Regola</span>
                        {/if}
                        <a href="clienti" class="button is-small is-light" style="border-radius: 8px;">
                            <span class="icon is-small"><i class="fas fa-times"></i></span>
                            <span>Mostra Tutti</span>
                        </a>
                    {/if}
                </div>
                <p class="subtitle is-6 has-text-grey">Visualizza e gestisci le schede anagrafiche dei clienti della palestra</p>
            </div>

            <!-- CONTROLS / TOOLBAR -->
            <div class="is-flex is-justify-content-between is-align-items-center mb-5 is-flex-wrap-wrap" style="gap: 15px;">
                <div class="buttons mb-0">
                    <a href="crea-cliente" class="button is-gymfly mr-2">
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
                    <form action="clienti" method="POST">
                        <div class="field">
                            <div class="control has-icons-left">
                                <input class="input" type="text" name="search_query" placeholder="Search" value="{if isset($smarty.post.search_query)}{$smarty.post.search_query}{/if}">
                                <span class="icon is-left">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CARDS GRID VIEW -->
            <div id="clienti-grid" class="columns is-multiline">
                {foreach $clienti as $c}
                    <div class="column is-3-desktop is-4-tablet is-12-mobile">
                        <a href="visualizza-profilo?id={$c.id}" class="box customer-card">
                            <div class="customer-avatar mb-3">
                                <span class="icon is-large">
                                    <i class="fas fa-user-circle fa-4x"></i>
                                </span>
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
                        <div class="customer-avatar mr-4">
                            <span class="icon is-medium">
                                <i class="fas fa-user-circle fa-2x"></i>
                            </span>
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
            const gridView = document.getElementById('clienti-grid');
            const listView = document.getElementById('clienti-list');
            const btnGrid = document.getElementById('btn-grid');
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
