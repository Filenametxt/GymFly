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
            padding: 1.5rem;
        }

        /* HEADER ALLENAMENTO */
        .allenamento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
            border-bottom: 2px solid var(--gymfly-primary);
            margin-bottom: 2rem;
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
            background: var(--gymfly-card-bg);
            border: 2px solid var(--gymfly-primary);
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.25rem;
            transition: box-shadow 0.2s ease;
        }

        .esercizio-block:hover {
            box-shadow: 0 6px 16px rgba(175, 175, 226, 0.12);
        }

        .esercizio-titolo {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gymfly-text);
            margin-bottom: 1.25rem;
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
            color: #1e3a8a;
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
            font-size: 0.8rem;
            font-weight: 600;
            color: #7f8794;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.5rem;
        }

        .parametro-input {
            background: var(--gymfly-bg);
            border: 1.5px solid var(--gymfly-primary);
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1rem;
            color: var(--gymfly-text);
            font-weight: 500;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .parametro-input:focus {
            border-color: var(--gymfly-secondary);
            box-shadow: 0 0 0 3px rgba(153, 205, 234, 0.1);
            outline: none;
        }

        /* PULSANTE SALVA */
        .btn-salva-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .btn-salva {
            background: var(--gymfly-primary);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 1rem 2rem;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(175, 175, 226, 0.24);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-salva:hover {
            background: var(--gymfly-secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(175, 175, 226, 0.32);
            color: white;
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

        /* BACK LINK */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gymfly-primary);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: var(--gymfly-secondary);
        }
    </style>
</head>
<body>

    <div class="modifica-container">

        <!-- BACK LINK -->
        <a href="visualizza-scheda" class="back-link">
            <i class="fas fa-arrow-left"></i>
            <span>Torna alla Scheda</span>
        </a>

        <!-- HEADER ALLENAMENTO -->
        <div class="allenamento-header">
            <h1 class="allenamento-titolo">
                <i class="fas fa-running mr-2"></i>{$allenamento->getNome()}
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
                        <div class="parametri-grid">
                            <!-- RIPETIZIONI -->
                            <div class="parametro-field">
                                <label class="parametro-label">
                                    <i class="fas fa-sync-alt mr-1"></i>Ripetizioni
                                </label>
                                <input 
                                    class="parametro-input" 
                                    type="number" 
                                    name="dettagli[{$dettaglio->getId()}][ripetizioni]" 
                                    value="{$dettaglio->getRipetizioni()}" 
                                    required 
                                    min="1"
                                    placeholder="Es: 10">
                            </div>

                            <!-- CARICO -->
                            <div class="parametro-field">
                                <label class="parametro-label">
                                    <i class="fas fa-weight mr-1"></i>Carico (Kg)
                                </label>
                                <input 
                                    class="parametro-input" 
                                    type="number" 
                                    step="0.5" 
                                    name="dettagli[{$dettaglio->getId()}][carico]" 
                                    value="{$dettaglio->getCarico()}" 
                                    required 
                                    min="0"
                                    placeholder="Es: 20">
                            </div>

                            <!-- RECUPERO -->
                            <div class="parametro-field">
                                <label class="parametro-label">
                                    <i class="fas fa-stopwatch mr-1"></i>Recupero
                                </label>
                                <input 
                                    class="parametro-input" 
                                    type="text" 
                                    name="dettagli[{$dettaglio->getId()}][recupero]" 
                                    value="{$allenamento->getDescrizione()|estrai_recupero:$dettaglio->getEsercizio()->getNomeEsercizio():$dettaglio->getSerie():$dettaglio->getId()}" 
                                    placeholder="Es: 90s">
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
            <div class="btn-salva-wrapper">
                <button type="submit" class="btn-salva">
                    <i class="fas fa-check"></i>
                    <span>SALVA MODIFICHE</span>
                </button>
            </div>

        </form>

    </div>

</body>
</html>
