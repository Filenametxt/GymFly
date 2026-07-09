<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Carica Certificato Medico</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-six-fifths-tablet is-half-desktop">
                    
                    <div class="mb-5">
                        <a href="profilo{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" class="button is-ghost has-text-grey">
                            <span class="icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Torna al Profilo</span>
                        </a>
                    </div>

                    <div class="card p-5">
                        <div class="has-text-centered mb-5">
                            <span class="icon is-large has-text-danger">
                                <i class="fas fa-file-medical fa-3x"></i>
                            </span>
                            <h1 class="title is-3 mt-3 style-theme-text">Certificato Medico</h1>
                            <p class="subtitle is-6 has-text-grey mt-1">Carica il tuo certificato in formato PDF per mantenere attiva l'iscrizione</p>
                        </div>

                        <form action="carica-certificato{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" method="POST" enctype="multipart/form-data">
                            
                            <div class="field">
                                <label class="label">Medico Certificante</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="medico" required placeholder="Es: Dott. Mario Rossi">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user-md"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Data di Emissione</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="date" name="data_emissione" required>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Seleziona File (PDF)</label>
                                <div class="control">
                                    <input class="input" type="file" name="file_certificato" accept="application/pdf" required style="padding: 6px 12px; height: auto; border: 2px solid var(--gymfly-primary); border-radius: 8px;">
                                </div>
                            </div>

                            <div class="field mt-5">
                                <div class="control">
                                    <button class="button is-gymfly is-fullwidth" type="submit">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i> Carica Certificato
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
