<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Dashboard Allenatore</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">

            <!-- HEADER (Coerente con le altre Dashboard) -->
            <div class="dashboard-header-trainer">
                <div class="columns is-vcentered is-mobile">
                    <div class="column">
                        <strong class="is-size-6 has-text-white-ter" style="letter-spacing: 1px; text-transform: uppercase;">Allenatore - Home</strong>
                        <h1 class="title is-2 has-text-white mt-1 mb-2">Ciao, Coach {$utente->getNome()}!</h1>
                        <p class="subtitle is-5 has-text-white-ter">Prepara nuove schede di allenamento e segui i tuoi atleti</p>
                    </div>
                    <div class="column is-narrow is-hidden-mobile">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-user-circle fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- RESPONSIVE GRID (Side-by-side su PC, impilato su Mobile) -->
            <div class="columns">
                
                <!-- COLONNA SINISTRA (Statistiche, Azioni e Messaggi) -->
                <div class="column is-12-mobile is-7-desktop">
                    
                    <!-- Semaforo Schede Allenamento (Stile Coerente con Dashboard Admin) -->
                    <div class="box mb-5">
                        <h2 class="title is-5 mb-4 style-theme-text">
                            <i class="fas fa-heartbeat mr-2" style="color: #ff3860;"></i> Controllo Schede Allenamento
                        </h2>
                        <div class="columns is-mobile is-vcentered" style="margin-top: 0;">
                            <div class="column has-text-centered">
                                <div class="notification is-danger is-light p-3" style="border-radius: 12px; border: 1px solid #ff3860;">
                                    <span class="icon is-large"><i class="fas fa-times-circle fa-2x"></i></span>
                                    <h4 class="title is-4 mt-2 mb-0">{$schede_scadute}</h4>
                                    <p class="is-size-7 font-weight-bold">SCADUTE</p>
                                </div>
                            </div>
                            <div class="column has-text-centered">
                                <div class="notification is-warning is-light p-3" style="border-radius: 12px; border: 1px solid #ffdd57;">
                                    <span class="icon is-large"><i class="fas fa-exclamation-triangle fa-2x"></i></span>
                                    <h4 class="title is-4 mt-2 mb-0">{$richieste_scheda}</h4>
                                    <p class="is-size-7 font-weight-bold">RICHIESTE</p>
                                </div>
                            </div>
                            <div class="column has-text-centered">
                                <div class="notification is-info is-light p-3" style="border-radius: 12px; border: 1px solid #209cee;">
                                    <span class="icon is-large"><i class="fas fa-check-circle fa-2x"></i></span>
                                    <h4 class="title is-4 mt-2 mb-0">{$schede_in_regola}</h4>
                                    <p class="is-size-7 font-weight-bold">IN REGOLA</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Widget Azioni Rapide (Stile Coerente con Dashboard Admin) -->
                    <div class="box mb-5">
                        <h2 class="title is-5 mb-4 style-theme-text">
                            <i class="fas fa-bolt mr-2" style="color: #ffdd57;"></i> Azioni Rapide
                        </h2>
                        <div class="columns is-mobile" style="margin-top: -0.5rem;">
                            <!-- Nuova Scheda -->
                            <div class="column is-4 has-text-centered py-2">
                                <a href="clienti" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent);">
                                    <span class="icon is-medium mb-1"><i class="fas fa-file-medical fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); white-space: normal; line-height: 1.1;">Nuova Scheda</span>
                                </a>
                            </div>
                            <!-- Aggiungi Esercizio -->
                            <div class="column is-4 has-text-centered py-2">
                                <a href="crea-esercizio" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent);">
                                    <span class="icon is-medium mb-1"><i class="fas fa-dumbbell fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); white-space: normal; line-height: 1.1;">Nuovo Esercizio</span>
                                </a>
                            </div>
                            <!-- Cerca Atleta -->
                            <div class="column is-4 has-text-centered py-2">
                                <a href="clienti" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent);">
                                    <span class="icon is-medium mb-1"><i class="fas fa-search fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); white-space: normal; line-height: 1.1;">Cerca Atleta</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Widget Messaggi Preimpostati (Stile Coerente con Dashboard Admin) -->
                    <div class="box mb-5">
                        <h2 class="title is-5 mb-4 style-theme-text">
                            <i class="fas fa-comment-alt mr-2" style="color: #3273dc;"></i> Messaggi Preimpostati
                        </h2>
                        <div class="columns is-mobile" style="margin-top: -0.5rem;">
                            <!-- Chiusura Palestra (Invio Automatico) -->
                            <form action="invia-messaggio" method="POST" class="column is-4 has-text-centered py-2" style="margin: 0; padding: 0.75rem;">
                                <input type="hidden" name="oggetto" value="Avviso Chiusura Palestra">
                                <input type="hidden" name="contenuto" value="Gentili clienti, vi comunichiamo che la palestra rimarrà chiusa temporaneamente per motivi tecnici straordinari. Ci scusiamo per il disagio.">
                                <input type="hidden" name="destinatari_tipo" value="gruppo">
                                <input type="hidden" name="gruppo_tipo" value="tutti_clienti">
                                <button type="submit" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent); cursor: pointer; white-space: normal; padding: 0;">
                                    <span class="icon is-medium mb-1"><i class="fas fa-door-closed fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); line-height: 1.1;">Chiusura Palestra</span>
                                </button>
                            </form>
                            
                            <!-- Guasto Attrezzi (Invio Automatico) -->
                            <form action="invia-messaggio" method="POST" class="column is-4 has-text-centered py-2" style="margin: 0; padding: 0.75rem;">
                                <input type="hidden" name="oggetto" value="Avviso Guasto Tecnico">
                                <input type="hidden" name="contenuto" value="Gentili clienti, vi avvisiamo che a causa di un guasto tecnico temporaneo ad alcune attrezzature, l'accesso a determinate sale potrebbe essere limitato. Lavoriamo al ripristino.">
                                <input type="hidden" name="destinatari_tipo" value="gruppo">
                                <input type="hidden" name="gruppo_tipo" value="tutti_clienti">
                                <button type="submit" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent); cursor: pointer; white-space: normal; padding: 0;">
                                    <span class="icon is-medium mb-1"><i class="fas fa-tools fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); line-height: 1.1;">Guasto Attrezzi</span>
                                </button>
                            </form>
                            
                            <!-- Nuovo Messaggio -->
                            <div class="column is-4 has-text-centered py-2">
                                <a href="messaggi" class="button is-light is-medium is-flex is-flex-direction-column is-justify-content-center" style="height: 90px; width: 100%; border-radius: 12px; border: 1px solid var(--gymfly-accent);">
                                    <span class="icon is-medium mb-1"><i class="fas fa-comment-dots fa-lg" style="color: var(--gymfly-text);"></i></span>
                                    <span class="is-size-7 font-weight-bold" style="color: var(--gymfly-text); line-height: 1.1;">Nuovo Messaggio</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- BOX ULTIMI MESSAGGI -->
                    <div class="box">
                        <h3 class="title is-4 mb-4 style-theme-text">
                            <i class="fas fa-paper-plane mr-2" style="color: var(--gymfly-secondary);"></i> Ultimi Messaggi Inviati
                        </h3>
                        
                        <div class="message-list">
                            {foreach $ultimi_messaggi as $msg}
                                <div class="p-3 mb-3" style="background-color: var(--gymfly-bg); border-radius: 12px; border-left: 4px solid var(--gymfly-primary);">
                                    <div class="is-flex is-justify-content-between">
                                        <strong class="is-size-6" style="color: var(--gymfly-text);">{$msg->getOggetto()}</strong>
                                        <span class="tag is-small is-light" style="color: var(--gymfly-text);">A: {$msg->getDestinatari()|@count} utenti</span>
                                    </div>
                                    <p class="is-size-7 mt-1 has-text-grey-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: var(--gymfly-text);">{$msg->getContenuto()}</p>
                                </div>
                            {foreachelse}
                                <div class="has-text-centered has-text-grey py-5">
                                    <span class="icon is-large"><i class="fas fa-comment-slash fa-2x"></i></span>
                                    <p class="is-size-6 mt-2">Nessun messaggio inviato di recente.</p>
                                </div>
                            {/foreach}
                        </div>
                    </div>

                </div>

                <!-- COLONNA DESTRA (Widget Oggi in Palestra) -->
                <div class="column is-12-mobile is-5-desktop">
                    <div class="box" style="height: 100%;">
                        <div class="is-flex is-justify-content-between is-align-items-center mb-4">
                            <h3 class="title is-4 mb-0 style-theme-text">
                                <i class="fas fa-calendar-day mr-2" style="color: var(--gymfly-secondary);"></i> Oggi in Palestra
                            </h3>
                            <span class="tag is-link is-light font-weight-bold">({$eventi_oggi|@count} Eventi)</span>
                        </div>
                        
                        <div class="event-list">
                            {foreach $eventi_oggi as $evento}
                                <div class="p-3 mb-3" style="background-color: var(--gymfly-bg); border-radius: 12px; border-left: 5px solid {$evento.colore};">
                                    <div class="is-flex is-align-items-center is-justify-content-between">
                                        <strong class="is-size-6" style="color: var(--gymfly-text);">{$evento.nome}</strong>
                                        <span class="is-size-7 has-text-grey" style="font-weight: 600;">{$evento.orario}</span>
                                    </div>
                                    <div class="mt-2 is-flex is-align-items-center is-size-7 has-text-grey-dark">
                                        <span class="icon is-small mr-1"><i class="fas fa-user-ninja" style="color: var(--gymfly-text);"></i></span>
                                        <span>Trainer: <strong>{$evento.allenatore}</strong></span>
                                    </div>
                                </div>
                            {foreachelse}
                                <div class="has-text-centered has-text-grey py-5">
                                    <span class="icon is-large mb-2"><i class="fas fa-calendar-times fa-2x"></i></span>
                                    <p class="is-size-6">Nessuna attività pianificata per oggi.</p>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

</body>
</html>
