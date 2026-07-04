<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Lista Clienti</title>
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

            <!-- SEARCH & HEADER BOX -->
            <div class="box mb-5">
                <div class="columns is-vcentered">
                    <div class="column is-6">
                        <h1 class="title is-3 style-theme-text"><i class="fas fa-users mr-3"></i> Elenco Clienti</h1>
                        <p class="subtitle is-6 has-text-grey mt-1">Lista completa degli iscritti della palestra</p>
                    </div>
                    <div class="column is-6">
                        <form action="clienti" method="POST">
                            <div class="field has-addons">
                                <div class="control is-expanded">
                                    <input class="input" type="text" name="search_query" placeholder="Cerca cliente per nome, cognome o email..." value="{if isset($smarty.post.search_query)}{$smarty.post.search_query}{/if}">
                                </div>
                                <div class="control">
                                    <button class="button is-gymfly" type="submit">
                                        <i class="fas fa-search mr-2"></i> Cerca
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- TABELLA CLIENTI -->
            <div class="box">
                <div class="table-container-custom">
                    <table class="table is-fullwidth is-hoverable">
                        <thead>
                            <tr>
                                <th>Nome Cliente</th>
                                <th>Email</th>
                                <th>Codice Fiscale</th>
                                <th class="has-text-right">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $clienti as $c}
                                <tr>
                                    <td><strong>{$c.nome} {$c.cognome}</strong></td>
                                    <td>{$c.email}</td>
                                    <td><code class="is-size-7">{$c.cf}</code></td>
                                    <td class="has-text-right">
                                        <a href="visualizza-profilo?id={$c.id}" class="button is-small is-link is-light">
                                            <i class="fas fa-eye mr-2"></i> Vedi Profilo
                                        </a>
                                    </td>
                                </tr>
                            {foreachelse}
                                <tr>
                                    <td colspan="4" class="has-text-centered has-text-grey py-5">
                                        <span class="icon is-large mb-3"><i class="fas fa-search fa-2x"></i></span>
                                        <p>Nessun cliente corrispondente ai criteri di ricerca.</p>
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
