<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Nuove Misure Corporee</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .custom-mobile-container {
            max-width: 550px;
            margin: 0 auto;
        }
        .form-section-title {
            color: var(--gymfly-text);
            border-bottom: 2px solid var(--gymfly-accent);
            padding-bottom: 0.3rem;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            <div class="container custom-mobile-container">
                
                <div class="mb-4">
                    <a href="aggiorna-misure{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" class="button is-ghost has-text-grey pl-0">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Annulla</span>
                    </a>
                </div>

                <div class="box p-5" style="border: 2px solid var(--gymfly-accent); border-radius: 15px; background-color: var(--gymfly-card-bg); box-shadow: 0 8px 16px rgba(0,0,0,0.02) !important;">
                    <div class="has-text-centered mb-4">
                        <span class="icon is-large" style="color: var(--gymfly-primary);">
                            <i class="fas fa-weight fa-3x"></i>
                        </span>
                        <h2 class="title is-4 style-theme-text mt-2">Registra Nuove Misure</h2>
                        <p class="subtitle is-6 has-text-grey mt-1">Registra i parametri fisici correnti per tenere traccia dei tuoi progressi</p>
                    </div>

                    <form action="inserisci-misure{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" method="POST">
                        
                        <!-- Parametri Generali -->
                        <h3 class="is-size-5 form-section-title">Parametri Generali</h3>
                        <div class="field">
                            <label class="label">Peso (kg) *</label>
                            <div class="control">
                                <input class="input" type="number" step="0.1" name="peso" required value="{if $ultimaMisure}{$ultimaMisure->getPeso()}{/if}" placeholder="Es: 72.5">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Altezza (cm) *</label>
                            <div class="control">
                                <input class="input" type="number" step="0.1" name="altezza" required value="{if $ultimaMisure}{$ultimaMisure->getAltezza()}{/if}" placeholder="Es: 178">
                            </div>
                        </div>

                        <!-- Misure Parte Superiore -->
                        <h3 class="is-size-5 form-section-title">Parte Superiore (cm)</h3>
                        <div class="columns is-mobile">
                            <div class="column">
                                <label class="label is-size-7">Bicipite Dx</label>
                                <input class="input" type="number" step="0.1" name="bicipite_destro" placeholder="Bic. Dx" value="{if $ultimaMisure}{$ultimaMisure->getBicipiteDestro()}{/if}">
                            </div>
                            <div class="column">
                                <label class="label is-size-7">Bicipite Sx</label>
                                <input class="input" type="number" step="0.1" name="bicipite_sinistro" placeholder="Bic. Sx" value="{if $ultimaMisure}{$ultimaMisure->getBicipiteSinistro()}{/if}">
                            </div>
                        </div>
                        <div class="columns is-mobile">
                            <div class="column">
                                <label class="label is-size-7">Tricipite Dx</label>
                                <input class="input" type="number" step="0.1" name="tricipite_destro" placeholder="Tri. Dx" value="{if $ultimaMisure}{$ultimaMisure->getTricipiteDestro()}{/if}">
                            </div>
                            <div class="column">
                                <label class="label is-size-7">Tricipite Sx</label>
                                <input class="input" type="number" step="0.1" name="tricipite_sinistro" placeholder="Tri. Sx" value="{if $ultimaMisure}{$ultimaMisure->getTricipiteSinistro()}{/if}">
                            </div>
                        </div>
                        <div class="columns is-mobile">
                            <div class="column">
                                <label class="label is-size-7">Petto</label>
                                <input class="input" type="number" step="0.1" name="misura_petto" placeholder="Petto" value="{if $ultimaMisure}{$ultimaMisure->getMisuraPetto()}{/if}">
                            </div>
                            <div class="column">
                                <label class="label is-size-7">Spalle</label>
                                <input class="input" type="number" step="0.1" name="misura_spalle" placeholder="Spalle" value="{if $ultimaMisure}{$ultimaMisure->getMisuraSpalle()}{/if}">
                            </div>
                        </div>

                        <!-- Misure Parte Inferiore & Tronco -->
                        <h3 class="is-size-5 form-section-title">Parte Inferiore & Tronco (cm)</h3>
                        <div class="columns is-mobile">
                            <div class="column">
                                <label class="label is-size-7">Coscia Dx</label>
                                <input class="input" type="number" step="0.1" name="coscia_destra" placeholder="Coscia Dx" value="{if $ultimaMisure}{$ultimaMisure->getCosciaDestra()}{/if}">
                            </div>
                            <div class="column">
                                <label class="label is-size-7">Coscia Sx</label>
                                <input class="input" type="number" step="0.1" name="coscia_sinistra" placeholder="Coscia Sx" value="{if $ultimaMisure}{$ultimaMisure->getCosciaSinistra()}{/if}">
                            </div>
                        </div>
                        <div class="columns is-mobile">
                            <div class="column">
                                <label class="label is-size-7">Polpaccio Dx</label>
                                <input class="input" type="number" step="0.1" name="polpaccio_destro" placeholder="Polp. Dx" value="{if $ultimaMisure}{$ultimaMisure->getPolpaccioDestro()}{/if}">
                            </div>
                            <div class="column">
                                <label class="label is-size-7">Polpaccio Sx</label>
                                <input class="input" type="number" step="0.1" name="polpaccio_sinistro" placeholder="Polp. Sx" value="{if $ultimaMisure}{$ultimaMisure->getPolpaccioSinistro()}{/if}">
                            </div>
                        </div>
                        <div class="columns is-mobile">
                            <div class="column">
                                <label class="label is-size-7">Vita</label>
                                <input class="input" type="number" step="0.1" name="misura_vita" placeholder="Vita" value="{if $ultimaMisure}{$ultimaMisure->getMisuraVita()}{/if}">
                            </div>
                            <div class="column">
                                <label class="label is-size-7">Fianchi</label>
                                <input class="input" type="number" step="0.1" name="misura_fianchi" placeholder="Fianchi" value="{if $ultimaMisure}{$ultimaMisure->getPolpaccioSinistro()}{/if}">
                            </div>
                        </div>

                        <button class="button is-gymfly is-fullwidth mt-5" type="submit" style="border-radius: 10px;">
                            <span class="icon mr-1"><i class="fas fa-check"></i></span> Salva Parametri
                        </button>
                    </form>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
