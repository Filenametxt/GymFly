<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stato Operazione - GymFly</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {literal}
    <style>
        html, body {
            background-color: #F4F9F1;
            height: 100%;
        }
        .hero-body {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
    {/literal}
</head>
<body>
    <section class="hero is-fullheight">
        <div class="hero-body">
            <div class="container has-text-centered">
                <div class="column is-6 is-offset-3">
                    <div class="notification {if $successo}is-success{else}is-danger{/if} is-light">
                        <p class="is-size-5">{$messaggio}</p>
                    </div>
                    <a href="login" class="button is-link is-light mt-4">Torna al Login</a>
                </div>
            </div>
        </div>
    </section>
</body>
</html>