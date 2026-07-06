<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Lista Allenatori</title>
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
                    <a href="logout" class="button is-danger is-light">
                        <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section">
        <div class="container">
            
            <div class="mb-5">
                <a href="{$ritorno}" class="button is-ghost has-text-grey">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Torna alla Dashboard</span>
                </a>
            </div>

            <!-- HEADER BOX -->
            <div class="box mb-5">
                <h1 class="title is-3 style-theme-text"><i class="fas fa-user-tie mr-3"></i> Staff Allenatori</h1>
                <p class="subtitle is-6 has-text-grey mt-1">Elenco dei preparatori atletici abilitati per la palestra</p>
            </div>

            <!-- TABELLA ALLENATORI -->
            <div class="box">
                <div class="table-container-custom">
                    <table class="table is-fullwidth is-hoverable">
                        <thead>
                            <tr>
                                <th>Nome Allenatore</th>
                                <th>Email</th>
                                <th>Codice Fiscale</th>
                                <th class="has-text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $allenatori as $a}
                                <tr>
                                    <td><strong>{$a.nome} {$a.cognome}</strong></td>
                                    <td>{$a.email}</td>
                                    <td><code class="is-size-7">{$a.cf}</code></td>
                                    <td class="has-text-right">
                                        <a href="visualizza-profilo?id={$a.id}" class="button is-small is-link is-light">
                                            <i class="fas fa-eye mr-2"></i> Vedi Profilo
                                        </a>
                                    </td>
                                </tr>
                            {foreachelse}
                                <tr>
                                    <td colspan="4" class="has-text-centered has-text-grey py-5">
                                        <span class="icon is-large mb-3"><i class="fas fa-search fa-2x"></i></span>
                                        <p>Nessun allenatore registrato in questa palestra.</p>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

</body>
</html>
