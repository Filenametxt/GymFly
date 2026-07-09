<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Prenota Sessione Privata</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- HEADER -->
            <div class="mb-6">
                <h1 class="title is-2 style-theme-text mb-2">Pianifica Sessione Privata</h1>
                <p class="subtitle is-6 has-text-grey">Prenota un incontro individuale con uno dei tuoi atleti e blocca la fascia oraria</p>
            </div>

            <!-- FORM CARD -->
            <div class="columns">
                <div class="column is-8-tablet is-6-desktop">
                    <div class="card p-5" style="border: 2px solid var(--gymfly-primary); background-color: var(--gymfly-card-bg);">
                        <form action="prenota-sessione-privata" method="POST">
                            
                            <!-- SELEZIONE ATLETA -->
                            <div class="field mb-4">
                                <label class="label style-theme-text">Atleta / Cliente *</label>
                                <div class="control has-icons-left">
                                    <div class="select is-fullwidth">
                                        <select name="id_cliente" required>
                                            <option value="">Scegli l'atleta...</option>
                                            {foreach from=$clienti item=cl}
                                                <option value="{$cl->getId()}">{$cl->getNome()} {$cl->getCognome()} ({$cl->getEmail()})</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <span class="icon is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- GIORNO -->
                            <div class="field mb-4">
                                <label class="label style-theme-text">Giorno della sessione *</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="date" name="data" required min="{$smarty.now|date_format:'%Y-%m-%d'}">
                                    <span class="icon is-left">
                                        <i class="fas fa-calendar-day"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="columns mb-5">
                                <!-- ORA INIZIO -->
                                <div class="column">
                                    <div class="field">
                                        <label class="label style-theme-text">Ora Inizio *</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="time" name="ora_inizio" required>
                                            <span class="icon is-left">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- ORA FINE -->
                                <div class="column">
                                    <div class="field">
                                        <label class="label style-theme-text">Ora Fine *</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="time" name="ora_fine" required>
                                            <span class="icon is-left">
                                                <i class="fas fa-clock"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PULSANTI -->
                            <div class="field is-grouped mt-5">
                                <div class="control">
                                    <button type="submit" class="button is-gymfly px-5">
                                        <span class="icon"><i class="fas fa-save"></i></span>
                                        <span>Salva Prenotazione</span>
                                    </button>
                                </div>
                                <div class="control">
                                    <a href="calendario" class="button is-light">
                                        <span>Annulla</span>
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>
</html>
