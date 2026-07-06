<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFly - Profilo Cliente</title>
    <link rel="stylesheet" href="css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar" role="navigation" aria-label="main navigation">
  <div class="container">
    <div class="navbar-brand">
      <a class="navbar-item" href="dashboard-cliente">
        <strong class="is-size-4" style="color: #AFAFE2;">GymFly 🏋️‍♂️</strong>
      </a>

      <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarProfile">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>
    </div>

    <div id="navbarProfile" class="navbar-menu">
      <div class="navbar-start">
        <div class="navbar-item">
          <h1 class="title is-4 has-text-weight-bold" style="color: #b5b500;">PROFILO</h1>
        </div>
      </div>
    </div>
  </div>
</nav>

<section class="section">
  <div class="container">
    
    <div class="box">
      <div class="columns is-mobile is-multiline">
        
        <div class="column is-seven-tenths-mobile is-three-quarters-tablet">
          <div class="block">
            <p class="is-size-5"><strong>Nome:</strong> {$utente->getNome()}</p>
            <p class="is-size-5"><strong>Cognome:</strong> {$utente->getCognome()}</p>
          </div>
          <div class="block">
            <p class="is-size-6"><strong>Sesso:</strong> {$utente->getSesso()->value}</p>
            <p class="is-size-6"><strong>Data di nascita:</strong> {$utente->getDataDiNascita()|date_format:"%d/%m/%Y"}</p>
          </div>
          <div class="block">
            <p class="is-size-6"><strong>e-mail:</strong> {$utente->getEmail()}</p>
            <p class="is-size-6"><strong>telefono:</strong> {$utente->getTelefono()}</p>
          </div>
        </div>
        
        <div class="column is-three-tenths-mobile is-one-quarter-tablet has-text-centered">
          <figure class="image is-64x64 is-inline-block-mobile is-128x128-tablet mb-2" style="margin: 0 auto;">
            {if $fotoProfilo}
              <img class="is-rounded" src="data:image/jpeg;base64,{$fotoProfilo}" alt="Foto Profilo" style="max-height: 128px; width: 128px; object-fit: cover; border: 2px solid #b5b500;">
            {else}
              <i class="fas fa-user-circle fa-5x" style="color: #b5b500;"></i>
            {/if}
          </figure>
          
          <!-- Form rapido caricamento foto profilo -->
          <form action="carica-foto" method="POST" enctype="multipart/form-data">
            <div class="file is-small is-centered mt-1">
              <label class="file-label">
                <input class="file-input" type="file" name="foto_profilo" accept="image/*" onchange="this.form.submit()">
                <span class="file-cta" style="background-color: #f5f5f5; border-color: #dbdbdb; border-radius: 4px;">
                  <span class="file-icon"><i class="fas fa-camera"></i></span>
                  <span class="file-label">Cambia</span>
                </span>
              </label>
            </div>
          </form>
        </div>

      </div>
    </div>

    <div class="box has-text-centered" style="border: 2px solid #000;">
      {if $abbonamento}
        <h3 class="title is-5 mb-3">
          Abbonamento 
          {if !$abbonamento->isScaduto()}
            <span class="has-text-success">attivo</span>
          {else}
            <span class="has-text-danger">scaduto</span>
          {/if}
        </h3>
        <p class="is-size-6">data inizio: <strong>{$abbonamento->getDataInizio()|date_format:"%d/%m/%Y"}</strong></p>
        <p class="is-size-6 mb-3">data fine: <strong>{$abbonamento->getDataFine()|date_format:"%d/%m/%Y"}</strong></p>
      {else}
        <h3 class="title is-5 mb-3">
          Abbonamento <span class="has-text-grey">non attivo</span>
        </h3>
        <p class="is-size-6 mb-3">Nessun abbonamento attivo o sottoscritto.</p>
      {/if}
      
      <p class="is-size-6 has-text-weight-bold is-uppercase">
        SCADENZA ISCRIZIONE: {if $utente->getScadenzaIscrizione()}{$utente->getScadenzaIscrizione()|date_format:"%d/%m/%Y"}{else}Non registrato / Scaduto{/if}
      </p>

      {if $smarty.session.ruolo_utente === 'amministratore'}
        <div class="mt-3">
          <a href="gestione-abbonamento?id={$utente->getId()}" class="button is-small is-gymfly">
            <span class="icon"><i class="fas fa-edit"></i></span>
            <span>Gestisci Abbonamento & Iscrizione</span>
          </a>
        </div>
      {/if}
    </div>

    <div class="block mt-5">
      
      <a href="aggiorna-misure" class="box py-3 px-4 mb-2 is-flex is-justify-content-between is-align-items-center" style="border: 1px solid #000;">
        <span class="is-flex is-align-items-center">
          <span class="icon mr-3"><i class="fas fa-chart-line fa-lg"></i></span>
          <span class="has-text-weight-semibold is-size-5">parametri</span>
        </span>
        <span class="icon"><i class="fas fa-chevron-right"></i></span>
      </a>

      <a href="carica-certificato" class="box py-3 px-4 mb-2 is-flex is-justify-content-between is-align-items-center" style="border: 1px solid #000;">
        <span class="is-flex is-align-items-center">
          {if $utente->isCertificatoValido()}
            <span class="icon mr-3 has-text-success"><i class="fas fa-check-circle fa-lg"></i></span>
            <div>
              <span class="has-text-weight-semibold is-size-5">Sostituisci Certificato Medico</span>
              <p class="is-size-7 has-text-grey">Valido fino al {$utente->getCertificatoMedico()->getDataScadenza()|date_format:"%d/%m/%Y"}</p>
            </div>
          {elseif $utente->getCertificatoMedico()}
            <span class="icon mr-3 has-text-warning"><i class="fas fa-exclamation-triangle fa-lg"></i></span>
            <div>
              <span class="has-text-weight-semibold is-size-5">Sostituisci Certificato (Scaduto)</span>
              <p class="is-size-7 has-text-danger">Scaduto il {$utente->getCertificatoMedico()->getDataScadenza()|date_format:"%d/%m/%Y"}</p>
            </div>
          {else}
            <span class="icon mr-3 has-text-danger"><i class="fas fa-file-medical fa-lg"></i></span>
            <div>
              <span class="has-text-weight-semibold is-size-5">Carica Certificato Medico</span>
              <p class="is-size-7 has-text-grey-light">Nessun certificato caricato</p>
            </div>
          {/if}
        </span>
        <span class="icon"><i class="fas fa-chevron-right"></i></span>
      </a>

      <a href="modifica-anagrafica" class="box py-3 px-4 mb-2 is-flex is-justify-content-between is-align-items-center" style="border: 1px solid #000;">
        <span class="is-flex is-align-items-center">
          <span class="icon mr-3"><i class="fas fa-pen fa-lg"></i></span>
          <span class="has-text-weight-semibold is-size-5">modifica dati</span>
        </span>
        <span class="icon"><i class="fas fa-chevron-right"></i></span>
      </a>

      {if $smarty.session.id_utente === $utente->getId()}
        <a href="cambia-password" class="box py-3 px-4 mb-2 is-flex is-justify-content-between is-align-items-center" style="border: 1px solid #000;">
          <span class="is-flex is-align-items-center">
            <span class="icon mr-3"><i class="fas fa-key fa-lg"></i></span>
            <span class="has-text-weight-semibold is-size-5">cambia password</span>
          </span>
          <span class="icon"><i class="fas fa-chevron-right"></i></span>
        </a>
      {/if}
    </div>

    </div>

  </div>
</section>

</body>
</html>