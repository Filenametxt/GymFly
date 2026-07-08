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
                    <a href="dashboard-allenatore" class="button is-link is-light">
                        <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-8">
                    
                    <!-- OPTION: COPIA DA ESISTENTE -->
                    {if $esercizi_esistenti|@count > 0}
                    <div class="control-box mb-5">
                        <h3 class="title is-5 mb-3" style="color: #AFAFE2;">
                            <i class="fas fa-copy mr-2"></i> Copia da Esercizio Esistente
                        </h3>
                        <p class="subtitle is-6 has-text-grey-dark mb-4">
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

                    <!-- MAIN FORM -->
                    <div class="control-box">
                        <div class="has-text-centered mb-5">
                            <span class="icon is-large has-text-trainer-theme">
                                <i class="fas fa-dumbbell fa-3x" style="color: #AFAFE2;"></i>
                            </span>
                            <h1 class="title is-3 mt-3 style-theme-text">
                                {if $is_copia}Modifica Variante Esercizio{else}Nuovo Esercizio{/if}
                            </h1>
                            <p class="subtitle is-6 has-text-grey mt-1">
                                Inserisci le informazioni per registrare l'esercizio nel catalogo globale.
                            </p>
                        </div>

                        <form id="form-esercizio" action="salva-esercizio" method="POST" enctype="multipart/form-data">
                            
                            <!-- Hidden ID Provvisorio -->
                            <input type="hidden" name="id_provvisorio" id="id_provvisorio" value="{$id_provvisorio}">

                            <!-- NOME ESERCIZIO -->
                            <div class="field">
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

                            <!-- DESCRIZIONE -->
                            <div class="field">
                                <label class="label">Descrizione / Esecuzione</label>
                                <div class="control">
                                    <textarea class="textarea" name="descrizione" placeholder="Descrivi l'esecuzione corretta dell'esercizio..." rows="3">{$descrizione|escape}</textarea>
                                </div>
                            </div>

                            <!-- TRACCIAMENTO CARICO -->
                            <div class="field">
                                <label class="label">Tipo di Tracciamento *</label>
                                <p class="help has-text-grey-dark mb-2">Definisci se l'esercizio richiede di tracciare il peso (carico) o solo il tempo/ripetizioni.</p>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="tracciamento_carico" id="tracciamento_carico">
                                            <option value="1" {if $tracciamento_carico == 1}selected{/if}>Tracciamento del Carico (con Peso in Kg)</option>
                                            <option value="0" {if $tracciamento_carico == 0}selected{/if}>Tracciamento Ripetizioni / Tempo (a corpo libero)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- GRUPPI MUSCOLARI (MULTIPLE SELECT) -->
                            <div class="field">
                                <label class="label">Gruppi Muscolari Coinvolti (Seleziona uno o più)</label>
                                <div class="columns is-multiline px-3 mt-2">
                                    {foreach $gruppi_muscolari as $gm}
                                        <div class="column is-4-tablet is-6-mobile py-1">
                                            <label class="checkbox">
                                                <input type="checkbox" name="gruppi_muscolari[]" value="{$gm->getId()}" 
                                                       {if in_array($gm->getId(), $selected_gruppi)}checked{/if}>
                                                <span class="ml-1">{$gm->getNomeGruppoMuscolare()}</span>
                                            </label>
                                        </div>
                                    {foreachelse}
                                        <p class="has-text-grey-light is-size-7 px-3">Nessun gruppo muscolare censito.</p>
                                    {/foreach}
                                </div>
                            </div>

                            <!-- ATTREZZATURA (SINGLE SELECT) -->
                            <div class="field">
                                <label class="label">Attrezzatura Necessaria</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select name="attrezzatura_id">
                                            <option value="">Nessuna Attrezzatura (Corpo Libero)</option>
                                            {foreach $attrezzature as $att}
                                                <option value="{$att->getId()}" {if $selected_attrezzatura == $att->getId()}selected{/if}>
                                                    {$att->getNomeAttrezzatura()}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- IMMAGINE / GIF DIMOSTRATIVA -->
                            <div class="field">
                                <label class="label">Immagine / GIF Dimostrativa</label>
                                <p class="help has-text-grey-dark mb-2">Carica una foto o una GIF che illustri la corretta esecuzione (Max: 5 MB).</p>
                                
                                {if $immagine_preview}
                                    <div class="mb-3" id="existing-img-container">
                                        <p class="is-size-7 has-text-grey-dark mb-1">Immagine esistente:</p>
                                        <figure class="image is-128x128 mb-2" style="border: 2px solid var(--gymfly-primary); border-radius: 8px; overflow: hidden;">
                                            <img src="data:image/jpeg;base64,{$immagine_preview}" alt="Anteprima">
                                        </figure>
                                    </div>
                                {/if}

                                <div class="file has-name is-fullwidth" id="file-upload">
                                    <label class="file-label">
                                        <input class="file-input" type="file" name="immagine" id="immagine-input">
                                        <span class="file-cta">
                                            <span class="file-icon">
                                                <i class="fas fa-upload"></i>
                                            </span>
                                            <span class="file-label">
                                                Scegli file...
                                            </span>
                                        </span>
                                        <span class="file-name" id="file-name-label">
                                            Nessun file selezionato
                                        </span>
                                    </label>
                                </div>
                                <p class="help is-danger is-hidden" id="error-file"></p>
                            </div>

                            <!-- ACTION BUTTONS -->
                            <hr>
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
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- REAL-TIME VALIDATION & DYNAMIC UI SCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const nomeInput = document.getElementById('nome-esercizio');
            const fileInput = document.getElementById('immagine-input');
            const errorNome = document.getElementById('error-nome');
            const successNome = document.getElementById('success-nome');
            const errorFile = document.getElementById('error-file');
            const fileNameLabel = document.getElementById('file-name-label');
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

            // Aggiorna etichetta nome file al caricamento
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    fileNameLabel.textContent = fileInput.files[0].name;
                } else {
                    fileNameLabel.textContent = 'Nessun file selezionato';
                }
                validaDati();
            });

            // 2. Real-time validation su inserimento nome
            let timeout = null;
            nomeInput.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(validaDati, 500); // Debounce di 500ms
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
                        btnSave.disabled = false; // Lascia gestire l'errore al required del browser
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
