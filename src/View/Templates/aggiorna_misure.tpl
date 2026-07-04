<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Aggiorna Misure Corporee</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-eight-tenths-tablet is-two-thirds-desktop">
                    
                    <div class="mb-5">
                        <a href="profilo" class="button is-ghost has-text-grey">
                            <span class="icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Torna al Profilo</span>
                        </a>
                    </div>

                    <div class="card p-5">
                        <div class="has-text-centered mb-5">
                            <span class="icon is-large has-text-success">
                                <i class="fas fa-weight fa-3x"></i>
                            </span>
                            <h1 class="title is-3 mt-3 style-theme-text">Misure Corporee</h1>
                            <p class="subtitle is-6 has-text-grey mt-1">Registra le tue attuali misure fisiche per tracciare i tuoi progressi</p>
                        </div>

                        <form action="aggiorna-misure" method="POST">
                            
                            <h2 class="subtitle is-5 section-title">Parametri Generali</h2>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Peso (kg) *</label>
                                        <div class="control">
                                            <input class="input" type="number" step="0.1" name="peso" required placeholder="Es: 70.5">
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Altezza (cm) *</label>
                                        <div class="control">
                                            <input class="input" type="number" step="0.1" name="altezza" required placeholder="Es: 175">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="subtitle is-5 section-title mt-4">Circonferenze ed Arti (cm)</h2>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Bicipite Destro</label>
                                        <input class="input" type="number" step="0.1" name="bicipite_destro" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Bicipite Sinistro</label>
                                        <input class="input" type="number" step="0.1" name="bicipite_sinistro" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Tricipite Destro</label>
                                        <input class="input" type="number" step="0.1" name="tricipite_destro" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Tricipite Sinistro</label>
                                        <input class="input" type="number" step="0.1" name="tricipite_sinistro" placeholder="0.0">
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Coscia Destra</label>
                                        <input class="input" type="number" step="0.1" name="coscia_destra" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Coscia Sinistra</label>
                                        <input class="input" type="number" step="0.1" name="coscia_sinistra" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Polpaccio Destro</label>
                                        <input class="input" type="number" step="0.1" name="polpaccio_destro" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Polpaccio Sinistro</label>
                                        <input class="input" type="number" step="0.1" name="polpaccio_sinistro" placeholder="0.0">
                                    </div>
                                </div>
                            </div>

                            <h2 class="subtitle is-5 section-title mt-4">Tronco (cm)</h2>
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Petto</label>
                                        <input class="input" type="number" step="0.1" name="misura_petto" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Vita</label>
                                        <input class="input" type="number" step="0.1" name="misura_vita" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Spalle</label>
                                        <input class="input" type="number" step="0.1" name="misura_spalle" placeholder="0.0">
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Fianchi</label>
                                        <input class="input" type="number" step="0.1" name="misura_fianchi" placeholder="0.0">
                                    </div>
                                </div>
                            </div>

                            <div class="field mt-5">
                                <div class="control">
                                    <button class="button is-gymfly is-fullwidth" type="submit">
                                        <i class="fas fa-check mr-2"></i> Invia Misure
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</body>
</html>
