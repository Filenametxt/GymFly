<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Dashboard Amministratore</title>
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
            background: linear-gradient(135deg, #AFAFE2 0%, #C5E0FC 100%);
            color: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(175, 175, 226, 0.2);
        }
        .stat-card {
            background-color: #FFFFFF;
            border: 2px solid #C5E0FC;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
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
        .tag.is-admin-theme {
            background-color: #C5E0FC;
            color: #1e3a8a;
            font-weight: bold;
        }
        .table-container {
            margin-top: 1rem;
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
                        <span class="tag is-medium is-admin-theme mr-3">
                            <i class="fas fa-user-shield mr-2"></i> Amministratore
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
                        <h1 class="title is-2 has-text-white mb-2">Benvenuto, {$utente->getNome()} {$utente->getCognome()}!</h1>
                        <p class="subtitle is-5 has-text-white-ter">Dashboard di Supervisione e Amministrazione di <strong>GymFly</strong></p>
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

            <!-- STATS -->
            <div class="columns mb-6">
                <div class="column">
                    <div class="stat-card">
                        <span class="icon is-large mb-2">
                            <i class="fas fa-users fa-2x" style="color: #AFAFE2 !important;"></i>
                        </span>
                        <h3 class="title is-4 mb-1">{$clienti|@count}</h3>
                        <p class="heading has-text-grey">Clienti Totali</p>
                    </div>
                </div>
                <div class="column">
                    <div class="stat-card">
                        <span class="icon is-large mb-2">
                            <i class="fas fa-user-tie fa-2x" style="color: #99CDEA !important;"></i>
                        </span>
                        <h3 class="title is-4 mb-1">{$allenatori|@count}</h3>
                        <p class="heading has-text-grey">Allenatori Attivi</p>
                    </div>
                </div>
                <div class="column">
                    <div class="stat-card">
                        <span class="icon is-large mb-2">
                            <i class="fas fa-dumbbell fa-2x" style="color: #AFAFE2 !important;"></i>
                        </span>
                        <h3 class="title is-4 mb-1">Attiva</h3>
                        <p class="heading has-text-grey">Stato Palestra</p>
                    </div>
                </div>
            </div>

            <!-- GESTIONE PANELS -->
            <div class="columns">
                
                <!-- GESTIONE CLIENTI -->
                <div class="column is-7">
                    <div class="control-box">
                        <div class="level mb-4">
                            <div class="level-left">
                                <h2 class="title is-4" style="color: #AFAFE2;"><i class="fas fa-users mr-2"></i> Gestione Clienti</h2>
                            </div>
                            <div class="level-right">
                                <button class="button is-gymfly is-small">
                                    <i class="fas fa-user-plus mr-2"></i> Nuovo Cliente
                                </button>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="table is-fullwidth is-hoverable">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>C.F.</th>
                                        <th class="has-text-right">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $clienti as $cliente}
                                        <tr>
                                            <td><strong>{$cliente->getNome()} {$cliente->getCognome()}</strong></td>
                                            <td>{$cliente->getEmail()}</td>
                                            <td><code class="is-size-7">{$cliente->getCF()}</code></td>
                                            <td class="has-text-right">
                                                <div class="buttons is-right">
                                                    <a href="visualizza-profilo?id={$cliente->getId()}" class="button is-small is-link is-light">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="button is-small is-danger is-light">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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

                <!-- GESTIONE ALLENATORI -->
                <div class="column is-5">
                    <div class="control-box">
                        <div class="level mb-4">
                            <div class="level-left">
                                <h2 class="title is-4" style="color: #99CDEA;"><i class="fas fa-user-tie mr-2"></i> Allenatori</h2>
                            </div>
                            <div class="level-right">
                                <button class="button is-gymfly is-small" style="background-color: #99CDEA !important;">
                                    <i class="fas fa-plus mr-2"></i> Nuovo Allenatore
                                </button>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="table is-fullwidth is-hoverable">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th class="has-text-right">Azione</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $allenatori as $allenatore}
                                        <tr>
                                            <td><strong>{$allenatore->getNome()} {$allenatore->getCognome()}</strong></td>
                                            <td>{$allenatore->getEmail()}</td>
                                            <td class="has-text-right">
                                                <button class="button is-small is-danger is-light">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    {foreachelse}
                                        <tr>
                                            <td colspan="3" class="has-text-centered has-text-grey py-5">Nessun allenatore registrato.</td>
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
