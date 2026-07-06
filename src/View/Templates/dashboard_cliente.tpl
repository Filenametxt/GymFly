<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Area Cliente</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=1.2">
    {literal}
    <style>
        .custom-mobile-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .profile-greeting-box {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }
        .profile-avatar-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: var(--gymfly-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin-right: 1rem;
            border: 2px solid var(--gymfly-accent);
        }
        .navigation-box-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem;
            border-radius: 16px;
            border: 2px solid var(--gymfly-primary);
            background-color: var(--gymfly-card-bg);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 1.5rem;
        }
        .navigation-box-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .course-info-label {
            font-size: 0.85rem;
            color: var(--gymfly-text);
            opacity: 0.8;
        }
    </style>
    {/literal}
</head>
<body>

    <!-- NAVBAR MOBILE-FIRST -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand is-flex is-justify-content-between is-align-items-center w-100 px-3">
                
                <!-- Menu a Panino (Hamburger) -->
                <a role="button" class="navbar-burger ml-0" aria-label="menu" aria-expanded="false" data-target="client-navbar-menu" onclick="document.getElementById('client-navbar-menu').classList.toggle('is-active'); this.classList.toggle('is-active');">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>

                <!-- Titolo Centrato -->
                <div class="navbar-item py-0">
                    <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">CLIENTE HOME</strong>
                </div>

                <!-- Icona Calendario / Misure a destra -->
                <a href="aggiorna-misure" class="navbar-item p-0 has-text-link">
                    <span class="icon is-medium">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </span>
                </a>

            </div>

            <!-- Menu che si espande sotto al click del panino -->
            <div id="client-navbar-menu" class="navbar-menu">
                <div class="navbar-end">
                    <a href="dashboard-cliente" class="navbar-item">
                        <span class="icon mr-2"><i class="fas fa-home"></i></span> Home Dashboard
                    </a>
                    <a href="profilo" class="navbar-item">
                        <span class="icon mr-2"><i class="fas fa-user-edit"></i></span> Il mio Profilo
                    </a>
                    <a href="messaggi" class="navbar-item">
                        <span class="icon mr-2"><i class="fas fa-envelope"></i></span> Bacheca Messaggi
                    </a>
                    <a href="cambia-password" class="navbar-item">
                        <span class="icon mr-2"><i class="fas fa-key"></i></span> Cambia Password
                    </a>
                    <hr class="navbar-divider">
                    <a href="logout" class="navbar-item has-text-danger">
                        <span class="icon mr-2"><i class="fas fa-sign-out-alt"></i></span> Log Out
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT CONTAINER -->
    <section class="section px-3">
        <div class="container custom-mobile-container">
            
            <!-- PROFILO GREETING (Avatar e Ciao Nome!) -->
            <div class="profile-greeting-box">
                <div class="profile-avatar-circle">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h2 class="title is-4 style-theme-text mb-0">Ciao, {$utente->getNome()}!</h2>
                    <p class="subtitle is-6 has-text-grey mt-1">Pronto per l'allenamento di oggi?</p>
                </div>
            </div>

            <!-- SCHEDA ALLENAMENTO -->
            <div class="block">
                {if $utente->getScheda()}
                    <a href="#" class="navigation-box-card">
                        <div>
                            <span class="tag is-light is-client-theme mb-2">SCHEDA ATTIVA</span>
                            <h3 class="title is-5 style-theme-text mb-1">{$utente->getScheda()->getNome()}</h3>
                            <p class="is-size-6 has-text-grey-dark">{$utente->getScheda()->getDescrizione()|default:'Nessuna descrizione'}</p>
                        </div>
                        <span class="icon has-text-grey"><i class="fas fa-chevron-right fa-lg"></i></span>
                    </a>
                {else}
                    <div class="box has-text-centered py-4">
                        <span class="icon is-medium has-text-grey-light mb-2"><i class="fas fa-dumbbell fa-2x"></i></span>
                        <p class="is-size-6 has-text-grey">Nessuna scheda di allenamento assegnata.</p>
                    </div>
                {/if}
            </div>

            <!-- CORSI PROGRAMMATI PER OGGI -->
            <div class="block mt-5">
                <h3 class="title is-5 style-theme-text mb-3">Corsi programmati per oggi</h3>

                {foreach $utente->getAttivitaPianificate() as $corso}
                    <a href="#" class="navigation-box-card">
                        <div>
                            <div class="is-flex is-align-items-center mb-2">
                                <span class="tag is-success is-light mr-2">{$corso->getOrario()}:00</span>
                                <span class="course-info-label">Sala: <strong>{$corso->getSala()->getNome()}</strong></span>
                            </div>
                            <h4 class="title is-5 style-theme-text mb-1">{$corso->getAttivita()->getNome()}</h4>
                            <p class="is-size-7 has-text-grey">Allenatore: {$corso->getAllenatore()->getNome()} {$corso->getAllenatore()->getCognome()}</p>
                        </div>
                        <span class="icon has-text-grey"><i class="fas fa-chevron-right fa-lg"></i></span>
                    </a>
                {foreachelse}
                    <div class="box has-text-centered py-5">
                        <span class="icon is-medium has-text-grey-light mb-2"><i class="fas fa-calendar-times fa-2x"></i></span>
                        <p class="is-size-6 has-text-grey">Nessun corso programmato per oggi.</p>
                    </div>
                {/foreach}
            </div>

        </div>
    </section>

</body>
</html>
