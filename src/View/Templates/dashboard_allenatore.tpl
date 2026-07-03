<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Dashboard Allenatore</title>
    <link class="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
    <link class="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            background: linear-gradient(135deg, #99CDEA 0%, #C5E0FC 100%);
            color: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(153, 205, 234, 0.2);
        }
        .trainer-card {
            background-color: #FFFFFF;
            border: 2px solid #99CDEA;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            margin-bottom: 1.5rem;
        }
        .control-box {
            background-color: #FFFFFF;
            border: 2px solid #AFAFE2;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 16px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
        }
        .button.is-gymfly {
            background-color: #99CDEA;
            color: #FFFFFF;
            border-radius: 12px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .button.is-gymfly:hover {
            background-color: #C5E0FC;
            color: #1e3a8a;
        }
        .tag.is-trainer-theme {
            background-color: #D0D0F5;
            color: #4a4a8a;
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
                <a class="navbar-item" href="./">
                    <strong class="is-size-4" style="color: #AFAFE2;">GymFly 🏋️‍♂️</strong>
                </a>
            </div>
            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="buttons">
                        <span class="tag is-medium is-trainer-theme mr-3">
                            <i class="fas fa-user-ninja mr-2"></i> Allenatore
                        </span>
                        <a href="logout" class="button is-danger is-light">
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
                        <h1 class="title is-2 has-text-white mb-2">Ciao, Coach {$utente->getNome()}!</h1>
                        <p class="subtitle is-5 has-text-white-ter">Prepara nuove schede di allenamento e segui i tuoi atleti</p>
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
                
                <!-- AZIONI SCHEDE ED ESERCIZI -->
                <div class="column is-4">
                    <div class="control-box">
                        <h3 class="title is-4 mb-4" style="color: #AFAFE2;"><i class="fas fa-tasks mr-2"></i> Logica Allenamento</h3>
                        
                        <div class="buttons">
                            <button class="button is-gymfly is-fullwidth mb-3">
                                <i class="fas fa-plus-circle mr-2"></i> Crea Nuovo Esercizio
                            </button>
                            <button class="button is-link is-light is-fullwidth mb-3" style="border-radius: 12px;">
                                <i class="fas fa-file-medical mr-2"></i> Crea Nuova Scheda
                            </button>
                        </div>
                        <hr>
                        <h4 class="subtitle is-6 has-text-grey-dark">Esercizi Registrati</h4>
                        <div class="tags">
                            {foreach $esercizi as $esercizio}
                                <span class="tag is-light">{$esercizio->getNome()}</span>
                            {foreachelse}
                                <span class="has-text-grey-light is-size-7">Nessun esercizio presente</span>
                            {/foreach}
                        </div>
                    </div>
                </div>

                <!-- ELENCO CLIENTI ASSEGNATI -->
                <div class="column is-8">
                    <div class="control-box">
                        <h3 class="title is-4 mb-4" style="color: #99CDEA;"><i class="fas fa-running mr-2"></i> Atleti / Clienti</h3>
                        
                        <div class="table-container">
                            <table class="table is-fullwidth is-hoverable">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Abbonamento</th>
                                        <th class="has-text-right">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $clienti as $cliente}
                                        <tr>
                                            <td><strong>{$cliente->getNome()} {$cliente->getCognome()}</strong></td>
                                            <td>{$cliente->getEmail()}</td>
                                            <td>
                                                {if $cliente->isAbbonamentoAttivo()}
                                                    <span class="tag is-success is-light">Attivo</span>
                                                {else}
                                                    <span class="tag is-danger is-light">Inattivo</span>
                                                {/if}
                                            </td>
                                            <td class="has-text-right">
                                                <div class="buttons is-right">
                                                    <a href="visualizza-profilo?id={$cliente->getId()}" class="button is-small is-gymfly">
                                                        <i class="fas fa-folder-open mr-2"></i> Scheda & Progressi
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    {foreachelse}
                                        <tr>
                                            <td colspan="4" class="has-text-centered has-text-grey py-5">Nessun cliente registrato nel sistema.</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

</body>
</html>
