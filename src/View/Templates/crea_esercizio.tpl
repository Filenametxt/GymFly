<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - {if $is_copia}Copia Esercizio{else}Crea Nuovo Esercizio{/if}</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- INTRO / TITLE -->
            <div class="mb-5">
                <h1 class="title is-2 style-theme-text mb-2">
                    {if $is_copia}Modifica Variante Esercizio{else}Crea Esercizio{/if}
                </h1>
                <p class="subtitle is-6 has-text-grey">
                    Definisci i parametri dell'esercizio e salvalo nel catalogo.
                </p>
            </div>

            <!-- OPTION: COPIA DA ESISTENTE (Coerente con lo stile) -->
            {if $esercizi_esistenti|@count > 0}
            <div class="box mb-5">
                <h3 class="title is-5 mb-3 style-theme-text">
                    <i class="fas fa-copy mr-2" style="color: var(--gymfly-primary);"></i> Copia da Esercizio Esistente
                </h3>
                <p class="is-size-7 has-text-grey-dark mb-3">
                    Scegli un esercizio simile dal catalogo per pre-compilare il modulo.
                </p>
                <div class="field has-addons">
                    <div class="control is-expanded">
                        <div class="select is-fullwidth">
                            <select id="select-copia">
                                <option value="">-- Seleziona Esercizio da Copiare --</option>
                                {foreach $esercizi_esistenti as $ex}
                                    <option value="{$ex->getId()}">{$ex->getNomeEsercizio()}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="control">
                        <button type="button" class="button is-gymfly" id="btn-copia">
                            <i class="fas fa-clone mr-2"></i> Copia Dati
                        </button>
                    </div>
                </div>
            </div>
            {/if}

            <!-- FORM PRINCIPALE -->
            <form id="form-esercizio" action="salva-esercizio" method="POST" enctype="multipart/form-data">
                <!-- Hidden ID Provvisorio -->
                <input type="hidden" name="id_provvisorio" id="id_provvisorio" value="{$id_provvisorio}">

                <!-- GRID A COLONNE (Side-by-side su Desktop, impilato su Mobile) -->
                <div class="columns">
                    
                    <!-- COLONNA IMAGINE (Sinistra) -->
                    <div class="column is-12-mobile is-4-desktop">
                        <div class="box" style="height: 100%; display: flex; flex-direction: column;">
                            <h3 class="title is-5 mb-3 style-theme-text">Immagine Esecuzione</h3>
                            <p class="is-size-7 has-text-grey-dark mb-4">Carica una foto o una GIF (Max: 5 MB).</p>
                            
                            <div class="file is-boxed is-centered is-fullwidth" id="file-upload" style="flex-grow: 1; display: flex; flex-direction: column; min-height: 250px;">
                                <label class="file-label" style="height: 100%; width: 100%; display: flex; flex-direction: column;">
                                    <input class="file-input" type="file" name="immagine" id="immagine-input">
                                    <span class="file-cta" style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; border: 2px dashed var(--gymfly-primary); border-radius: 12px; background-color: var(--gymfly-card-bg); overflow: hidden; position: relative; padding: 0;">
                                        {if $immagine_preview}
                                            <!-- Preview esistente -->
                                            <img id="img-preview-tag" src="data:image/jpeg;base64,{$immagine_preview}" style="width: 100%; height: 100%; object-fit: cover;">
                                            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.6); color: white; padding: 5px; text-align: center; font-size: 0.8rem;">
                                                <i class="fas fa-sync-alt mr-1"></i> Cambia Immagine
                                            </div>
                                        {else}
                                            <!-- Placeholder vuoto -->
                                            <img id="img-preview-tag" class="is-hidden" style="width: 100%; height: 100%; object-fit: cover;">
                                            <div id="img-placeholder" class="has-text-centered p-4">
                                                <span class="icon is-large mb-3" style="color: var(--gymfly-primary);">
                                                    <i class="fas fa-plus fa-3x"></i>
                                                </span>
                                                <p class="file-label" style="font-weight: 600; color: var(--gymfly-text);">
                                                    add image
                                                </p>
                                            </div>
                                        {/if}
                                    </span>
                                </label>
                            </div>
                            <p class="help is-danger is-hidden" id="error-file"></p>
                        </div>
                    </div>

                    <!-- COLONNA METADATI ESERCIZIO (Destra) -->
                    <div class="column is-12-mobile is-8-desktop">
                        <div class="box" style="height: 100%;">
                            <h3 class="title is-5 mb-4 style-theme-text">Parametri Esercizio</h3>
                            
                            <!-- NOME -->
                            <div class="field mb-4">
                                <label class="label">Nome Esercizio *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="nome" id="nome-esercizio" 
                                           value="{$nome_esercizio|escape}" required 
                                           placeholder="Es. Squat con Bilanciere, Affondi Laterali">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                </div>
                                <p class="help is-danger is-hidden" id="error-nome"></p>
                                <p class="help is-success is-hidden" id="success-nome">Nome disponibile.</p>
                            </div>

                            <!-- PRIMA RIGA SELEZIONI (Tipologia e Gruppo Muscolare) -->
                            <div class="columns is-mobile mb-2">
                                <div class="column is-6">
                                    <label class="label">Tipologia</label>
                                    <div class="select is-fullwidth">
                                        <select name="tipologia_mock">
                                            <option value="corpo_libero">CORPO LIBERO</option>
                                            <option value="cardio">CARDIO</option>
                                            <option value="macchinario">MACCHINARIO</option>
                                            <option value="pesi_liberi">PESI LIBERI</option>
                                            <option value="elastici">ELASTICI</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <label class="label">Gruppo Muscolare *</label>
                                    <div class="select is-fullwidth">
                                        <select name="gruppi_muscolari[]" id="gruppi_muscolari" required>
                                            <option value="">-- Seleziona --</option>
                                            {foreach $gruppi_muscolari as $gm}
                                                <option value="{$gm->getId()}" {if in_array($gm->getId(), $selected_gruppi)}selected{/if}>
                                                    {$gm->getNomeGruppoMuscolare()}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- SECONDA RIGA SELEZIONI (Esecuzione/Tracciamento e Attrezzatura) -->
                            <div class="columns is-mobile">
                                <div class="column is-6">
                                    <label class="label">Esecuzione *</label>
                                    <div class="select is-fullwidth">
                                        <select name="tracciamento_carico" id="tracciamento_carico" required>
                                            <option value="1" {if $tracciamento_carico == 1}selected{/if}>Con Carico (Peso in Kg)</option>
                                            <option value="0" {if $tracciamento_carico == 0}selected{/if}>A Corpo Libero (Tempo/Rip)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="column is-6">
                                    <label class="label">Attrezzatura</label>
                                    <div class="select is-fullwidth">
                                        <select name="attrezzatura_id">
                                            <option value="">Corpo Libero (Nessuna)</option>
                                            {foreach $attrezzature as $att}
                                                <option value="{$att->getId()}" {if $selected_attrezzatura == $att->getId()}selected{/if}>
                                                    {$att->getNomeAttrezzatura()}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- BOX DESCRIZIONE (Sotto) -->
                <div class="box mt-5">
                    <h3 class="title is-5 style-theme-text mb-3">Descrizione / Esecuzione</h3>
                    <div class="control">
                        <textarea class="textarea" name="descrizione" placeholder="Descrivi l'esecuzione corretta dell'esercizio..." rows="5">{$descrizione|escape}</textarea>
                    </div>
                </div>

                <!-- PULSANTI SALVATAGGIO -->
                <div class="field is-grouped mt-5">
                    <div class="control is-expanded">
                        <button class="button is-gymfly is-fullwidth" type="submit" id="btn-save">
                            <i class="fas fa-save mr-2"></i> Salva Esercizio
                        </button>
                    </div>
                    <div class="control">
                        <a href="elimina-bozza?id={$id_provvisorio}" class="button is-danger is-light">
                            <i class="fas fa-trash-alt mr-2"></i> Elimina
                        </a>
                    </div>
                </div>

            </form>

        </main>
    </div>

    <!-- SCRIPT VALIDAZIONE E PREVIEW DIMOSTRATIVA -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nomeInput = document.getElementById('nome-esercizio');
            const fileInput = document.getElementById('immagine-input');
            const errorNome = document.getElementById('error-nome');
            const successNome = document.getElementById('success-nome');
            const errorFile = document.getElementById('error-file');
            const idProvvisorio = document.getElementById('id_provvisorio').value;
            const btnSave = document.getElementById('btn-save');

            // 1. Gestione Copia da Esistente (Redirection)
            const btnCopia = document.getElementById('btn-copia');
            if (btnCopia) {
                btnCopia.addEventListener('click', () => {
                    const select = document.getElementById('select-copia');
                    if (select.value) {
                        window.location.href = `copia-esercizio?id=${ select.value }`;
                    } else {
                        alert('Seleziona un esercizio da copiare.');
                    }
                });
            }

            // 2. Anteprima immagine in tempo reale su upload client-side
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const imgTag = document.getElementById('img-preview-tag');
                        const placeholder = document.getElementById('img-placeholder');
                        if (imgTag) {
                            imgTag.src = e.target.result;
                            imgTag.classList.remove('is-hidden');
                        }
                        if (placeholder) {
                            placeholder.classList.add('is-hidden');
                        }
                    };
                    reader.readAsDataURL(file);
                }
                validaDati();
            });

            // 3. Debounce validazione in tempo reale
            let timeout = null;
            nomeInput.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(validaDati, 500);
            });

            function validaDati() {
                const formData = new FormData();
                formData.append('nome', nomeInput.value);
                formData.append('id_provvisorio', idProvvisorio);
                
                if (fileInput.files.length > 0) {
                    formData.append('immagine', fileInput.files[0]);
                }

                fetch('valida-esercizio', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Validazione Nome
                    if (data.duplicato) {
                        nomeInput.classList.add('is-danger');
                        nomeInput.classList.remove('is-success');
                        errorNome.textContent = data.errore_nome;
                        errorNome.classList.remove('is-hidden');
                        successNome.classList.add('is-hidden');
                    } else if (nomeInput.value.trim() !== '') {
                        nomeInput.classList.remove('is-danger');
                        nomeInput.classList.add('is-success');
                        errorNome.classList.add('is-hidden');
                        successNome.classList.remove('is-hidden');
                    } else {
                        nomeInput.classList.remove('is-danger', 'is-success');
                        errorNome.classList.add('is-hidden');
                        successNome.classList.add('is-hidden');
                    }

                    // Validazione File
                    if (data.errore_file) {
                        errorFile.textContent = data.errore_file;
                        errorFile.classList.remove('is-hidden');
                        fileInput.classList.add('is-danger');
                    } else {
                        errorFile.classList.add('is-hidden');
                        fileInput.classList.remove('is-danger');
                    }

                    // Abilita/Disabilita pulsante salvataggio
                    if (data.success && nomeInput.value.trim() !== '') {
                        btnSave.disabled = false;
                    } else if (nomeInput.value.trim() === '') {
                        btnSave.disabled = false;
                    } else {
                        btnSave.disabled = true;
                    }
                })
                .catch(err => console.error('Errore durante la validazione:', err));
            }
        });
    </script>
</body>
</html>
