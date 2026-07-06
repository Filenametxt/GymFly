<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Dashboard Amministratore</title>
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
                    <strong class="is-size-4" style="color: #AFAFE2;">GymFly 🏋️‍♂️</strong>
                </a>
            </div>
            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="buttons">
                        <span class="tag is-medium is-admin-theme mr-3">
                            <i class="fas fa-user-shield mr-2"></i> Amministratore
                        </span>
                        <a href="cambia-password" class="button is-link is-light mr-2">
                            <i class="fas fa-key mr-2"></i> Password
                        </a>
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
            <div class="dashboard-header-admin">
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

                        <div class="table-container-custom">
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
                        <div class="has-text-right mt-3">
                            <a href="clienti" class="button is-small is-link is-light">
                                <span>Vedi Tutti i Clienti</span> <span class="icon"><i class="fas fa-arrow-right"></i></span>
                            </a>
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

                        <div class="table-container-custom">
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
                        <div class="has-text-right mt-3">
                            <a href="allenatori" class="button is-small is-link is-light">
                                <span>Vedi Tutti gli Allenatori</span> <span class="icon"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

</body>
</html>
