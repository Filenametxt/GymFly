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
                            Bacheca Messaggi
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            Leggi i messaggi ricevuti o invia nuove comunicazioni a utenti e gruppi della palestra
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <span class="icon is-large has-text-white" style="margin-right: 1.5rem;">
                            <i class="fas fa-envelope fa-3x"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">MESSAGGI</strong>
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
                        <div class="box {if $ruolo !== 'amministratore'}mt-5{/if}" style="{if $ruolo === 'amministratore'}height: calc(100% - 20px);{/if} display: flex; flex-direction: column;">
                            <h2 class="title is-4 style-theme-text mb-4"><i class="fas fa-paper-plane mr-2"></i> Posta in Uscita</h2>
                            <div style="flex-grow: 1; max-height: 520px; overflow-y: auto; padding-right: 0.5rem;">
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
                                        <div class="box p-3" id="gruppo-list-container" style="max-height: 200px; overflow-y: auto;">
                                            <label class="radio is-block mb-2">
                                                <input type="radio" name="gruppo_tipo" value="tutti_clienti" checked>
                                                Tutti i Clienti
                                            </label>
                                            {if $ruolo === 'amministratore'}
                                                <label class="radio is-block mb-2">
                                                    <input type="radio" name="gruppo_tipo" value="tutti_allenatori">
                                                    Tutti gli Allenatori
                                                </label>
                                            {/if}
                                            <label class="radio is-block mb-2">
                                                <input type="radio" name="gruppo_tipo" value="tutti_palestra">
                                                Tutti i membri della palestra
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- SELEZIONE UTENTI INDIVIDUALI (Nascosto di default) -->
                                <div id="div-selezionati" class="field" style="display: none;">
                                    <label class="label">Seleziona Destinatari</label>
                                    <div class="control">
                                        <div class="box p-3" style="max-height: 200px; overflow-y: auto;">
                                            {if !empty($clientiCandidati)}
                                                <p class="menu-label font-weight-bold mb-2">CLIENTI</p>
                                                {foreach $clientiCandidati as $c}
                                                    <label class="checkbox is-block mb-2">
                                                        <input type="checkbox" name="destinatari_ids[]" value="{$c->getId()}" class="chk-utente-singolo">
                                                        {$c->getNome()} {$c->getCognome()}
                                                    </label>
                                                {/foreach}
                                            {/if}
                                            {if !empty($allenatoriCandidati)}
                                                <p class="menu-label font-weight-bold mb-2 mt-3">ALLENATORI</p>
                                                {foreach $allenatoriCandidati as $a}
                                                    <label class="checkbox is-block mb-2">
                                                        <input type="checkbox" name="destinatari_ids[]" value="{$a->getId()}" class="chk-utente-singolo">
                                                        {$a->getNome()} {$a->getCognome()}
                                                    </label>
                                                {/foreach}
                                            {/if}
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
