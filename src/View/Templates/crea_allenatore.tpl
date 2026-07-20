<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Nuovo Allenatore</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        
        <main class="app-content">
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
                        <h1 class="title is-2 has-text-white mb-2">
                            Nuovo Allenatore
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Registra un nuovo allenatore/preparatore. La password temporanea sarà inviata via email.
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-user-ninja fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px; flex-grow: 1;">NUOVO ALLENATORE</strong>
            </div>

            <!-- FORM PRINCIPALE -->
            <form action="crea-allenatore" method="POST">
                
                <!-- BOX 1: ANAGRAFICA -->
                <div class="columns">
                    <div class="column is-12">
                        <div class="box">
                            <h3 class="title is-5 mb-4 style-theme-text">Anagrafica</h3>
                            
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Nome *</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="text" name="nome" required placeholder="Es: Luca">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Cognome *</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="text" name="cognome" required placeholder="Es: Bianchi">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Codice Fiscale *</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="text" name="cf" required placeholder="Es: BNCLCU85A01H501Z" maxlength="16">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-id-card"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Sesso *</label>
                                        <div class="control has-icons-left">
                                            <div class="select is-fullwidth">
                                                <select name="sesso" required>
                                                    <option value="" disabled selected>Seleziona sesso</option>
                                                    <option value="M">Maschio</option>
                                                    <option value="F">Femmina</option>
                                                </select>
                                            </div>
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-venus-mars"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- BOX 2: CONTATTI & DOMICILIO -->
                <div class="columns mt-4">
                    <div class="column is-12">
                        <div class="box">
                            <h3 class="title is-5 mb-4 style-theme-text">Contatti & Domicilio</h3>
                            
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Email *</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="email" name="email" required placeholder="Es: luca.bianchi@email.it">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Telefono</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="text" name="telefono" placeholder="Es: 3339876543">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="field mb-4">
                                <label class="label">Indirizzo di Residenza *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="indirizzo" required placeholder="Es: Corso Umberto I 45, Torino">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-home"></i>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- PULSANTI SALVATAGGIO -->
                <div class="field is-grouped mt-5">
                    <div class="control is-expanded">
                        <button class="button is-gymfly is-fullwidth" type="submit">
                            <i class="fas fa-check-circle mr-2"></i> Registra Allenatore
                        </button>
                    </div>
                    <div class="control">
                        <a href="allenatori" class="button is-danger is-light">
                            Annulla
                        </a>
                    </div>
                </div>

            </form>
        </main>
    </div>

</body>
</html>
