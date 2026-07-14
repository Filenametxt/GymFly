<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>Stato Operazione - GymFly</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <section class="hero is-fullheight">
        <div class="hero-body hero-body-centered">
            <div class="container has-text-centered">
                <div class="column is-6 is-offset-3">
                    <div class="notification {if $successo}is-success{else}is-danger{/if} is-light">
                        <p class="is-size-5">{$messaggio}</p>
                    </div>
                    <a href="{if isset($ritorno)}{$ritorno}{else}login{/if}" class="button is-gymfly mt-4">
                        {if isset($ritorno)}
                            {if $ritorno === 'login'}
                                Torna al Login
                            {elseif $ritorno === 'calendario' || $ritorno|truncate:10:"" === 'calendario'}
                                Torna al Calendario
                            {elseif $ritorno === 'visualizza-scheda' || $ritorno|truncate:17:"" === 'visualizza-scheda'}
                                Torna alla Scheda
                            {elseif $ritorno === 'profilo'}
                                Torna al Profilo
                            {else}
                                Torna alla Dashboard
                            {/if}
                        {else}
                            Torna al Login
                        {/if}
                    </a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>