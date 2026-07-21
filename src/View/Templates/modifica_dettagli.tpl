<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Modifica Allenamento</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ESERCIZIO BLOCK */
        .esercizio-block {
            background: var(--gymfly-bg);
            border: 1px solid rgba(175, 175, 226, 0.15);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .esercizio-titolo {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gymfly-text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .esercizio-serie-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: var(--gymfly-accent);
            color: var(--gymfly-text);
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* PARAMETRI GRID */
        .parametri-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 640px) {
            .parametri-grid {
                grid-template-columns: 1fr;
            }
        }

        .parametro-field {
            display: flex;
            flex-direction: column;
        }

        .parametro-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #7f8794;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.3rem;
        }

        /* SEZIONE VUOTA */
        .esercizi-vuoti {
            text-align: center;
            padding: 2rem 1rem;
            color: #9ca3af;
            font-size: 0.95rem;
        }

        .esercizi-vuoti i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.4;
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            <div class="container">

                <!-- TORNA INDIETRO (Desktop) -->
                <div class="mb-5 is-hidden-mobile">
                    <a href="visualizza-scheda" class="button is-ghost has-text-grey pl-0">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Torna alla Scheda</span>
                    </a>
                </div>

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
                            <h1 class="title is-2 has-text-white mb-2">Modifica Allenamento</h1>
                            <p class="subtitle is-5 has-text-white-ter">Aggiorna ripetizioni, carico e tempi di recupero per ciascun esercizio dell'allenamento "{$allenamento->getNome()}"</p>
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
                    <a href="visualizza-scheda" class="button is-ghost p-0 mr-3" style="color: inherit; height: auto;">
                        <span class="icon is-medium"><i class="fas fa-arrow-left fa-lg"></i></span>
                    </a>
                    <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px; flex-grow: 1;">MODIFICA ALLENAMENTO</strong>
                </div>

                <!-- FORM DI MODIFICA ESTESO -->
                <div class="columns">
                    <div class="column is-12">
                        <div class="box p-5">
                            <h3 class="title is-5 mb-5 style-theme-text">
                                <i class="fas fa-edit mr-2" style="color: var(--gymfly-primary);"></i> Dettagli Allenamento
                            </h3>
                            
                            <form action="modifica-dettagli" method="POST">
                                <input type="hidden" name="id_allenamento" value="{$allenamento->getId()}">

                                <!-- ESERCIZI -->
                                {if $allenamento->getDettagliOrdinati()|@count > 0}
                                    {foreach $allenamento->getDettagliOrdinati() as $dettaglio}
                                        <div class="esercizio-block">
                                            <div class="columns is-mobile is-vcentered">
                                                {if $dettaglio->getEsercizio()->getImmagine()}
                                                    <div class="column is-narrow">
                                                        <figure class="image is-96x96" style="border-radius: 8px; overflow: hidden; border: 1px solid var(--gymfly-accent); background-color: #f5f5f5;">
                                                            <img src="data:{if $dettaglio->getEsercizio()->getTipoImmagine()}{$dettaglio->getEsercizio()->getTipoImmagine()}{else}image/jpeg{/if};base64,{$dettaglio->getEsercizio()->getImmagine()|base64_encode}" alt="Esercizio" style="object-fit: cover; width: 100%; height: 100%;">
                                                        </figure>
                                                    </div>
                                                {/if}
                                                <div class="column">
                                                    <!-- TITOLO ESERCIZIO -->
                                                    <div class="esercizio-titolo">
                                                        <span class="esercizio-serie-badge">S{$dettaglio->getSerie()}</span>
                                                        <span class="has-text-weight-bold">{$dettaglio->getEsercizio()->getNomeEsercizio()}</span>
                                                    </div>

                                                    <!-- PARAMETRI IN GRID -->
                                                    {assign var="nomeTipo" value=$dettaglio->getEsercizio()->getTipologia()->getNomeTipologia()|lower}
                                                    {assign var="isDurata" value=($nomeTipo === 'durata')}
                                                    <div class="parametri-grid">
                                                        <!-- RIPETIZIONI -->
                                                        <div class="parametro-field" {if $isDurata}style="opacity: 0.6;"{/if}>
                                                            <label class="parametro-label">
                                                                <i class="fas fa-sync-alt mr-1"></i>Ripetizioni
                                                            </label>
                                                            <input 
                                                                class="input" 
                                                                type="number" 
                                                                name="dettagli[{$dettaglio->getId()}][ripetizioni]" 
                                                                value="{$dettaglio->getRipetizioni()}" 
                                                                {if !$isDurata}required min="1"{else}disabled{/if}
                                                                placeholder="Es: 10"
                                                                style="border-radius: 8px;">
                                                        </div>

                                                        <!-- CARICO -->
                                                        <div class="parametro-field">
                                                            <label class="parametro-label">
                                                                <i class="fas fa-weight mr-1"></i>Carico (Kg)
                                                            </label>
                                                            <input 
                                                                class="input" 
                                                                type="number" 
                                                                step="0.5" 
                                                                name="dettagli[{$dettaglio->getId()}][carico]" 
                                                                value="{$dettaglio->getCarico()}" 
                                                                required 
                                                                min="0"
                                                                placeholder="Es: 20"
                                                                style="border-radius: 8px;">
                                                        </div>

                                                        <!-- TEMPO -->
                                                        <div class="parametro-field" {if !$isDurata}style="opacity: 0.6;"{/if}>
                                                            <label class="parametro-label">
                                                                <i class="fas fa-stopwatch mr-1"></i>Tempo
                                                            </label>
                                                            <input 
                                                                class="input" 
                                                                type="text" 
                                                                name="dettagli[{$dettaglio->getId()}][tempo]" 
                                                                value="{$dettaglio->getTempo()}" 
                                                                {if $isDurata}required{else}disabled{/if}
                                                                placeholder="Es: 90s"
                                                                style="border-radius: 8px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    {/foreach}
                                {else}
                                    <div class="esercizi-vuoti">
                                        <div>
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <p>Nessun esercizio presente in questo allenamento.</p>
                                    </div>
                                {/if}

                                <!-- SUBMIT -->
                                <div class="field mt-5">
                                    <div class="control">
                                        <button type="submit" class="button is-gymfly is-fullwidth">
                                            <span class="icon"><i class="fas fa-save"></i></span>
                                            <span>Salva Modifiche</span>
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
