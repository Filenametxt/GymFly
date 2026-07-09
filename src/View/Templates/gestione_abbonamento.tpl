<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Gestione Abbonamento Cliente</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="./">
                    <strong class="is-size-4" style="color: #AFAFE2;">GymFly 🏋️‍♂️</strong>
                </a>
            </div>
            <div class="navbar-end">
                <div class="navbar-item">
                    <a href="logout" class="button is-danger is-light">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section">
        <div class="container">
            
            <div class="mb-5">
                <a href="visualizza-profilo?id={$cliente->getId()}" class="button is-ghost has-text-grey">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Torna al Profilo Cliente</span>
                </a>
            </div>

            <!-- CLIENT INFO BOX -->
            <div class="box mb-5">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-3 style-theme-text"><i class="fas fa-id-card mr-3"></i> Gestione Abbonamento & Iscrizione</h1>
                        <p class="subtitle is-6 has-text-grey mt-1">Cliente: <strong>{$cliente->getNome()} {$cliente->getCognome()}</strong> (CF: {$cliente->getCF()})</p>
                    </div>
                </div>
            </div>

            <div class="columns">
                
                <!-- SEZIONE ABBONAMENTO -->
                <div class="column is-6">
                    <div class="box card-custom" style="height: 100%;">
                        <h2 class="title is-4 style-theme-text mb-4"><i class="fas fa-calendar-check mr-2"></i> Abbonamento Mensile/Periodico</h2>
                        
                        <!-- Stato Abbonamento Corrente -->
                        <div class="notification is-light {if $abbonamentoAttivo && !$abbonamentoAttivo->isScaduto()}is-success{else}is-warning{/if} mb-5">
                            {if $abbonamentoAttivo}
                                <p class="is-size-5 mb-2">
                                    Stato: <strong>{if $abbonamentoAttivo->isScaduto()}Scaduto{else}Attivo{/if}</strong>
                                </p>
                                <p><strong>Tipologia:</strong> {$abbonamentoAttivo->getAbbonamento()->getTipologia()} ({$abbonamentoAttivo->getAbbonamento()->getCategoria()})</p>
                                <p><strong>Data Inizio:</strong> {$abbonamentoAttivo->getDataInizio()->format('d/m/Y')}</p>
                                <p><strong>Data Fine:</strong> {$abbonamentoAttivo->getDataFine()->format('d/m/Y')}</p>
                                <p class="mt-2"><strong>{$abbonamentoAttivo->getDescrizioneScadenza()}</strong> ({$abbonamentoAttivo->giorniRimanenti()} giorni rimanenti)</p>
                            {else}
                                <p class="is-size-5">Stato: <strong>Nessun abbonamento attivo</strong></p>
                                <p class="is-size-7 mt-1">Il cliente non ha attualmente sottoscritto alcun piano.</p>
                            {/if}
                        </div>

                        <!-- Form Sottoscrizione / Rinnovo -->
                        <form action="gestione-abbonamento" method="POST" class="mt-4">
                            <input type="hidden" name="id_cliente" value="{$cliente->getId()}">
                            <input type="hidden" name="azione" value="abbonamento">

                            <div class="field">
                                <label class="label">Seleziona Piano Abbonamento</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="abbonamento_id" required onchange="if(this.value === 'nuovo_piano') { document.getElementById('sezione-nuovo-piano').classList.remove('is-hidden'); this.value = ''; } else { document.getElementById('sezione-nuovo-piano').classList.add('is-hidden'); }">
                                            <option value="" disabled selected>Scegli una tipologia...</option>
                                            {foreach $abbonamentiDisponibili as $plan}
                                                <option value="{$plan->getId()}">{$plan->getTipologia()} - {$plan->getCategoria()} ({$plan->getDurata()} gg)</option>
                                            {/foreach}
                                            <option value="nuovo_piano">+ Aggiungi Nuova Tipologia...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Data Inizio Validità</label>
                                <div class="control">
                                    <input class="input" type="date" name="data_inizio_abbonamento" value="{$smarty.now|date_format:'%Y-%m-%d'}" required>
                                </div>
                                <p class="help">Seleziona la data a partire dalla quale l'abbonamento sarà valido.</p>
                            </div>

                            <div class="field mt-5">
                                <div class="control">
                                    <button type="submit" class="button is-gymfly is-fullwidth">
                                        <span class="icon"><i class="fas fa-check-circle"></i></span>
                                        <span>Sottoscrivi / Rinnova Abbonamento</span>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div id="sezione-nuovo-piano" class="box is-hidden mt-4 p-4" style="background-color: rgba(175, 175, 226, 0.05); border: 2px dashed var(--gymfly-primary); border-radius: 12px;">
                            <h3 class="title is-5 mb-3 style-theme-text"><i class="fas fa-plus mr-2"></i> Nuova Tipologia Abbonamento</h3>
                            <form action="gestione-abbonamento?id={$cliente->getId()}" method="POST">
                                <input type="hidden" name="azione" value="crea_tipologia">
                                <div class="field">
                                    <label class="label">Nome Tipologia (es. Open, Corsi)</label>
                                    <div class="control">
                                        <input class="input" type="text" name="nuova_tipologia" required placeholder="es: Open">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Categoria (es. Mensile, Trimestrale)</label>
                                    <div class="control">
                                        <input class="input" type="text" name="nuova_categoria" required placeholder="es: Mensile">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Durata in Giorni</label>
                                    <div class="control">
                                        <input class="input" type="number" name="nuova_durata" required placeholder="es: 30">
                                    </div>
                                </div>
                                <div class="field mt-4">
                                    <div class="control">
                                        <button type="submit" class="button is-gymfly is-fullwidth">
                                            <i class="fas fa-save mr-2"></i> Salva Nuova Tipologia
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- SEZIONE ISCRIZIONE ANNUALE -->
                <div class="column is-6">
                    <div class="box card-custom" style="height: 100%;">
                        <h2 class="title is-4 style-theme-text mb-4"><i class="fas fa-file-contract mr-2"></i> Iscrizione Annuale</h2>

                        <!-- Stato Iscrizione Corrente -->
                        <div class="notification is-light {if $iscrizione && $iscrizione->isAttiva()}is-success{else}is-warning{/if} mb-5">
                            {if $iscrizione}
                                <p class="is-size-5 mb-2">
                                    Stato: <strong>{if $iscrizione->isAttiva()}Attiva{else}Scaduta{/if}</strong>
                                </p>
                                <p><strong>Data Inizio:</strong> {$iscrizione->getDataInizio()->format('d/m/Y')}</p>
                                <p><strong>Data Fine (Scadenza):</strong> {$iscrizione->getDataFine()->format('d/m/Y')}</p>
                                <p class="mt-2"><strong>Scadenza:</strong> {$iscrizione->giorniRimanenti()} giorni rimanenti</p>
                            {else}
                                <p class="is-size-5">Stato: <strong>Nessuna iscrizione annuale registrata</strong></p>
                                <p class="is-size-7 mt-1">Il cliente non possiede una quota di iscrizione annuale valida o registrata.</p>
                            {/if}
                        </div>

                        <!-- Form Sottoscrizione / Rinnovo Iscrizione -->
                        <form action="gestione-abbonamento" method="POST" class="mt-4">
                            <input type="hidden" name="id_cliente" value="{$cliente->getId()}">
                            <input type="hidden" name="azione" value="iscrizione">

                            <div class="field">
                                <label class="label">Data Inizio Iscrizione (Validità 1 Anno)</label>
                                <div class="control">
                                    <input class="input" type="date" name="data_inizio_iscrizione" value="{$smarty.now|date_format:'%Y-%m-%d'}" required>
                                </div>
                                <p class="help">L'iscrizione ha validità automatica di 365 giorni (1 anno) dalla data inserita.</p>
                            </div>

                            <div class="field mt-5">
                                <div class="control">
                                    <button type="submit" class="button is-link is-light is-fullwidth">
                                        <span class="icon"><i class="fas fa-user-check"></i></span>
                                        <span>Registra / Rinnova Iscrizione Annuale</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </section>

</body>
</html>
