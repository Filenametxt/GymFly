<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Nuove Misure Corporee</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        
        <main class="app-content">
            <!-- ================= DESKTOP HEADER ================= -->
            {assign var="headerClass" value="dashboard-header"}
            {if isset($smarty.session.ruolo_utente)}
                {if $smarty.session.ruolo_utente === 'amministratore'}
                    {assign var="headerClass" value="dashboard-header-admin"}
                {elseif $smarty.session.ruolo_utente === 'allenatore'}
                    {assign var="headerClass" value="dashboard-header-trainer"}
                {/if}
            {/if}
            <div class="{$headerClass} is-hidden-mobile">
                <div class="columns is-vcentered">
                    <div class="column">
                        <h1 class="title is-2 has-text-white mb-2">
                            Registra Nuove Misure
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Registra i parametri fisici correnti per tenere traccia dei progressi
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-weight fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px; flex-grow: 1;">REGISTRA MISURE</strong>
            </div>

            <!-- FORM PRINCIPALE -->
            <form action="inserisci-misure{if $smarty.session.ruolo_utente !== 'cliente'}?id={$utente->getId()}{/if}" method="POST">
                
                <!-- BOX 1: PARAMETRI GENERALI -->
                <div class="columns">
                    <div class="column is-12">
                        <div class="box">
                            <h3 class="title is-5 mb-4 style-theme-text">Parametri Generali</h3>
                            
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Peso (kg) *</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="peso" required value="{if $ultimaMisure}{$ultimaMisure->getPeso()}{/if}" placeholder="Es: 72.5">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-weight"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Altezza (cm) *</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="altezza" required value="{if $ultimaMisure}{$ultimaMisure->getAltezza()}{/if}" placeholder="Es: 178">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-ruler-vertical"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- BOX 2: PARTE SUPERIORE -->
                <div class="columns mt-4">
                    <div class="column is-12">
                        <div class="box">
                            <h3 class="title is-5 mb-4 style-theme-text">Parte Superiore (cm)</h3>
                            
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Bicipite Destro</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="bicipite_destro" placeholder="Es: 36.5" value="{if $ultimaMisure}{$ultimaMisure->getBicipiteDestro()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-dumbbell"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Bicipite Sinistro</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="bicipite_sinistro" placeholder="Es: 36.2" value="{if $ultimaMisure}{$ultimaMisure->getBicipiteSinistro()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-dumbbell"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Tricipite Destro</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="tricipite_destro" placeholder="Es: 31.0" value="{if $ultimaMisure}{$ultimaMisure->getTricipiteDestro()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-dumbbell"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Tricipite Sinistro</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="tricipite_sinistro" placeholder="Es: 30.8" value="{if $ultimaMisure}{$ultimaMisure->getTricipiteSinistro()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-dumbbell"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Misura Petto</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="misura_petto" placeholder="Es: 104" value="{if $ultimaMisure}{$ultimaMisure->getMisuraPetto()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-arrows-alt-h"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Misura Spalle</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="misura_spalle" placeholder="Es: 118" value="{if $ultimaMisure}{$ultimaMisure->getMisuraSpalle()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-arrows-alt-h"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- BOX 3: PARTE INFERIORE & TRONCO -->
                <div class="columns mt-4">
                    <div class="column is-12">
                        <div class="box">
                            <h3 class="title is-5 mb-4 style-theme-text">Parte Inferiore & Tronco (cm)</h3>
                            
                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Coscia Destra</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="coscia_destra" placeholder="Es: 54.5" value="{if $ultimaMisure}{$ultimaMisure->getCosciaDestra()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-walking"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Coscia Sinistra</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="coscia_sinistra" placeholder="Es: 54.0" value="{if $ultimaMisure}{$ultimaMisure->getCosciaSinistra()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-walking"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Polpaccio Destro</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="polpaccio_destro" placeholder="Es: 38.0" value="{if $ultimaMisure}{$ultimaMisure->getPolpaccioDestro()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-walking"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Polpaccio Sinistro</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="polpaccio_sinistro" placeholder="Es: 37.8" value="{if $ultimaMisure}{$ultimaMisure->getPolpaccioSinistro()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-walking"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="columns">
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Misura Vita</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="misura_vita" placeholder="Es: 82" value="{if $ultimaMisure}{$ultimaMisure->getMisuraVita()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-compress-alt"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="column">
                                    <div class="field">
                                        <label class="label">Misura Fianchi</label>
                                        <div class="control has-icons-left">
                                            <input class="input" type="number" step="0.1" name="misura_fianchi" placeholder="Es: 96" value="{if $ultimaMisure}{$ultimaMisure->getMisuraFianchi()}{/if}">
                                            <span class="icon is-small is-left">
                                                <i class="fas fa-compress-alt"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- PULSANTI SALVATAGGIO -->
                <div class="field is-grouped mt-5">
                    <div class="control is-expanded">
                        <button class="button is-gymfly is-fullwidth" type="submit">
                            <i class="fas fa-check-circle mr-2"></i> Salva Parametri
                        </button>
                    </div>
                </div>

            </form>
        </main>
    </div>

</body>
</html>
