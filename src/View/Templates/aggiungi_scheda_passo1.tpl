<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Seleziona Cliente</title>
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
                <div class="column is-6">
                    
                    <div class="control-box">
                        <div class="has-text-centered mb-5">
                            <span class="icon is-large has-text-trainer-theme">
                                <i class="fas fa-user-check fa-3x" style="color: #AFAFE2;"></i>
                            </span>
                            <h1 class="title is-3 mt-3 style-theme-text">Nuova Scheda Allenamento</h1>
                            <p class="subtitle is-6 has-text-grey mt-1">Seleziona il cliente della tua palestra a cui associare la nuova scheda.</p>
                        </div>

                        <form action="crea-scheda" method="GET">
                            
                            <div class="field mb-5">
                                <label class="label">Scegli il Cliente *</label>
                                <div class="control has-icons-left">
                                    <div class="select is-fullwidth">
                                        <select name="cf" required>
                                            <option value="">-- Seleziona un Atleta --</option>
                                            {foreach $clienti as $cliente}
                                                <option value="{$cliente->getCF()}">
                                                    {$cliente->getNome()} {$cliente->getCognome()} ({$cliente->getCF()})
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                            </div>

                            <hr>
                            
                            <div class="field">
                                <button class="button is-gymfly is-fullwidth" type="submit">
                                    <i class="fas fa-plus mr-2"></i> Inizializza Scheda Vuota
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
