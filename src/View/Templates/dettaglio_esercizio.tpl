<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Dettaglio Esercizio</title>
    <link class="css-link" rel="stylesheet" href="css/bulma.min.css">
    <link class="css-link" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link class="css-link" rel="stylesheet" href="css/style.css?v=1.2">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- Back link -->
            <div class="mb-4">
                <a href="esercizi" class="button is-ghost has-text-grey pl-0">
                    <span class="icon"><i class="fas fa-chevron-left"></i></span>
                    <span>Torna alla lista esercizi</span>
                </a>
            </div>

            <!-- ================= DESKTOP HEADER ================= -->
            <div class="dashboard-header-trainer is-hidden-mobile mb-5">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-2 has-text-white mb-2">
                            {$nome}
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Dettagli e scheda tecnica dell'esercizio
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
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">DETTAGLI ESERCIZIO</strong>
            </div>

            <!-- GRID A COLONNE (Side-by-side su Desktop, impilato su Mobile) -->
            <div class="columns">
                
                <!-- COLONNA IMMAGINE (Sinistra) -->
                <div class="column is-12-mobile is-5-desktop">
                    <div class="box" style="height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 300px;">
                        <h3 class="title is-5 mb-4 style-theme-text">Immagine Esecuzione</h3>
                        <div class="image-container" style="width: 100%; max-width: 320px; aspect-ratio: 1/1; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f5f5f5; border: 1px solid #dbdbdb;">
                            {if isset($immagine) && $immagine !== null}
                                <img src="data:{if isset($immagine_type)}{$immagine_type}{else}image/jpeg{/if};base64,{$immagine}" alt="Esecuzione Esercizio" style="width: 100%; height: 100%; object-fit: cover;">
                            {else}
                                <div class="has-text-centered has-text-grey-light">
                                    <span class="icon is-large"><i class="fas fa-dumbbell fa-4x"></i></span>
                                    <p class="is-size-7 mt-2">Nessuna immagine disponibile</p>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>

                <!-- COLONNA DETTAGLI (Destra) -->
                <div class="column is-12-mobile is-7-desktop">
                    <div class="box" style="height: 100%;">
                        <h3 class="title is-4 mb-4 style-theme-text">
                            <i class="fas fa-info-circle mr-2" style="color: var(--gymfly-primary);"></i> Informazioni Generali
                        </h3>

                        <div class="content style-theme-text" style="font-size: 1.1rem; line-height: 1.6;">
                            <div class="columns is-multiline">
                                <div class="column is-12">
                                    <p><strong>Nome Esercizio:</strong><br><span class="is-size-5">{$nome}</span></p>
                                </div>
                                <div class="column is-6">
                                    <p><strong>Tipologia:</strong><br><span class="tag is-info is-light is-medium">{$tipologia}</span></p>
                                </div>
                                <div class="column is-6">
                                    <p><strong>Attrezzatura Necessaria:</strong><br><span class="tag is-success is-light is-medium">{$attrezzatura}</span></p>
                                </div>
                                <div class="column is-12">
                                    <p><strong>Gruppi Muscolari Coinvolti:</strong><br>
                                        {if $gruppiMuscolari}
                                            <span class="tag is-link is-light is-medium">{$gruppiMuscolari}</span>
                                        {else}
                                            <span class="has-text-grey">Nessuno</span>
                                        {/if}
                                    </p>
                                </div>
                                <div class="column is-12">
                                    <p><strong>Creatore:</strong><br><span class="has-text-weight-semibold">{$creatore}</span></p>
                                </div>
                                <div class="column is-12">
                                    <p><strong>Descrizione ed Esecuzione:</strong></p>
                                    <div class="p-3 style-theme-text" style="background-color: var(--gymfly-bg); border-radius: 8px; border-left: 4px solid var(--gymfly-primary); min-height: 100px; white-space: pre-wrap;">{$descrizione|default:'Nessuna descrizione specificata.'}</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </main>
    </div>

</body>
</html>
