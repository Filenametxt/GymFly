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

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="dashboard-admin" style="display: flex; align-items: center; gap: 6px;">
                    <strong class="is-size-4" style="color: var(--gymfly-text) !important; font-weight: 800;">GymFly</strong>
                    <div class="gf-logo-icon" style="width: 44px; height: 44px;"></div>
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-eight-tenths-tablet is-two-thirds-desktop">
                    
                    <div class="mb-5">
                        <a href="dashboard-admin" class="button is-ghost has-text-grey">
                            <span class="icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Torna alla Dashboard</span>
                        </a>
                    </div>

                    <div class="card p-5">
                        <div class="has-text-centered mb-5">
                            <span class="icon is-large has-text-link">
                                <i class="fas fa-user-ninja fa-3x"></i>
                            </span>
                            <h1 class="title is-3 mt-3 style-theme-text">Nuovo Allenatore</h1>
                            <p class="subtitle is-6 has-text-grey mt-1">Registra un nuovo allenatore/preparatore. La password temporanea sarà inviata via email.</p>
                        </div>

                        <form action="crea-allenatore" method="POST">
                            
                            <h2 class="subtitle is-5 style-theme-text" style="border-bottom: 2px solid var(--gymfly-primary); padding-bottom: 0.5rem;">Anagrafica</h2>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Nome *</label>
                                        <input class="input" type="text" name="nome" required placeholder="Es: Luca">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Cognome *</label>
                                        <input class="input" type="text" name="cognome" required placeholder="Es: Bianchi">
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Codice Fiscale *</label>
                                        <input class="input" type="text" name="cf" required placeholder="Es: BNCLCU85A01H501Z" maxlength="16">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Sesso *</label>
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select name="sesso" required>
                                                    <option value="" disabled selected>Seleziona sesso</option>
                                                    <option value="M">Maschio</option>
                                                    <option value="F">Femmina</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="subtitle is-5 style-theme-text mt-5" style="border-bottom: 2px solid var(--gymfly-primary); padding-bottom: 0.5rem;">Contatti & Indirizzo</h2>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Email *</label>
                                        <input class="input" type="email" name="email" required placeholder="Es: luca.bianchi@email.it">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Telefono</label>
                                        <input class="input" type="text" name="telefono" placeholder="Es: 3339876543">
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Indirizzo di Residenza *</label>
                                <input class="input" type="text" name="indirizzo" required placeholder="Es: Corso Umberto I 45, Torino">
                            </div>

                            <div class="field mt-5">
                                <button class="button is-gymfly is-fullwidth" type="submit">
                                    <span class="icon mr-1"><i class="fas fa-check"></i></span> Registra Allenatore
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

</body>
</html>
