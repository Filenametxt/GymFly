<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Registrazione Amministratore & Palestra</title>
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
                    <strong class="is-size-4" style="color: #AFAFE2;">GymFly</strong>
                </a>
            </div>

            <div class="navbar-end">
                <div class="navbar-items is-flex is-align-items-center" style="height: 100%;">
                    <a class="navbar-item is-tab is-active-register px-4 py-2 mr-2" href="registrazione">
                        <span>SIGN IN</span>
                        <span class="icon is-small ml-2">
                            <i class="fas fa-user-plus"></i>
                        </span>
                    </a>
                    <a class="navbar-item is-tab px-4 py-2" href="login">
                        <span>LOG IN</span>
                        <span class="icon is-small ml-2">
                            <i class="fas fa-user"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="hero is-fullheight-with-navbar">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-centered">
                    <div class="column is-8-tablet is-6-desktop">
                        
                        <div class="register-box">
                            <h3 class="title is-3 has-text-centered mb-5" style="color: #AFAFE2; letter-spacing: 2px;">REGISTRAZIONE</h3>

                            <!-- Step Indicator -->
                            <div class="step-indicator">
                                <div class="step is-active" id="indicator-1">1. Dati Amministratore</div>
                                <div class="step" id="indicator-2">2. Dati Palestra</div>
                            </div>

                            <form action="registrazione" method="POST" id="registerForm">
                                
                                <!-- STEP 1: Dati Amministratore -->
                                <div class="form-step is-active" id="step-1">
                                    <h4 class="title is-5 mb-4 has-text-grey-dark">Informazioni Personali</h4>
                                    
                                    <div class="columns">
                                        <div class="column is-6">
                                            <div class="field">
                                                <label class="label is-small">Nome *</label>
                                                <div class="control">
                                                    <input class="input" type="text" name="nome" placeholder="Mario" required id="field-nome">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column is-6">
                                            <div class="field">
                                                <label class="label is-small">Cognome *</label>
                                                <div class="control">
                                                    <input class="input" type="text" name="cognome" placeholder="Rossi" required id="field-cognome">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="field">
                                        <label class="label is-small">E-mail *</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="email" name="email" placeholder="mario.rossi@esempio.com" required id="field-email">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="columns">
                                        <div class="column is-6">
                                            <div class="field">
                                                <label class="label is-small">Codice Fiscale *</label>
                                                <div class="control">
                                                    <input class="input" type="text" name="cf" placeholder="RSSMRA80A01H501U" required maxlength="16" id="field-cf" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column is-6">
                                            <div class="field">
                                                <label class="label is-small">Sesso *</label>
                                                <div class="control">
                                                    <div class="select is-fullwidth">
                                                        <select name="sesso" required id="field-sesso">
                                                            <option value="" disabled selected>Seleziona</option>
                                                            <option value="M">Maschio</option>
                                                            <option value="F">Femmina</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="field">
                                        <label class="label is-small">Indirizzo Residenza *</label>
                                        <div class="control">
                                            <input class="input" type="text" name="indirizzo" placeholder="Via Roma 1, Milano" required id="field-indirizzo">
                                        </div>
                                    </div>

                                    <div class="columns">
                                        <div class="column is-6">
                                            <div class="field">
                                                <label class="label is-small">Telefono</label>
                                                <div class="control has-icons-left">
                                                    <input class="input" type="tel" name="telefono" placeholder="3331234567" id="field-telefono">
                                                    <span class="icon is-small is-left">
                                                        <i class="fas fa-phone"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column is-6">
                                            <div class="field">
                                                <label class="label is-small">Password *</label>
                                                <div class="control has-icons-left">
                                                    <input class="input" type="password" name="password" placeholder="Min. 8 caratteri" required minlength="8" id="field-password">
                                                    <span class="icon is-small is-left">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="field mt-5">
                                        <button type="button" class="button is-gymfly is-fullwidth" onclick="goToStep(2)">
                                            Avanti <i class="fas fa-chevron-right ml-2"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- STEP 2: Dati Palestra -->
                                <div class="form-step" id="step-2">
                                    <h4 class="title is-5 mb-4 has-text-grey-dark">Dati della Palestra da Gestire</h4>

                                    <div class="field">
                                        <label class="label is-small">Nome Palestra *</label>
                                        <div class="control">
                                            <input class="input" type="text" name="nome_palestra" placeholder="GymFly Central" required id="field-nome_palestra">
                                        </div>
                                    </div>

                                    <div class="field">
                                        <label class="label is-small">Indirizzo Palestra *</label>
                                        <div class="control">
                                            <input class="input" type="text" name="indirizzo_palestra" placeholder="Via delle Palestre 10, Milano" required id="field-indirizzo_palestra">
                                        </div>
                                    </div>

                                    <div class="columns">
                                        <div class="column is-6">
                                            <div class="field">
                                                <label class="label is-small">E-mail Palestra *</label>
                                                <div class="control has-icons-left">
                                                    <input class="input" type="email" name="email_palestra" placeholder="info@palestra.it" required id="field-email_palestra">
                                                    <span class="icon is-small is-left">
                                                        <i class="fas fa-envelope"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column is-6">
                                            <div class="field">
                                                <label class="label is-small">Telefono Palestra *</label>
                                                <div class="control has-icons-left">
                                                    <input class="input" type="tel" name="telefono_palestra" placeholder="02123456" required id="field-telefono_palestra">
                                                    <span class="icon is-small is-left">
                                                        <i class="fas fa-phone"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="columns mt-5">
                                        <div class="column is-6">
                                            <button type="button" class="button is-link is-light is-fullwidth" onclick="goToStep(1)">
                                                <i class="fas fa-chevron-left mr-2"></i> Indietro
                                            </button>
                                        </div>
                                        <div class="column is-6">
                                            <button type="submit" class="button is-gymfly is-fullwidth">
                                                Registra & Crea <i class="fas fa-check-circle ml-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Simple JavaScript for Step Management -->
    {literal}
    <script>
        function goToStep(step) {
            if (step === 2) {
                // Semplice controllo di validazione per lo Step 1 prima di procedere
                const fields = ['nome', 'cognome', 'email', 'cf', 'sesso', 'indirizzo', 'password'];
                let valid = true;

                fields.forEach(function(fieldId) {
                    const el = document.getElementById('field-' + fieldId);
                    if (!el.checkValidity()) {
                        el.reportValidity();
                        valid = false;
                    }
                });

                if (!valid) return;

                document.getElementById('step-1').classList.remove('is-active');
                document.getElementById('step-2').classList.add('is-active');
                document.getElementById('indicator-1').classList.remove('is-active');
                document.getElementById('indicator-2').classList.add('is-active');
            } else if (step === 1) {
                document.getElementById('step-2').classList.remove('is-active');
                document.getElementById('step-1').classList.add('is-active');
                document.getElementById('indicator-2').classList.remove('is-active');
                document.getElementById('indicator-1').classList.add('is-active');
            }
        }
    </script>
    {/literal}

</body>
</html>
