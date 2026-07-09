<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Bacheca Messaggi</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    {literal}
    <style>
        .message-card {
            border-left: 5px solid var(--gymfly-primary);
            transition: transform 0.2s ease;
        }
        .message-card:hover {
            transform: translateX(3px);
        }
        .message-sent-card {
            border-left: 5px solid var(--gymfly-secondary);
        }
    </style>
    {/literal}
</head>
<body>

    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            
            
            <div class="mb-5">
                <a href="{$ritorno}" class="button is-ghost has-text-grey">
                    <span class="icon"><i class="fas fa-arrow-left"></i></span>
                    <span>Torna alla Dashboard</span>
                </a>
            </div>

            <!-- HEADER BOX -->
            <div class="box mb-5">
                <h1 class="title is-3 style-theme-text"><i class="fas fa-envelope mr-3"></i> Bacheca Messaggi</h1>
                <p class="subtitle is-6 has-text-grey mt-1">Leggi i messaggi ricevuti o invia nuove comunicazioni a utenti e gruppi della palestra</p>
            </div>

            <div class="columns">
                
                <!-- COLONNA LISTA MESSAGGI (RICEVUTI & INVIATI) -->
                <div class="column {if $invioConsentito}is-7{else}is-12{/if}">
                    
                    <!-- MESSAGGI RICEVUTI -->
                    {if $ruolo !== 'amministratore'}
                    <div class="box">
                        <h2 class="title is-4 style-theme-text mb-4"><i class="fas fa-inbox mr-2"></i> Posta in Arrivo</h2>
                        
                        {foreach $messaggiRicevuti as $msg}
                            <div class="box message-card mb-3 p-4">
                                <div class="level mb-2">
                                    <div class="level-left">
                                        <div>
                                            <p class="is-size-6">
                                                Da: <strong>{$msg->getMittente()->getNome()} {$msg->getMittente()->getCognome()}</strong> 
                                                <span class="tag is-small is-light is-uppercase ml-2">{$msg->getMittente()->getRuolo()}</span>
                                            </p>
                                            <h4 class="title is-5 mt-1 mb-0">{$msg->getOggetto()}</h4>
                                        </div>
                                    </div>
                                </div>
                                <p class="has-text-grey-dark" style="white-space: pre-line;">{$msg->getContenuto()}</p>
                            </div>
                        {foreachelse}
                            <div class="has-text-centered py-5 has-text-grey">
                                <span class="icon is-large mb-2"><i class="fas fa-folder-open fa-2x"></i></span>
                                <p>Nessun messaggio nella posta in arrivo.</p>
                            </div>
                        {/foreach}
                    </div>
                    {/if}

                    <!-- MESSAGGI INVIATI (Solo se consentito l'invio) -->
                    {if $invioConsentito}
                        <div class="box mt-5">
                            <h2 class="title is-4 style-theme-text mb-4"><i class="fas fa-paper-plane mr-2"></i> Posta in Uscita</h2>
                            
                            {foreach $messaggiInviati as $msg}
                                <div class="box message-card message-sent-card mb-3 p-4">
                                    <div class="level mb-2">
                                        <div class="level-left">
                                            <div>
                                                <p class="is-size-7 has-text-grey">
                                                    A: 
                                                    {foreach $msg->getDestinatari() as $dest}
                                                        <strong>{$dest->getNome()} {$dest->getCognome()}</strong> ({$dest->getRuolo()}){if !$dest@last}, {/if}
                                                    {/foreach}
                                                </p>
                                                <h4 class="title is-5 mt-1 mb-0">{$msg->getOggetto()}</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="has-text-grey-dark" style="white-space: pre-line;">{$msg->getContenuto()}</p>
                                </div>
                            {foreachelse}
                                <div class="has-text-centered py-5 has-text-grey">
                                    <span class="icon is-large mb-2"><i class="fas fa-paper-plane fa-2x"></i></span>
                                    <p>Non hai ancora inviato nessun messaggio.</p>
                                </div>
                            {/foreach}
                        </div>
                    {/if}

                </div>

                <!-- COLONNA INVIO NUOVO MESSAGGIO (Solo per Admin e Trainer) -->
                {if $invioConsentito}
                    <div class="column is-5">
                        <div class="box">
                            <h2 class="title is-4 style-theme-text mb-4"><i class="fas fa-edit mr-2"></i> Scrivi Messaggio</h2>
                            
                            <form action="invia-messaggio" method="POST">
                                
                                <!-- OGGETTO -->
                                <div class="field">
                                    <label class="label">Oggetto</label>
                                    <div class="control">
                                        <input class="input" type="text" name="oggetto" placeholder="Oggetto del messaggio" required>
                                    </div>
                                </div>

                                <!-- CONTENUTO -->
                                <div class="field">
                                    <label class="label">Contenuto</label>
                                    <div class="control">
                                        <textarea class="textarea" name="contenuto" placeholder="Scrivi qui il tuo messaggio..." rows="5" required></textarea>
                                    </div>
                                </div>

                                <hr>

                                <!-- TIPO DESTINATARI -->
                                <div class="field">
                                    <label class="label">Tipologia Destinatario</label>
                                    <div class="control">
                                        <label class="radio mr-3">
                                            <input type="radio" name="destinatari_tipo" value="gruppo" checked onclick="document.getElementById('div-gruppo').style.display='block'; document.getElementById('div-selezionati').style.display='none';">
                                            Gruppo di Utenti
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="destinatari_tipo" value="selezionati" onclick="document.getElementById('div-gruppo').style.display='none'; document.getElementById('div-selezionati').style.display='block';">
                                            Utenti Singoli
                                        </label>
                                    </div>
                                </div>

                                <!-- SELEZIONE GRUPPO -->
                                <div id="div-gruppo" class="field">
                                    <label class="label">Seleziona Gruppo</label>
                                    <div class="control">
                                        <div class="select is-fullwidth">
                                            <select name="gruppo_tipo">
                                                <option value="tutti_clienti" selected>Tutti i Clienti</option>
                                                {if $ruolo === 'amministratore'}
                                                    <option value="tutti_allenatori">Tutti gli Allenatori</option>
                                                {/if}
                                                <option value="tutti_palestra">Tutti i membri della palestra</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- SELEZIONE UTENTI INDIVIDUALI (Nascosto di default) -->
                                <div id="div-selezionati" class="field" style="display: none;">
                                    <label class="label">Seleziona Destinatari (tieni premuto Ctrl / Cmd per selezione multipla)</label>
                                    <div class="control">
                                        <div class="select is-multiple is-fullwidth">
                                            <select name="destinatari_ids[]" multiple style="height: 150px;">
                                                {if !empty($clientiCandidati)}
                                                    <optgroup label="Clienti">
                                                        {foreach $clientiCandidati as $c}
                                                            <option value="{$c->getId()}">{$c->getNome()} {$c->getCognome()}</option>
                                                        {/foreach}
                                                    </optgroup>
                                                {/if}
                                                {if !empty($allenatoriCandidati)}
                                                    <optgroup label="Allenatori">
                                                        {foreach $allenatoriCandidati as $a}
                                                            <option value="{$a->getId()}">{$a->getNome()} {$a->getCognome()}</option>
                                                        {/foreach}
                                                    </optgroup>
                                                {/if}
                                                {if !empty($adminCandidati)}
                                                    <optgroup label="Amministratori">
                                                        {foreach $adminCandidati as $ad}
                                                            <option value="{$ad->getId()}">{$ad->getNome()} {$ad->getCognome()}</option>
                                                        {/foreach}
                                                    </optgroup>
                                                {/if}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- INVIA -->
                                <div class="field mt-5">
                                    <div class="control">
                                        <button type="submit" class="button is-gymfly is-fullwidth">
                                            <span class="icon"><i class="fas fa-paper-plane"></i></span>
                                            <span>Invia Messaggio</span>
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                {/if}

            </div>

        </main>
    </div>

</body>
</html>
