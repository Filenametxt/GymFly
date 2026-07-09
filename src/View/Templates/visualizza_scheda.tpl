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
</head>
<body>

    <!-- INCLUDI SIDEBAR REQUISITO -->
    {include file='sidebar.tpl'}

    <!-- CONTENT -->
    <section class="section" style="padding-left: 1rem !important; padding-right: 1rem !important; padding-top: 1.5rem; padding-bottom: 1.5rem;">
        <div class="container visualizza-scheda-container">
            
            {assign var="letters" value=['A', 'B', 'C', 'D', 'E', 'F', 'G']}
            
            <!-- ================= SCREEN 1: LISTA ALLENAMENTI ================= -->
            <div id="workouts-list-screen">
                
                <!-- HEADER BAR SCREEN 1 -->
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--gymfly-accent); padding-bottom: 0.75rem; margin-bottom: 1.5rem; padding-left: 2.75rem;">
                    <h1 class="title is-5 style-theme-text mb-0" style="font-weight: 800; letter-spacing: 0.5px;">SCHEDA ALLENAMENTO</h1>
                    
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <a href="richiedi-scheda" class="button is-ghost p-0 style-theme-text" title="Richiedi Nuova Scheda">
                            <span class="icon is-medium"><i class="fas fa-plus fa-lg"></i></span>
                        </a>
                        <a href="esporta-scheda" class="button is-ghost p-0 style-theme-text" title="Esporta in PDF">
                            <span class="icon is-medium"><i class="fas fa-download fa-lg"></i></span>
                        </a>
                    </div>
                </div>

                <!-- CARTELLO INFO SCHEDA -->
                <div class="box p-4 mb-5" style="border: 2px solid var(--gymfly-primary); border-radius: 16px; background-color: var(--gymfly-card-bg);">
                    <span class="tag is-success is-light mb-2" style="font-weight: bold; border-radius: 6px;">SCHEDA ATTIVA</span>
                    <h2 class="title is-4 style-theme-text mb-1" style="line-height: 1.2;">{$scheda->getNome_scheda()}</h2>
                    <p class="subtitle is-6 has-text-grey-dark mb-3">Obiettivo: <strong>{$scheda->getObiettivo()}</strong></p>
                    <div style="border-top: 1px solid var(--gymfly-accent); padding-top: 0.75rem;" class="is-size-7 has-text-grey">
                        <p class="mb-1"><i class="fas fa-user-ninja mr-1"></i> Coach: <strong>{$scheda->getAllenatore()->getNome()} {$scheda->getAllenatore()->getCognome()}</strong></p>
                        <p><i class="fas fa-calendar-alt mr-1"></i> Scadenza: <strong>{$scheda->getData_fine()->format('d/m/Y')}</strong></p>
                    </div>
                </div>

                <!-- LISTA SESSIONI -->
                <div class="columns is-multiline">
                    {foreach $scheda->getAllenamenti() as $index => $allenamento}
                        {assign var="workoutLetter" value=$letters[$index]}
                        <div class="column is-12-mobile is-6-tablet is-4-desktop">
                            <div class="box mb-4" onclick="openWorkout('workout-{$allenamento->getId()}')" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; border: 2px solid var(--gymfly-accent); border-radius: 16px; background-color: var(--gymfly-card-bg); height: 100%;">
                                <div style="max-width: 85%;">
                                    <h3 class="title is-5 mb-1 style-theme-text" style="font-weight: 700;">ALLENAMENTO {$workoutLetter}</h3>
                                    <p class="subtitle is-6 has-text-grey mb-0" style="font-size: 0.9rem;">{$allenamento->getNome()}{if $allenamento->getDescrizione()|pulisci_descrizione} - {$allenamento->getDescrizione()|pulisci_descrizione}{/if}</p>
                                </div>
                                <span class="icon style-theme-text">
                                    <i class="fas fa-chevron-right fa-lg"></i>
                                </span>
                            </div>
                        </div>
                    {foreachelse}
                        <div class="column is-12">
                            <div class="box has-text-centered py-5" style="border: 2px dashed var(--gymfly-accent); border-radius: 16px; background-color: var(--gymfly-card-bg);">
                                <span class="icon is-large has-text-grey-light"><i class="fas fa-file-invoice fa-2x"></i></span>
                                <p class="has-text-grey mt-2">Nessun allenamento presente in questa scheda.</p>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>

            <!-- ================= SCREEN 2: DETTAGLIO E MODIFICHE ================= -->
            {foreach $scheda->getAllenamenti() as $index => $allenamento}
                {assign var="workoutLetter" value=$letters[$index]}
                <div id="workout-{$allenamento->getId()}" class="workout-detail-screen" style="display: none;">
                    
                    <!-- HEADER BAR SCREEN 2 -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--gymfly-accent); padding-bottom: 0.75rem; margin-bottom: 1.5rem; padding-left: 2.75rem;">
                        <div style="display: flex; align-items: center;">
                            <!-- Back arrow button -->
                            <button type="button" class="button is-ghost p-0 style-theme-text mr-3" onclick="closeWorkout()" style="height: auto;">
                                <span class="icon is-medium"><i class="fas fa-arrow-left fa-lg"></i></span>
                            </button>
                            <h1 class="title is-5 style-theme-text mb-0" style="font-weight: 800; letter-spacing: 0.5px; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">ALLENAMENTO {$workoutLetter}</h1>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 1.5rem;">
                            <button type="button" class="button is-ghost p-0 style-theme-text" title="Info Sessione" onclick="openInfoModal('{$allenamento->getDescrizione()|pulisci_descrizione|default:"Nessuna descrizione inserita dall\'allenatore per questa sessione."|escape:"javascript"}')" style="height: auto;">
                                <span class="icon is-medium"><i class="fas fa-info-circle fa-lg"></i></span>
                            </button>
                            <a href="esporta-scheda" class="button is-ghost p-0 style-theme-text" title="Esporta in PDF" style="height: auto;">
                                <span class="icon is-medium"><i class="fas fa-download fa-lg"></i></span>
                            </a>
                        </div>
                    </div>

                    <!-- FORM DI COMPILAZIONE/MODIFICA DIRETTA -->
                    <form action="modifica-dettagli" method="POST">
                        {assign var="blocks" value=[]}
                        {assign var="currentBlock" value=null}
                        {foreach $allenamento->getDettagli() as $dettaglio}
                            {assign var="exId" value=$dettaglio->getEsercizio()->getId()}
                            {assign var="isNewBlock" value=false}
                            {if $currentBlock === null || $currentBlock.esercizio_id !== $exId}
                                {assign var="isNewBlock" value=true}
                            {else}
                                {assign var="lastIndex" value=count($currentBlock.dettagli) - 1}
                                {assign var="lastDettaglio" value=$currentBlock.dettagli[$lastIndex]}
                                {if $dettaglio->getSerie() <= $lastDettaglio->getSerie()}
                                    {assign var="isNewBlock" value=true}
                                {/if}
                            {/if}
                            {if $isNewBlock}
                                {if $currentBlock !== null}
                                    {$blocks[] = $currentBlock}
                                {/if}
                                {assign var="currentBlock" value=[
                                    'esercizio' => $dettaglio->getEsercizio(),
                                    'esercizio_id' => $exId,
                                    'dettagli' => []
                                ]}
                            {/if}
                            {$currentBlock.dettagli[] = $dettaglio}
                        {/foreach}
                        {if $currentBlock !== null}
                            {$blocks[] = $currentBlock}
                        {/if}

                        <div class="columns is-multiline">
                            {foreach $blocks as $group}
                                {assign var="esercizio" value=$group['esercizio']}
                                <div class="column is-12-mobile is-6-tablet is-6-desktop">
                                    <div class="box mb-4 p-4" style="border: 2px solid var(--gymfly-primary); border-radius: 16px; background-color: var(--gymfly-card-bg); height: 100%;">
                                        <!-- HEADER CON NOME E IMMAGINE ESERCIZIO -->
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 2px solid var(--gymfly-accent); padding-bottom: 0.75rem;">
                                            <div style="max-width: 70%;">
                                                <h3 class="title is-5 style-theme-text mb-1" style="font-weight: 700; line-height: 1.2;">{$esercizio->getNomeEsercizio()}</h3>
                                            </div>
                                            <div style="flex-shrink: 0; margin-left: 0.75rem;">
                                                {if $esercizio->getImmagine()}
                                                    <figure class="image is-48x48" style="border-radius: 8px; overflow: hidden; border: 1px solid var(--gymfly-accent);">
                                                        <img src="data:image/jpeg;base64,{$esercizio->getImmagine()|base64_encode}" alt="Esercizio" style="width: 48px; height: 48px; object-fit: cover;">
                                                    </figure>
                                                {else}
                                                    <div style="width: 48px; height: 48px; border-radius: 8px; border: 1px dashed var(--gymfly-primary); display: flex; align-items: center; justify-content: center; background-color: var(--gymfly-bg);">
                                                        <span class="icon has-text-grey-light"><i class="fas fa-dumbbell"></i></span>
                                                    </div>
                                                {/if}
                                            </div>
                                        </div>

                                        <!-- DETTAGLI SERIE IN COLONNA -->
                                        {foreach $group['dettagli'] as $dettaglio}
                                            <div class="mb-4" style="{if not $dettaglio@last}border-bottom: 1px dashed var(--gymfly-accent); padding-bottom: 0.75rem; margin-bottom: 0.75rem;{/if}">
                                                <div style="font-weight: bold; margin-bottom: 0.5rem; color: var(--gymfly-primary);">Serie {$dettaglio->getSerie()}</div>
                                                
                                                <div style="display: flex; flex-direction: column; gap: 0.5rem; width: 100%;">
                                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                        <span class="is-size-7 has-text-grey-dark" style="width: 85px; font-weight: bold; text-align: right;">Ripetizioni:</span>
                                                        <div class="control has-icons-left" style="flex: 1;">
                                                            <input class="input is-small" type="number" name="dettagli[{$dettaglio->getId()}][ripetizioni]" value="{$dettaglio->getRipetizioni()}" required min="1" style="text-align: center;">
                                                            <span class="icon is-small is-left"><i class="fas fa-redo"></i></span>
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                        <span class="is-size-7 has-text-grey-dark" style="width: 85px; font-weight: bold; text-align: right;">Carico (Kg):</span>
                                                        <div class="control has-icons-left" style="flex: 1;">
                                                            <input class="input is-small" type="number" step="0.5" name="dettagli[{$dettaglio->getId()}][carico]" value="{$dettaglio->getCarico()}" required min="0" style="text-align: center;">
                                                            <span class="icon is-small is-left"><i class="fas fa-weight-hanging"></i></span>
                                                        </div>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                        <span class="is-size-7 has-text-grey-dark" style="width: 85px; font-weight: bold; text-align: right;">Recupero:</span>
                                                        <div class="control has-icons-left" style="flex: 1;">
                                                            <input class="input is-small" type="text" name="dettagli[{$dettaglio->getId()}][recupero]" value="{$allenamento->getDescrizione()|estrai_recupero:$esercizio->getNomeEsercizio():$dettaglio->getSerie():$dettaglio->getId()}" placeholder="Es: 60s" style="text-align: center;">
                                                            <span class="icon is-small is-left"><i class="fas fa-clock"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            {/foreach}
                        </div>

                        <!-- BOTTONE SALVA MODIFICHE -->
                        <div class="field mt-5" style="max-width: 400px; margin: 1.5rem auto 0 auto;">
                            <div class="control">
                                <button class="button is-gymfly is-fullwidth py-4" type="submit" style="font-weight: bold; border-radius: 12px;">
                                    <i class="fas fa-save mr-2"></i> SALVA MODIFICHE
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            {/foreach}

            <!-- MODAL INFO DI BULMA -->
            <div class="modal" id="info-modal">
                <div class="modal-background" onclick="closeInfoModal()"></div>
                <div class="modal-content px-3">
                    <div class="box p-5" style="border: 2px solid var(--gymfly-primary); border-radius: 16px; background-color: var(--gymfly-card-bg); max-width: 450px; margin: 0 auto;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--gymfly-accent); padding-bottom: 0.75rem; margin-bottom: 1rem;">
                            <h3 class="title is-5 style-theme-text mb-0" style="font-weight: 700;">NOTE ALLENAMENTO</h3>
                            <button type="button" class="delete" aria-label="close" onclick="closeInfoModal()"></button>
                        </div>
                        <p id="info-modal-text" class="style-theme-text" style="white-space: pre-line; font-size: 0.95rem; line-height: 1.5;"></p>
                        <button type="button" class="button is-gymfly is-fullwidth mt-4" onclick="closeInfoModal()" style="border-radius: 10px; font-weight: bold; background: var(--gymfly-primary); color: white;">CHIUDI</button>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- SCRIPT CAMBIO SCHERMO DUAL-VIEW E MODAL INFO -->
    <script>
        function openWorkout(workoutId) {
            document.getElementById('workouts-list-screen').style.display = 'none';
            
            const screens = document.querySelectorAll('.workout-detail-screen');
            screens.forEach(s => s.style.display = 'none');
            
            const targetScreen = document.getElementById(workoutId);
            if (targetScreen) {
                targetScreen.style.display = 'block';
            }
        }

        function closeWorkout() {
            const screens = document.querySelectorAll('.workout-detail-screen');
            screens.forEach(s => s.style.display = 'none');
            
            document.getElementById('workouts-list-screen').style.display = 'block';
        }

        function openInfoModal(text) {
            document.getElementById('info-modal-text').textContent = text;
            document.getElementById('info-modal').classList.add('is-active');
        }

        function closeInfoModal() {
            document.getElementById('info-modal').classList.remove('is-active');
        }
    </script>
</body>
</html>
