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
    <section class="section">
        <div class="container">
            <div class="columns is-centered">
                <div class="column is-six-fifths-tablet is-half-desktop">
                    
                    <div class="mb-5">
                        <a href="profilo" class="button is-ghost has-text-grey">
                            <span class="icon"><i class="fas fa-arrow-left"></i></span>
                            <span>Torna al Profilo</span>
                        </a>
                    </div>

                    <div class="card p-5">
                        <div class="has-text-centered mb-5">
                            <span class="icon is-large has-text-info">
                                <i class="fas fa-user-edit fa-3x"></i>
                            </span>
                            <h1 class="title is-3 mt-3 style-theme-text">Modifica Dati</h1>
                            <p class="subtitle is-6 has-text-grey mt-1">Aggiorna le tue informazioni di residenza, domicilio e pagamento</p>
                        </div>

                        <form action="modifica-anagrafica" method="POST">
                            <div class="field">
                                <label class="label">Nome</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="nome" value="{$utente->getNome()|escape}" required placeholder="Nome">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Cognome</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="cognome" value="{$utente->getCognome()|escape}" required placeholder="Cognome">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Residenza (Indirizzo Principale)</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="indirizzo" value="{$utente->getIndirizzo()|escape}" required placeholder="Via/Piazza, Numero, Città">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-home"></i>
                                    </span>
                                </div>
                            </div>

                            {if $isClient}
                            <div class="field">
                                <label class="label">Domicilio (Opzionale)</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="indirizzo_domicilio" value="{$utente->getIndirizzoDiDomicilio()|escape}" placeholder="Via/Piazza, Numero, Città">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="field">
                                <label class="label">Metodo di Pagamento</label>
                                <div class="control has-icons-left">
                                    <input class="input" type="text" name="metodo_pagamento" value="{$utente->getMetodoDiPagamento()|escape}" required placeholder="Carta di Credito, Paypal, Contanti, ecc.">
                                    <span class="icon is-small is-left">
                                        <i class="fas fa-credit-card"></i>
                                    </span>
                                </div>
                            </div>
                            {/if}

                            <div class="field mt-5">
                                <div class="control">
                                    <button class="button is-gymfly is-fullwidth" type="submit">
                                        <i class="fas fa-save mr-2"></i> Salva Modifiche
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
