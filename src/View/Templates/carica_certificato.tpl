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
    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            <div class="container">
                
                <!-- TORNA AL PROFILO (Desktop) -->
                <div class="mb-5 is-hidden-mobile">
                    <a href="profilo{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" class="button is-ghost has-text-grey pl-0">
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
                            <h1 class="title is-2 has-text-white mb-2">Certificato Medico</h1>
                            <p class="subtitle is-5 has-text-white-ter">Carica il tuo certificato in formato PDF per mantenere attiva l'iscrizione</p>
                        </div>
                        <div class="column is-narrow">
                            <figure class="image is-96x96">
                                <span class="icon is-large has-text-white">
                                    <i class="fas fa-file-medical fa-5x"></i>
                                </span>
                            </figure>
                        </div>
                    </div>
                </div>

                <!-- ================= MOBILE HEADER (Con pulsante indietro integrato) ================= -->
                <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                    <div style="width: 45px;"></div>
                    <a href="profilo{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" class="button is-ghost p-0 mr-3" style="color: inherit; height: auto;">
                        <span class="icon is-medium"><i class="fas fa-arrow-left fa-lg"></i></span>
                    </a>
                    <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">CARICA CERTIFICATO</strong>
                </div>

                <!-- FORM DI MODIFICA ESTESO (Coerente con modifica_anagrafica.tpl) -->
                <div class="columns">
                    <div class="column is-12">
                        <div class="box p-5">
                            <h3 class="title is-5 mb-5 style-theme-text">
                                <i class="fas fa-file-medical mr-2" style="color: var(--gymfly-primary);"></i> Certificato Medico
                            </h3>
                            
                            <form action="carica-certificato{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" method="POST" enctype="multipart/form-data">
                                
                                <div class="field mb-4">
                                    <label class="label">Medico Certificante</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="medico" required placeholder="Es: Dott. Mario Rossi">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-user-md" style="color: var(--gymfly-primary);"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field mb-4">
                                    <label class="label">Data di Emissione</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="date" name="data_emissione" required>
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-calendar-alt" style="color: var(--gymfly-primary);"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field mb-5">
                                    <label class="label">Seleziona File (PDF)</label>
                                    <div class="control">
                                        <input class="input" type="file" name="file_certificato" accept="application/pdf" required style="padding: 6px 12px; height: auto; border: 2px solid var(--gymfly-primary); border-radius: 8px;">
                                    </div>
                                </div>

                                <!-- SUBMIT -->
                                <div class="field mt-5">
                                    <div class="control">
                                        <button class="button is-gymfly is-fullwidth" type="submit">
                                            <span class="icon"><i class="fas fa-cloud-upload-alt"></i></span>
                                            <span>Carica Certificato</span>
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
