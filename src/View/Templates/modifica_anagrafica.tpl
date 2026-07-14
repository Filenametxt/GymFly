<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>GymFly - Modifica Dati Personali</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-container">
        {include file='sidebar.tpl'}
        <main class="app-content">
            <div class="container">
                
                <!-- TORNA AL PROFILO (Desktop) -->
                <div class="mb-5 is-hidden-mobile">
                    <a href="{if isset($isSelf) && !$isSelf}visualizza-profilo?id={$utente->getId()}{else}profilo{/if}" class="button is-ghost has-text-grey pl-0">
                        <span class="icon"><i class="fas fa-arrow-left"></i></span>
                        <span>Torna al Profilo</span>
                    </a>
                </div>

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
                            <h1 class="title is-2 has-text-white mb-2">Modifica Dati</h1>
                            <p class="subtitle is-5 has-text-white-ter">Aggiorna le tue informazioni di residenza, domicilio e pagamento</p>
                        </div>
                        <div class="column is-narrow">
                            <figure class="image is-96x96">
                                <span class="icon is-large has-text-white">
                                    <i class="fas fa-user-cog fa-5x"></i>
                                </span>
                            </figure>
                        </div>
                    </div>
                </div>

                <!-- ================= MOBILE HEADER (Con pulsante indietro integrato) ================= -->
                <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                    <div style="width: 45px;"></div>
                    <a href="{if isset($isSelf) && !$isSelf}visualizza-profilo?id={$utente->getId()}{else}profilo{/if}" class="button is-ghost p-0 mr-3" style="color: inherit; height: auto;">
                        <span class="icon is-medium"><i class="fas fa-arrow-left fa-lg"></i></span>
                    </a>
                    <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">MODIFICA DATI</strong>
                </div>

                <!-- FORM DI MODIFICA ESTESO (Coerente con cambia_password.tpl) -->
                <div class="columns">
                    <div class="column is-12">
                        <div class="box p-5">
                            <h3 class="title is-5 mb-5 style-theme-text">
                                <i class="fas fa-user-edit mr-2" style="color: var(--gymfly-primary);"></i> Informazioni Personali
                            </h3>
                            
                            <form action="modifica-anagrafica{if isset($isSelf) && !$isSelf}?id={$utente->getId()}{/if}" method="POST">
                                
                                <div class="field mb-4">
                                    <label class="label">Nome</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="nome" value="{$utente->getNome()|escape}" required placeholder="Nome">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-user" style="color: var(--gymfly-primary);"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field mb-4">
                                    <label class="label">Cognome</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="cognome" value="{$utente->getCognome()|escape}" required placeholder="Cognome">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-user" style="color: var(--gymfly-primary);"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field mb-4">
                                    <label class="label">Residenza (Indirizzo Principale)</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="indirizzo" value="{$utente->getIndirizzo()|escape}" required placeholder="Via/Piazza, Numero, Città">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-home" style="color: var(--gymfly-primary);"></i>
                                        </span>
                                    </div>
                                </div>

                                {if $isClient}
                                <div class="field mb-4">
                                    <label class="label">Domicilio (Opzionale)</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="indirizzo_domicilio" value="{$utente->getIndirizzoDiDomicilio()|escape}" placeholder="Via/Piazza, Numero, Città">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-map-marker-alt" style="color: var(--gymfly-primary);"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="field mb-5">
                                    <label class="label">Metodo di Pagamento</label>
                                    <div class="control has-icons-left">
                                        <input class="input" type="text" name="metodo_pagamento" value="{$utente->getMetodoDiPagamento()|escape}" required placeholder="Carta di Credito, Paypal, Contanti, ecc.">
                                        <span class="icon is-small is-left">
                                            <i class="fas fa-credit-card" style="color: var(--gymfly-primary);"></i>
                                        </span>
                                    </div>
                                </div>
                                {/if}

                                <!-- SUBMIT -->
                                <div class="field mt-5">
                                    <div class="control">
                                        <button class="button is-gymfly is-fullwidth" type="submit">
                                            <span class="icon"><i class="fas fa-save"></i></span>
                                            <span>Salva Modifiche</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>
