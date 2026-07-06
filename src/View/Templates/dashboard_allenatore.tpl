<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Dashboard Allenatore</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=1.3">
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
            <div class="dashboard-header-trainer">
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
                        <div class="has-text-right mt-3">
                            <a href="clienti" class="button is-small is-link is-light">
                                <span>Vedi Tutti i Clienti</span> <span class="icon"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

</body>
</html>
