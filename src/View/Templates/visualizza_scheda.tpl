<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - La mia Scheda</title>
    <link class="sheet" rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .custom-mobile-container {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        /* ACCORDION STYLES */
        .accordion-item {
            border: 2px solid var(--gymfly-accent);
            border-radius: 15px;
            margin-bottom: 1.25rem;
            overflow: hidden;
            transition: all 0.2s ease;
            background: var(--gymfly-card-bg);
        }

        .accordion-item:hover {
            box-shadow: 0 8px 16px rgba(175, 175, 226, 0.06);
        }

        .accordion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            cursor: pointer;
            user-select: none;
            transition: background-color 0.2s ease;
            background: var(--gymfly-card-bg);
        }

        .accordion-header:hover {
            background: rgba(197, 224, 252, 0.1);
        }

        .accordion-header-content {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }

        .accordion-icon {
            font-size: 1.3rem;
            color: var(--gymfly-primary);
        }

        .accordion-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gymfly-text);
            margin: 0;
        }

        .accordion-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: var(--gymfly-accent);
            border-radius: 50%;
            color: var(--gymfly-text);
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }

        .accordion-item.active .accordion-toggle {
            transform: rotate(180deg);
        }

        .accordion-content {
            display: none;
            padding: 0 1.5rem 1.5rem 1.5rem;
            border-top: 1px solid var(--gymfly-accent);
            animation: slideDown 0.3s ease;
        }

        .accordion-item.active .accordion-content {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* EXERCISES LIST */
        .exercises-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .exercise-card {
            background: var(--gymfly-bg);
            border: 1px solid var(--gymfly-accent);
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.2s ease;
        }

        .exercise-card:hover {
            border-color: var(--gymfly-primary);
            box-shadow: 0 4px 12px rgba(175, 175, 226, 0.06);
        }

        .exercise-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--gymfly-text);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .exercise-serie-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: var(--gymfly-accent);
            color: var(--gymfly-text);
            border-radius: 50%;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .exercise-params {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.75rem;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
        }

        @media (max-width: 500px) {
            .exercise-params {
                grid-template-columns: 1fr;
            }
        }

        .param-box {
            background: white;
            border: 1px solid rgba(175, 175, 226, 0.15);
            border-radius: 8px;
            padding: 0.5rem;
            text-align: center;
        }

        .param-label {
            font-size: 0.7rem;
            color: #7f8794;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .param-value {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--gymfly-text);
        }

        .exercises-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .btn-edit-allenamento {
            flex: 1;
            background: var(--gymfly-primary);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-edit-allenamento:hover {
            background: var(--gymfly-secondary);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(175, 175, 226, 0.2);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            <div class="container custom-mobile-container">
                
                <!-- BACK LINK -->
                <div class="mb-4">
                    <a href="dashboard-cliente" class="button is-ghost has-text-grey pl-0">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Torna alla Dashboard</span>
                    </a>
                </div>

                <!-- HEADER SCHEDA -->
                <div class="box p-5 mb-5" style="border: 2px solid var(--gymfly-accent); border-radius: 15px; background-color: var(--gymfly-card-bg); box-shadow: 0 8px 16px rgba(0,0,0,0.02) !important;">
                    <div class="mb-3">
                        <span class="tag is-success is-light mb-2">
                            <i class="fas fa-check-circle mr-1"></i> SCHEDA ATTIVA
                        </span>
                    </div>
                    <h1 class="title is-2 style-theme-text mb-2">{$scheda->getNome_scheda()}</h1>
                    <p class="subtitle is-6 has-text-grey-dark mb-4">
                        <strong>Obiettivo:</strong> {$scheda->getObiettivo()}
                    </p>
                    <div class="is-size-7 has-text-grey mb-4">
                        <p><strong>Coach:</strong> {$scheda->getAllenatore()->getNome()} {$scheda->getAllenatore()->getCognome()}</p>
                        <p><strong>Validità:</strong> {$scheda->getData_inizio()->format('d/m/Y')} — {$scheda->getData_fine()->format('d/m/Y')}</p>
                    </div>
                </div>

                <!-- ALLENAMENTI ACCORDION -->
                <div>
                    <h2 class="title is-5 style-theme-text mb-3">
                        <i class="fas fa-running mr-2"></i> I tuoi Allenamenti
                    </h2>

                    {if $scheda->getAllenamenti()|@count > 0}
                        {foreach $scheda->getAllenamenti() as $idx => $allenamento}
                            <div class="accordion-item" data-allenamento-id="{$allenamento->getId()}">
                                <!-- ACCORDION HEADER -->
                                <div class="accordion-header" onclick="toggleAccordion(this)">
                                    <div class="accordion-header-content">
                                        <span class="accordion-icon">
                                            <i class="fas fa-dumbbell"></i>
                                        </span>
                                        <h3 class="accordion-title">{$allenamento->getNome()}</h3>
                                    </div>
                                    <div class="accordion-toggle">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>

                                <!-- ACCORDION CONTENT -->
                                <div class="accordion-content">
                                    {if $allenamento->getDescrizione()|pulisci_descrizione}
                                        <p class="subtitle is-6 has-text-grey mb-3" style="font-size: 0.85rem;">
                                            <i class="fas fa-note-sticky mr-1"></i> {$allenamento->getDescrizione()|pulisci_descrizione}
                                        </p>
                                    {/if}

                                    <!-- EXERCISES LIST -->
                                    {if $allenamento->getDettagli()|@count > 0}
                                        <div class="exercises-list">
                                            {foreach $allenamento->getDettagli() as $dettaglio}
                                                <div class="exercise-card">
                                                    <!-- EXERCISE NAME -->
                                                    <div class="exercise-name">
                                                        <span class="exercise-serie-badge">S{$dettaglio->getSerie()}</span>
                                                        <span>{$dettaglio->getEsercizio()->getNomeEsercizio()}</span>
                                                    </div>

                                                    <!-- EXERCISE PARAMS -->
                                                    <div class="exercise-params">
                                                        <div class="param-box">
                                                            <div class="param-label">
                                                                <i class="fas fa-redo mr-1"></i> Ripetizioni
                                                            </div>
                                                            <div class="param-value">{$dettaglio->getRipetizioni()}</div>
                                                        </div>
                                                        <div class="param-box">
                                                            <div class="param-label">
                                                                <i class="fas fa-weight mr-1"></i> Carico
                                                            </div>
                                                            <div class="param-value">{$dettaglio->getCarico()} Kg</div>
                                                        </div>
                                                        <div class="param-box">
                                                            <div class="param-label">
                                                                <i class="fas fa-stopwatch mr-1"></i> Recupero
                                                            </div>
                                                            <div class="param-value">{$allenamento->getDescrizione()|estrai_recupero:$dettaglio->getEsercizio()->getNomeEsercizio():$dettaglio->getSerie():$dettaglio->getId()}</div>
                                                        </div>
                                                    </div>

                                                    <!-- EXERCISE IMAGE (optional) -->
                                                    {if $dettaglio->getEsercizio()->getImmagine()}
                                                        <div style="margin-top: 0.75rem; text-align: center;">
                                                            <figure class="image is-96x96 is-inline-block" style="border-radius: 8px; overflow: hidden; border: 1px solid var(--gymfly-accent);">
                                                                <img src="data:image/jpeg;base64,{$dettaglio->getEsercizio()->getImmagine()|base64_encode}" alt="Esercizio" style="object-fit: cover; width: 100%; height: 100%;">
                                                            </figure>
                                                        </div>
                                                    {/if}
                                                </div>
                                            {/foreach}
                                        </div>

                                        <!-- EDIT BUTTON -->
                                        <div class="exercises-actions">
                                            <a href="modifica-dettagli?id_allenamento={$allenamento->getId()}" class="btn-edit-allenamento">
                                                <i class="fas fa-edit"></i>
                                                <span>Modifica Dettagli</span>
                                            </a>
                                        </div>
                                    {else}
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <p>Nessun esercizio in questo allenamento.</p>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        {/foreach}
                    {else}
                        <div class="box has-text-centered py-5" style="border: 1px solid var(--gymfly-accent); border-radius: 15px;">
                            <i class="fas fa-file-invoice" style="font-size: 2rem; color: #d0d0d0; margin-bottom: 1rem; display: block;"></i>
                            <p class="has-text-grey" style="font-size: 0.95rem;">Nessun allenamento presente in questa scheda.</p>
                        </div>
                    {/if}
                </div>

            </div>
        </main>
    </div>

    <!-- SCRIPT ACCORDION GESTIONE -->
    <script>
        function toggleAccordion(headerElement) {
            const accordionItem = headerElement.parentElement;
            accordionItem.classList.toggle('active');
        }
    </script>
</body>
</html>
