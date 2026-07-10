<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Nuova Attività</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand">
                <a class="navbar-item" href="dashboard-admin">
                    <strong class="is-size-4" style="color: #AFAFE2;">GymFly 🏋️‍♂️</strong>
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-eight-tenths-tablet is-two-thirds-desktop">
                    
                    <div class="mb-5">
                        <a href="dashboard-admin" class="button is-ghost has-text-grey">
                            <span class="icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Torna alla Dashboard</span>
                        </a>
                    </div>

                    <div class="card p-5">
                        <div class="has-text-centered mb-5">
                            <span class="icon is-large has-text-link">
                                <i class="fas fa-dumbbell fa-3x"></i>
                            </span>
                            <h1 class="title is-3 mt-3 style-theme-text">Nuova Attività</h1>
                            <p class="subtitle is-6 has-text-grey mt-1">Registra una nuova disciplina sportiva nel catalogo generale della palestra.</p>
                        </div>

                        <form action="crea-attivita" method="POST">
                            
                            <div class="field">
                                <label class="label">Nome Attività *</label>
                                <div class="control">
                                    <input class="input" type="text" name="nome" required placeholder="Es: Pilates, Spinning, Zumba...">
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Descrizione *</label>
                                <div class="control">
                                    <textarea class="textarea" name="descrizione" required placeholder="Fornisci una breve descrizione delle finalità e svolgimento del corso..."></textarea>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Numero Massimo Partecipanti *</label>
                                <div class="control">
                                    <input class="input" type="number" name="max_partecipanti" required placeholder="Es: 15" min="1">
                                </div>
                            </div>

                            <div class="field mt-5">
                                <button class="button is-gymfly is-fullwidth" type="submit">
                                    <span class="icon mr-1"><i class="fas fa-check"></i></span> Crea Attività
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
