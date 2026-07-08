<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Seleziona Scheda da Modificare</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=1.2">
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
                    <a href="dashboard-allenatore" class="button is-link is-light">
                        <i class="fas fa-arrow-left mr-2"></i> Torna alla Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-8">
                    
                    <div class="control-box">
                        <div class="has-text-centered mb-5">
                            <span class="icon is-large has-text-trainer-theme">
                                <i class="fas fa-edit fa-3x" style="color: #AFAFE2;"></i>
                            </span>
                            <h1 class="title is-3 mt-3 style-theme-text">Gestione Schede Allenamento</h1>
                            <p class="subtitle is-6 has-text-grey mt-1">Seleziona la scheda dell'atleta che desideri modificare o eliminare.</p>
                        </div>

                        <!-- ELENCO SCHEDE -->
                        <div class="table-container">
                            <table class="table is-fullwidth is-striped is-hoverable">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Nome Scheda</th>
                                        <th>Obiettivo</th>
                                        <th>Validità</th>
                                        <th class="has-text-right">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $schede as $sch}
                                        <tr>
                                            <td><strong>{$sch->getCliente()->getNome()} {$sch->getCliente()->getCognome()}</strong></td>
                                            <td>{$sch->getNome_scheda()}</td>
                                            <td>{$sch->getObiettivo()}</td>
                                            <td>{$sch->getData_inizio()->format('d/m/Y')} - {$sch->getData_fine()->format('d/m/Y')}</td>
                                            <td class="has-text-right">
                                                <div class="buttons is-right">
                                                    <a href="modifica-scheda?id={$sch->getId()}" class="button is-small is-gymfly">
                                                        <i class="fas fa-pencil-alt mr-1"></i> Gestisci
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    {foreachelse}
                                        <tr>
                                            <td colspan="5" class="has-text-centered has-text-grey py-5">
                                                <i class="fas fa-info-circle mr-2"></i>Nessuna scheda di allenamento registrata nella tua palestra.
                                            </td>
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
