<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Area Personale Cliente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {literal}
    <style>
        html, body {
            background-color: #F4F9F1;
            min-height: 100%;
        }
        .navbar {
            background-color: #F4F9F1;
            border-bottom: 2px solid #99CDEA;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #AFAFE2 0%, #99CDEA 100%);
            color: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(175, 175, 226, 0.2);
        }
        .info-box {
            background-color: #FFFFFF;
            border: 2px solid #C5E0FC;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 16px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
            height: 100%;
        }
        .button.is-gymfly {
            background-color: #AFAFE2;
            color: #FFFFFF;
            border-radius: 12px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .button.is-gymfly:hover {
            background-color: #D0D0F5;
            color: #333333;
        }
        .tag.is-client-theme {
            background-color: #C5E0FC;
            color: #1e3a8a;
            font-weight: bold;
        }
    </style>
    {/literal}
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="index.php">
                    <strong class="is-size-4" style="color: #AFAFE2;">GymFly 🏋️‍♂️</strong>
                </a>
            </div>
            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="buttons">
                        <span class="tag is-medium is-client-theme mr-3">
                            <i class="fas fa-user mr-2"></i> Area Cliente
                        </span>
                        <a href="index.php?action=logout" class="button is-danger is-light">
                            <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section">
        <div class="container">
            
            <!-- HEADER -->
            <div class="dashboard-header">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-2 has-text-white mb-2">Benvenuto, {$utente->getNome()}!</h1>
                        <p class="subtitle is-5 has-text-white-ter">Resta in forma, monitora i tuoi progressi e controlla gli allenamenti</p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-user-circle fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <div class="columns">
                
                <!-- DATI PERSONALI ED ABBONAMENTO -->
                <div class="column is-6">
                    <div class="info-box">
                        <h3 class="title is-4 mb-4" style="color: #AFAFE2;"><i class="fas fa-id-card mr-2"></i> Profilo & Abbonamento</h3>
                        
                        <div class="block">
                            <p class="is-size-5 mb-2"><strong>Nome:</strong> {$utente->getNome()} {$utente->getCognome()}</p>
                            <p class="is-size-6 mb-1"><strong>Sesso:</strong> {$utente->getSesso()->value}</p>
                            <p class="is-size-6 mb-1"><strong>Email:</strong> {$utente->getEmail()}</p>
                            <p class="is-size-6 mb-1"><strong>Codice Fiscale:</strong> <code class="is-size-7">{$utente->getCF()}</code></p>
                            <p class="is-size-6"><strong>Telefono:</strong> {$utente->getTelefono()|default:'Non specificato'}</p>
                        </div>
                        
                        <hr>

                        <div class="notification {if $utente->isAbbonamentoAttivo()}is-success{else}is-danger{/if} is-light has-text-centered py-3">
                            <h4 class="title is-5 mb-2">
                                Abbonamento: {if $utente->isAbbonamentoAttivo()}Attivo{else}Scaduto{/if}
                            </h4>
                            <p class="is-size-7">Scadenza Iscrizione Palestra: 
                                <strong>
                                    {if $utente->getIscrizione()}
                                        {$utente->getIscrizione()->getDataFine()->format('d/m/Y')}
                                    {else}
                                        Non attiva
                                    {/if}
                                </strong>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- SCHEDA ALLENAMENTO E CERTIFICATO MEDICO -->
                <div class="column is-6">
                    <div class="info-box">
                        <h3 class="title is-4 mb-4" style="color: #99CDEA;"><i class="fas fa-heartbeat mr-2"></i> Stato Salute & Allenamento</h3>

                        <!-- CERTIFICATO MEDICO -->
                        <div class="box mb-4 py-3" style="border: 1px solid #99CDEA;">
                            <div class="level">
                                <div class="level-left">
                                    <div>
                                        <p class="is-size-6 mb-1"><strong>Certificato Medico</strong></p>
                                        <p class="is-size-7 has-text-grey">
                                            {if $utente->isCertificatoValido()}
                                                Valido fino al: <strong>{$utente->getCertificatoMedico()->getDataScadenza()->format('d/m/Y')}</strong>
                                            {else}
                                                <span class="has-text-danger">Mancante o Scaduto</span>
                                            {/if}
                                        </p>
                                    </div>
                                </div>
                                <div class="level-right">
                                    {if $utente->isCertificatoValido()}
                                        <span class="icon has-text-success"><i class="fas fa-check-circle fa-lg"></i></span>
                                    {else}
                                        <span class="icon has-text-danger"><i class="fas fa-exclamation-circle fa-lg"></i></span>
                                    {/if}
                                </div>
                            </div>
                        </div>

                        <!-- SCHEDA -->
                        <div class="box mb-5 py-3" style="border: 1px solid #AFAFE2;">
                            <div class="level">
                                <div class="level-left">
                                    <div>
                                        <p class="is-size-6 mb-1"><strong>Scheda di Allenamento</strong></p>
                                        <p class="is-size-7 has-text-grey">
                                            {if $utente->getScheda()}
                                                Nome scheda: <strong>{$utente->getScheda()->getNome()}</strong>
                                            {else}
                                                Nessuna scheda attiva assegnata
                                            {/if}
                                        </p>
                                    </div>
                                </div>
                                <div class="level-right">
                                    {if $utente->getScheda()}
                                        <button class="button is-small is-gymfly">Visualizza</button>
                                    {else}
                                        <span class="has-text-grey-light is-size-7">Nessuna scheda</span>
                                    {/if}
                                </div>
                            </div>
                        </div>

                        <!-- AZIONI RAPIDE -->
                        <h4 class="subtitle is-6 mb-2 has-text-grey-dark">Scorciatoie Area Personale</h4>
                        <div class="buttons">
                            <a href="index.php?action=profilo" class="button is-small is-link is-light">
                                <i class="fas fa-user-edit mr-2"></i> Vedi Dettagli & Modifica Dati
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

</body>
</html>
