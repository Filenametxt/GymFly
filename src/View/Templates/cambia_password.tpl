<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Modifica Password</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            <div class="container">

                <div class="columns is-centered">
                    <div class="column is-5">
                        <div class="mb-5 has-text-left">
                            <a href="profilo" class="button is-ghost has-text-grey pl-0">
                                <span class="icon"><i class="fas fa-arrow-left"></i></span>
                                <span>Torna al Profilo</span>
                            </a>
                        </div>
                        
                        <div class="box card-custom">
                            <div class="has-text-centered mb-5">
                                <span class="icon is-large" style="color: var(--gymfly-primary);">
                                    <i class="fas fa-key fa-2x"></i>
                                </span>
                                <h1 class="title is-3 style-theme-text mt-3">Modifica Password</h1>
                                <p class="subtitle is-6 has-text-grey mt-1">Aggiorna le credenziali di accesso al tuo account</p>
                            </div>

                            <form action="cambia-password" method="POST">
                                
                                <!-- VECCHIA PASSWORD -->
                                <div class="field">
                                    <label class="label">Vecchia Password</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="password" name="vecchia_password" placeholder="Inserisci la vecchia password" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- NUOVA PASSWORD -->
                                <div class="field">
                                    <label class="label">Nuova Password</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="password" name="nuova_password" placeholder="Almeno 8 caratteri" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-key"></i>
                                        </span>
                                    </div>
                                    <p class="help">La nuova password deve essere lunga almeno 8 caratteri.</p>
                                </div>

                                <!-- CONFERMA PASSWORD -->
                                <div class="field mb-5">
                                    <label class="label">Conferma Nuova Password</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="password" name="conferma_password" placeholder="Ripeti la nuova password" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-key"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- SUBMIT -->
                                <div class="field">
                                    <div class="control">
                                        <button type="submit" class="button is-gymfly is-fullwidth">
                                            <span class="icon"><i class="fas fa-save"></i></span>
                                            <span>Salva Nuova Password</span>
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
