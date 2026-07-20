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

                <!-- TORNA AL PROFILO (Desktop) -->
                <div class="mb-5 is-hidden-mobile">
                    <a href="profilo" class="button is-ghost has-text-grey pl-0">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Torna al Profilo</span>
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
                            <h1 class="title is-2 has-text-white mb-2">Modifica Password</h1>
                            <p class="subtitle is-5 has-text-white-ter">Aggiorna le credenziali di accesso al tuo account</p>
                        </div>
                        <div class="column is-narrow">
                            <figure class="image is-96x96">
                                <span class="icon is-large has-text-white">
                                    <i class="fas fa-key fa-5x"></i>
                                </span>
                            </figure>
                        </div>
                    </div>
                </div>

                <!-- ================= MOBILE HEADER (Con pulsante indietro integrato) ================= -->
                <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                    <div style="width: 45px;"></div>
                    <a href="profilo" class="button is-ghost p-0 mr-3" style="color: inherit; height: auto;">
                        <span class="icon is-medium"><i class="fas fa-arrow-left fa-lg"></i></span>
                    </a>
                    <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">MODIFICA PASSWORD</strong>
                </div>

                <!-- FORM DI MODIFICA ESTESO -->
                <div class="columns">
                    <div class="column is-12">
                        <div class="box p-5">
                            <h3 class="title is-5 mb-5 style-theme-text">
                                <i class="fas fa-shield-alt mr-2" style="color: var(--gymfly-primary);"></i> Credenziali di Sicurezza
                            </h3>
                            
                            <form action="cambia-password" method="POST">
                                
                                <!-- VECCHIA PASSWORD -->
                                <div class="field mb-4">
                                    <label class="label">Vecchia Password</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="password" name="vecchia_password" placeholder="Inserisci la vecchia password" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-lock" style="color: var(--gymfly-primary);"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- NUOVA PASSWORD -->
                                <div class="field mb-4">
                                    <label class="label">Nuova Password</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="password" name="nuova_password" placeholder="Almeno 8 caratteri" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-key" style="color: var(--gymfly-primary);"></i>
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
                                            <i class="fas fa-key" style="color: var(--gymfly-primary);"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- SUBMIT -->
                                <div class="field mt-5">
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
