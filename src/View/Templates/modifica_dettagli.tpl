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
        .modifica-container {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        /* HEADER ALLENAMENTO */
        .allenamento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--gymfly-accent);
            margin-bottom: 1.5rem;
        }

        .allenamento-titolo {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gymfly-text);
            letter-spacing: 0.5px;
        }

        .allenamento-icon {
            font-size: 1.5rem;
            color: var(--gymfly-primary);
        }

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
            <div class="container modifica-container">

                <!-- BACK LINK -->
                <div class="mb-4">
                    <a href="visualizza-scheda" class="button is-ghost has-text-grey pl-0">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Torna alla Scheda</span>
                    </a>
                </div>

                <div class="box p-5" style="border: 2px solid var(--gymfly-accent); border-radius: 15px; background-color: var(--gymfly-card-bg); box-shadow: 0 8px 16px rgba(0,0,0,0.02) !important;">
                    
                    <!-- HEADER ALLENAMENTO -->
                    <div class="allenamento-header">
                        <h1 class="allenamento-titolo">
                            <i class="fas fa-running mr-2" style="color: var(--gymfly-primary);"></i>{$allenamento->getNome()}
                        </h1>
                        <div class="allenamento-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                    </div>

                    <!-- FORM -->
                    <form action="modifica-dettagli" method="POST">
                        <input type="hidden" name="id_allenamento" value="{$allenamento->getId()}">

                        <!-- ESERCIZI -->
                        {if $allenamento->getDettagli()|@count > 0}
                            {foreach $allenamento->getDettagli() as $dettaglio}
                                <div class="esercizio-block">
                                    <!-- TITOLO ESERCIZIO -->
                                    <div class="esercizio-titolo">
                                        <span class="esercizio-serie-badge">S{$dettaglio->getSerie()}</span>
                                        <span>{$dettaglio->getEsercizio()->getNomeEsercizio()}</span>
                                    </div>

                                    <!-- PARAMETRI IN GRID -->
                                    {assign var="nomeTipo" value=$dettaglio->getEsercizio()->getTipologia()->getNomeTipologia()|lower}
                                    {assign var="isDurata" value=($nomeTipo === 'durata' || $nomeTipo === 'tempo/ripetizioni')}
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
                            {/foreach}
                        {else}
                            <div class="esercizi-vuoti">
                                <div>
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <p>Nessun esercizio presente in questo allenamento.</p>
                            </div>
                        {/if}

                        <!-- PULSANTE SALVA -->
                        <button type="submit" class="button is-gymfly is-fullwidth mt-5" style="border-radius: 10px;">
                            <i class="fas fa-check mr-2"></i>
                            <span>Salva Modifiche</span>
                        </button>

                    </form>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
