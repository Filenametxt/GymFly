<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Profilo Cliente</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=1.2">
    {literal}
    <style>
        .custom-mobile-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .profile-avatar-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--gymfly-primary);
            margin: 0 auto 0.5rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--gymfly-bg);
        }
        .profile-avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-details-text p {
            margin-bottom: 0.4rem;
            font-size: 0.95rem;
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
            margin-bottom: 1rem;
        }
        .navigation-box-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
    </style>
    {/literal}
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="container">
            <div class="navbar-brand is-flex is-justify-content-between is-align-items-center w-100 px-3">
                
                <!-- Menu a Panino (Hamburger) -->
                <a role="button" class="navbar-burger ml-0" aria-label="menu" aria-expanded="false" data-target="profile-navbar-menu" onclick="document.getElementById('profile-navbar-menu').classList.toggle('is-active'); this.classList.toggle('is-active');">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>

                <!-- Titolo Centrato -->
                <div class="navbar-item py-0">
                    <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">PROFILO</strong>
                </div>

                <!-- Spaziatore a destra per mantenere centrato il titolo -->
                <div style="width: 32px;"></div>

            </div>

            <!-- Menu che si espande sotto al click del panino -->
            <div id="profile-navbar-menu" class="navbar-menu">
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
            
            <!-- DETTAGLI PROFILO (Dati a sinistra, Foto a destra) -->
            <div class="box p-4 mb-4">
                <div class="columns is-mobile is-vcentered">
                    
                    <!-- Dati Personali -->
                    <div class="column is-7 profile-details-text">
                        <p><strong>Nome:</strong> {$utente->getNome()}</p>
                        <p><strong>Cognome:</strong> {$utente->getCognome()}</p>
                        <p><strong>Sesso:</strong> {$utente->getSesso()->value}</p>
                        {if $isClient}
                            <p><strong>Nascita:</strong> {$utente->getDataDiNascita()|date_format:"%d/%m/%Y"}</p>
                        {/if}
                        <p><strong>e-mail:</strong> <span class="is-size-7-mobile">{$utente->getEmail()}</span></p>
                        <p><strong>Telefono:</strong> {$utente->getTelefono()|default:'-'}</p>
                    </div>

                    <!-- Foto Profilo -->
                    <div class="column is-5 has-text-centered">
                        <div class="profile-avatar-circle">
                            {if $fotoProfilo}
                                <img src="data:image/jpeg;base64,{$fotoProfilo}" alt="Foto Profilo">
                            {else}
                                <i class="fas fa-user-circle fa-4x" style="color: var(--gymfly-primary);"></i>
                            {/if}
                        </div>

                        <!-- Form rapido caricamento foto profilo -->
                        <form action="carica-foto" method="POST" enctype="multipart/form-data">
                            <div class="file is-small is-centered mt-2">
                                <label class="file-label">
                                    <input class="file-input" type="file" name="foto_profilo" accept="image/*" onchange="this.form.submit()">
                                    <span class="file-cta" style="background-color: var(--gymfly-bg); border-color: var(--gymfly-primary);">
                                        <span class="file-icon"><i class="fas fa-camera"></i></span>
                                        <span class="file-label">Cambia</span>
                                    </span>
                                </label>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <!-- INFO ABBONAMENTO -->
            {if $isClient}
            <div class="box p-4 mb-4">
                <h3 class="title is-5 style-theme-text mb-3">
                    Abbonamento 
                    {if $abbonamento && !$abbonamento->isScaduto()}
                        <span class="has-text-success">attivo</span>
                    {else}
                        <span class="has-text-danger">scaduto</span>
                    {/if}
                </h3>
                
                {if $abbonamento}
                    <p class="is-size-6 mb-1">Data inizio: <strong>{$abbonamento->getDataInizio()|date_format:"%d/%m/%Y"}</strong></p>
                    <p class="is-size-6 mb-3">Data fine: <strong>{$abbonamento->getDataFine()|date_format:"%d/%m/%Y"}</strong></p>
                {else}
                    <p class="is-size-6 mb-3 has-text-grey">Nessun abbonamento attivo o sottoscritto.</p>
                {/if}

                <div class="is-divider my-3" style="border-top: 1px solid var(--gymfly-primary);"></div>

                <p class="is-size-6 has-text-weight-bold is-uppercase style-theme-text">
                    SCADENZA ISCRIZIONE: {if $utente->getScadenzaIscrizione()}{$utente->getScadenzaIscrizione()|date_format:"%d/%m/%Y"}{else}Non registrato{/if}
                </p>

                {if $smarty.session.ruolo_utente === 'amministratore'}
                    <div class="mt-3">
                        <a href="gestione-abbonamento?id={$utente->getId()}" class="button is-small is-gymfly is-fullwidth">
                            <span class="icon"><i class="fas fa-edit"></i></span>
                            <span>Gestisci Abbonamento & Iscrizione</span>
                        </a>
                    </div>
                {/if}
            </div>
            {/if}

            <!-- INFO SCHEDA ALLENAMENTO (Visibile a Coach e Admin) -->
            {if $smarty.session.ruolo_utente === 'allenatore' || $smarty.session.ruolo_utente === 'amministratore'}
            <div class="box p-4 mb-4">
                <h3 class="title is-5 style-theme-text mb-3">
                    <i class="fas fa-dumbbell mr-2"></i>Scheda Allenamento
                </h3>
                {if $utente->getScheda()}
                    <p class="is-size-6 mb-1">Nome Scheda: <strong>{$utente->getScheda()->getNome_scheda()}</strong></p>
                    <p class="is-size-6 mb-3">Obiettivo: <strong>{$utente->getScheda()->getObiettivo()}</strong></p>
                    <div class="buttons">
                        <a href="modifica-scheda?id={$utente->getScheda()->getId()}" class="button is-small is-gymfly is-fullwidth mb-2">
                            <span class="icon"><i class="fas fa-edit"></i></span>
                            <span>Gestisci / Modifica Scheda</span>
                        </a>
                    </div>
                {else}
                    <p class="is-size-6 mb-3 has-text-grey">Nessuna scheda attiva per questo utente.</p>
                    <div class="buttons">
                        <a href="crea-scheda?cf={$utente->getCF()}" class="button is-small is-success is-fullwidth">
                            <span class="icon"><i class="fas fa-plus"></i></span>
                            <span>Crea Nuova Scheda</span>
                        </a>
                    </div>
                {/if}
            </div>
            {/if}

            <!-- AZIONI / SHORTCUTS -->
            <div class="block mt-4">
                
                {if $isClient}
                <!-- Parametri -->
                <a href="aggiorna-misure" class="navigation-box-card">
                    <span class="is-flex is-align-items-center">
                        <span class="icon mr-3 has-text-link"><i class="fas fa-chart-line fa-lg"></i></span>
                        <span class="has-text-weight-semibold is-size-5">parametri</span>
                    </span>
                    <span class="icon has-text-grey"><i class="fas fa-chevron-right fa-lg"></i></span>
                </a>

                <!-- Certificato Medico -->
                <a href="carica-certificato" class="navigation-box-card">
                    <span class="is-flex is-align-items-center">
                        {if $utente->isCertificatoValido()}
                            <span class="icon mr-3 has-text-success"><i class="fas fa-check-circle fa-lg"></i></span>
                            <div>
                                <span class="has-text-weight-semibold is-size-5">Certificato medico</span>
                                <p class="is-size-7 has-text-grey">Scade il {$utente->getCertificatoMedico()->getDataScadenza()|date_format:"%d/%m/%Y"}</p>
                            </div>
                        {else}
                            <span class="icon mr-3 has-text-danger"><i class="fas fa-file-medical fa-lg"></i></span>
                            <div>
                                <span class="has-text-weight-semibold is-size-5">Certificato medico</span>
                                <p class="is-size-7 has-text-danger">Mancante o Scaduto</p>
                            </div>
                        {/if}
                    </span>
                    <span class="icon has-text-grey"><i class="fas fa-chevron-right fa-lg"></i></span>
                </a>
                {/if}

                <!-- Modifica Dati -->
                <a href="modifica-anagrafica" class="navigation-box-card">
                    <span class="is-flex is-align-items-center">
                        <span class="icon mr-3 has-text-link"><i class="fas fa-pen fa-lg"></i></span>
                        <span class="has-text-weight-semibold is-size-5">modifica dati</span>
                    </span>
                    <span class="icon has-text-grey"><i class="fas fa-chevron-right fa-lg"></i></span>
                </a>

                <!-- Cambia Password (se stesso utente) -->
                {if $smarty.session.id_utente === $utente->getId()}
                    <a href="cambia-password" class="navigation-box-card">
                        <span class="is-flex is-align-items-center">
                            <span class="icon mr-3 has-text-link"><i class="fas fa-key fa-lg"></i></span>
                            <span class="has-text-weight-semibold is-size-5">cambia password</span>
                        </span>
                        <span class="icon has-text-grey"><i class="fas fa-chevron-right fa-lg"></i></span>
                    </a>
                {/if}

            </div>

        </div>
    </section>

</body>
</html>