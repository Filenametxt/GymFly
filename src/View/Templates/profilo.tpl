<!DOCTYPE html>
<html lang="it">
<head>
    <meta name="color-scheme" content="light">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Profilo Cliente</title>
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
                            {if $isSelf}
                                Il mio Profilo
                            {elseif $isClient}
                                Profilo Cliente
                            {else}
                                Profilo Allenatore
                            {/if}
                        </h1>
                        <p class="subtitle is-5 has-text-white-ter">
                            {if $isSelf}
                                Visualizza e gestisci le tue informazioni personali
                            {else}
                                Visualizza e gestisci le informazioni dell'utente
                            {/if}
                        </p>
                    </div>
                    <div class="column is-narrow">
                        <figure class="image is-96x96">
                            <span class="icon is-large has-text-white">
                                <i class="fas fa-user-circle fa-5x"></i>
                            </span>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- ================= MOBILE HEADER ================= -->
            <div class="is-flex is-align-items-center mb-5 is-hidden-tablet" style="padding-top: 5px;">
                <div style="width: 45px;"></div>
                <strong class="is-size-4 style-theme-text" style="letter-spacing: 1px;">PROFILO</strong>
            </div>

            <!-- ================= CONTENT ================= -->
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
                            {if $isSelf}
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
                            {/if}
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

                <!-- ATTIVITÀ ABILITATE (Visibile solo per gli Allenatori) -->
                {if isset($isTrainer) && $isTrainer}
                <div class="box p-4 mb-4">
                    <h3 class="title is-5 style-theme-text mb-3">
                        <i class="fas fa-certificate mr-2"></i>Attività Abilitate
                    </h3>

                    {if $isSelf || $smarty.session.ruolo_utente === 'amministratore'}
                        <!-- Form di gestione con Checkbox per abilitare/disabilitare più attività contemporaneamente -->
                        <form action="aggiorna-abilitazioni-profilo" method="POST">
                            <input type="hidden" name="id_allenatore" value="{$utente->getId()}">
                            
                            {if $tutteAttivita && count($tutteAttivita) > 0}
                                <div class="field mb-4">
                                    <label class="label is-size-7 has-text-grey-dark mb-3">Seleziona le attività per cui sei abilitato:</label>
                                    <div class="control is-flex is-flex-wrap-wrap" style="gap: 0.75rem;">
                                        {foreach from=$tutteAttivita item=att}
                                            {assign var="isAbilitato" value=false}
                                            {if $attivitaAbilitate}
                                                {foreach from=$attivitaAbilitate item=aa}
                                                    {if $aa->getId() === $att->getId()}
                                                        {assign var="isAbilitato" value=true}
                                                    {/if}
                                                {/foreach}
                                            {/if}
                                            <label class="checkbox is-size-6" style="background: var(--gymfly-bg); padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid var(--gymfly-accent); display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; user-select: none;">
                                                <input type="checkbox" name="attivita[]" value="{$att->getId()}" {if $isAbilitato}checked{/if}>
                                                <span style="font-weight: 500;">{$att->getNome()}</span>
                                            </label>
                                        {/foreach}
                                    </div>
                                </div>
                                <button type="submit" class="button is-gymfly is-small mt-2" style="border-radius: 8px;">
                                    <span class="icon"><i class="fas fa-save"></i></span>
                                    <span>Salva Abilitazioni</span>
                                </button>
                            {else}
                                <p class="is-size-6 mb-0 has-text-grey">Nessuna attività presente nel catalogo della palestra.</p>
                            {/if}
                        </form>
                    {else}
                        <!-- Visualizzazione in sola lettura (per altri utenti) -->
                        {if $attivitaAbilitate && count($attivitaAbilitate) > 0}
                            <div class="tags">
                                {foreach from=$attivitaAbilitate item=att}
                                    <span class="tag is-light style-theme-text is-rounded" style="font-weight: 500; font-size: 0.95rem;">{$att->getNome()}</span>
                                {/foreach}
                            </div>
                        {else}
                            <p class="is-size-6 mb-0 has-text-grey">Nessuna attività abilitata per questo allenatore.</p>
                        {/if}
                    {/if}
                </div>
                {/if}

                <!-- INFO SCHEDA ALLENAMENTO (Visibile a Coach se il profilo è di un cliente) -->
                {if ($smarty.session.ruolo_utente === 'allenatore') && $isClient}
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
                    {if $smarty.session.ruolo_utente !== 'amministratore'}
                    <a href="aggiorna-misure{if !$isSelf}?id={$utente->getId()}{/if}" class="navigation-box-card">
                        <span class="is-flex is-align-items-center">
                            <span class="icon mr-3 has-text-link"><i class="fas fa-chart-line fa-lg"></i></span>
                            <span class="has-text-weight-semibold is-size-5">parametri</span>
                        </span>
                        <span class="icon has-text-grey"><i class="fas fa-chevron-right fa-lg"></i></span>
                    </a>
                    {/if}

                    <!-- Certificato Medico -->
                    {if $smarty.session.ruolo_utente !== 'allenatore'}
                    <a href="carica-certificato{if !$isSelf}?id={$utente->getId()}{/if}" class="navigation-box-card">
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
                    {/if}

                    <!-- Modifica Dati -->
                    {if !($smarty.session.ruolo_utente === 'allenatore' && !$isSelf)}
                    <a href="modifica-anagrafica{if !$isSelf}?id={$utente->getId()}{/if}" class="navigation-box-card">
                        <span class="is-flex is-align-items-center">
                            <span class="icon mr-3 has-text-link"><i class="fas fa-pen fa-lg"></i></span>
                            <span class="has-text-weight-semibold is-size-5">modifica dati</span>
                        </span>
                        <span class="icon has-text-grey"><i class="fas fa-chevron-right fa-lg"></i></span>
                    </a>
                    {/if}

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
        </main>
    </div>


</body>
</html>