<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Lista Esercizi</title>
    <link class="css-link" rel="stylesheet" href="css/bulma.min.css">
    <link class="css-link" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link class="css-link" rel="stylesheet" href="css/style.css?v=1.2">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- ================= DESKTOP HEADER ================= -->
            <div class="dashboard-header-trainer is-hidden-mobile">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-2 has-text-white mb-2">
                            Gestione Esercizi
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Visualizza, cerca e gestisci gli esercizi del catalogo della palestra
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-dumbbell fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">ESERCIZI</strong>
            </div>

            <!-- CONTROLS / TOOLBAR -->
            <div class="is-flex is-justify-content-between is-align-items-center mb-5 is-flex-wrap-wrap" style="gap: 15px;">
                <div class="buttons mb-0">
                    <a href="crea-esercizio" class="button is-gymfly mr-3">
                        <span>+ Nuovo Esercizio</span>
                    </a>
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
                        <form action="esercizi" method="POST">
                            <div class="field mb-0">
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="search_query" placeholder="Search" value="{if isset($search_query)}{$search_query}{/if}">
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
            <div id="esercizi-grid" class="columns is-multiline">
                {foreach $esercizi as $e}
                    <div class="column is-3-desktop is-4-tablet is-12-mobile">
                        <a href="visualizza-esercizio?id={$e.id}" class="box customer-card" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div class="mb-3" style="width: 100%; height: 140px; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f5f5f5;">
                                    {if isset($e.immagine) && $e.immagine !== null}
                                        <img src="data:{if isset($e.immagine_type)}{$e.immagine_type}{else}image/jpeg{/if};base64,{$e.immagine}" alt="Foto Esercizio" style="width: 100%; height: 100%; object-fit: cover;">
                                    {else}
                                        <span class="icon is-large has-text-grey-light">
                                            <i class="fas fa-dumbbell fa-3x"></i>
                                        </span>
                                    {/if}
                                </div>
                                <h3 class="title is-5 mb-2 has-text-centered">{$e.nome}</h3>
                                <p class="subtitle is-7 has-text-centered has-text-weight-semibold tag is-light is-rounded mb-2" style="display: block; width: fit-content; margin: 0 auto;">{$e.tipologia}</p>
                                <p class="is-size-7 has-text-grey-dark mb-1"><strong>Attrezzatura:</strong> {$e.attrezzatura}</p>
                                <p class="is-size-7 has-text-grey-dark"><strong>Gruppi:</strong> {$e.gruppiMuscolari|default:'Nessuno'}</p>
                            </div>
                            <div class="mt-3">
                                <span class="button is-small is-gymfly is-fullwidth">Dettagli</span>
                            </div>
                        </a>
                    </div>
                {foreachelse}
                    <div class="column is-12">
                        <div class="box has-text-centered py-6">
                            <span class="icon is-large mb-3 has-text-grey"><i class="fas fa-search fa-3x"></i></span>
                            <p class="has-text-grey is-size-5">Nessun esercizio corrispondente ai criteri di ricerca.</p>
                        </div>
                    </div>
                {/foreach}
            </div>

            <!-- LIST VIEW -->
            <div id="esercizi-list" class="is-hidden">
                {foreach $esercizi as $e}
                    <a href="visualizza-esercizio?id={$e.id}" class="box customer-list-item mb-3">
                        <div class="mr-4" style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f5f5f5; flex-shrink: 0;">
                            {if isset($e.immagine) && $e.immagine !== null}
                                <img src="data:{if isset($e.immagine_type)}{$e.immagine_type}{else}image/jpeg{/if};base64,{$e.immagine}" alt="Foto Esercizio" style="width: 100%; height: 100%; object-fit: cover;">
                            {else}
                                <span class="icon is-medium has-text-grey-light">
                                    <i class="fas fa-dumbbell fa-lg"></i>
                                </span>
                            {/if}
                        </div>
                        <div class="is-flex-grow-1">
                            <h3 class="title is-5 mb-1 style-theme-text">{$e.nome} <span class="tag is-light is-size-7" style="margin-left: 8px;">{$e.tipologia}</span></h3>
                            <p class="subtitle is-6 has-text-grey-dark mb-0">
                                Attrezzatura: <strong>{$e.attrezzatura}</strong> &bull; Gruppi: <strong>{$e.gruppiMuscolari|default:'Nessuno'}</strong>
                            </p>
                        </div>
                        <div>
                            <span class="icon has-text-grey-light"><i class="fas fa-chevron-right"></i></span>
                        </div>
                    </a>
                {foreachelse}
                    <div class="box has-text-centered py-6">
                        <span class="icon is-large mb-3 has-text-grey"><i class="fas fa-search fa-3x"></i></span>
                        <p class="has-text-grey is-size-5">Nessun esercizio corrispondente ai criteri di ricerca.</p>
                    </div>
                {/foreach}
            </div>

        </main>
    </div>

    <!-- SCRIPT PER TOGGLE VISUALIZZAZIONE -->
    <script>
        function switchView(viewType) {
            const gridView = document.getElementById('esercizi-grid');
            const listView = document.getElementById('esercizi-list');
            const btnGrid = document.getElementById('btn-grid');
            const btnList = document.getElementById('btn-list');

            if (viewType === 'grid') {
                gridView.classList.remove('is-hidden');
                listView.classList.add('is-hidden');
                
                btnGrid.classList.add('is-gymfly');
                btnGrid.classList.remove('is-light');
                
                btnList.classList.remove('is-gymfly');
                btnList.classList.add('is-light');
                
                localStorage.setItem('esercizi-view-preference', 'grid');
            } else {
                gridView.classList.add('is-hidden');
                listView.classList.remove('is-hidden');
                
                btnList.classList.add('is-gymfly');
                btnList.classList.remove('is-light');
                
                btnGrid.classList.remove('is-gymfly');
                btnGrid.classList.add('is-light');
                
                localStorage.setItem('esercizi-view-preference', 'list');
            }
        }

        // Ripristina preferenza
        document.addEventListener('DOMContentLoaded', () => {
            const pref = localStorage.getItem('esercizi-view-preference') || 'grid';
            switchView(pref);
        });
    </script>

</body>
</html>
