<nav class="navbar is-light" role="navigation" aria-label="main navigation">
  <div class="container">
    <div class="navbar-brand">
      <a class="navbar-item" href="/">
        <strong>GymFly 🏋️‍♂️</strong>
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
            <p class="is-size-6"><strong>Sesso:</strong> {$utente->getSesso()}</p>
            <p class="is-size-6"><strong>Data di nascita:</strong> {$utente->getDataNascita()|date_format:"%d/%m/%Y"}</p>
          </div>
          <div class="block">
            <p class="is-size-6"><strong>e-mail:</strong> {$utente->getEmail()}</p>
            <p class="is-size-6"><strong>telefono:</strong> {$utente->getTelefono()}</p>
          </div>
        </div>
        
        <div class="column is-three-tenths-mobile is-one-quarter-tablet has-text-centered">
          <figure class="image is-64x64 is-inline-block-mobile is-128x128-tablet">
            <span class="icon is-large">
              <i class="fas fa-user-circle fa-3x"></i>
            </span>
          </figure>
        </div>

      </div>
    </div>

    <div class="box has-text-centered" style="border: 2px solid #000;">
      <h3 class="title is-5 mb-3">
        Abbonamento 
        {if $abbonamento->isAttivo()}
          <span class="has-text-success">attivo</span>
        {else}
          <span class="has-text-danger">scaduto</span>
        {if_end}
      </h3>
      
      <p class="is-size-6">data inizio: <strong>{$abbonamento->getDataInizio()|date_format:"%d/%m/%Y"}</strong></p>
      <p class="is-size-6 mb-3">data fine: <strong>{$abbonamento->getDataFine()|date_format:"%d/%m/%Y"}</strong></p>
      
      <p class="is-size-6 has-text-weight-bold is-uppercase">
        SCADENZA ISCRIZIONE: {$utente->getScadenzaIscrizione()|date_format:"%d/%m/%Y"}
      </p>
    </div>

    <div class="block mt-5">
      
      <a href="parametri.php" class="box py-3 px-4 mb-2 is-flex is-justify-content-between is-align-items-center" style="border: 1px solid #000;">
        <span class="is-flex is-align-items-center">
          <span class="icon mr-3"><i class="fas fa-chart-line fa-lg"></i></span>
          <span class="has-text-weight-semibold is-size-5">parametri</span>
        </span>
        <span class="icon"><i class="fas fa-chevron-right"></i></span>
      </a>

      <a href="certificato.php" class="box py-3 px-4 mb-2 is-flex is-justify-content-between is-align-items-center" style="border: 1px solid #000;">
        <span class="is-flex is-align-items-center">
          <span class="icon mr-3 has-text-danger"><i class="fas fa-file-medical fa-lg"></i></span>
          <span class="has-text-weight-semibold is-size-5">Certificato medico</span>
        </span>
        <span class="icon"><i class="fas fa-chevron-right"></i></span>
      </a>

      <a href="modifica.php" class="box py-3 px-4 mb-2 is-flex is-justify-content-between is-align-items-center" style="border: 1px solid #000;">
        <span class="is-flex is-align-items-center">
          <span class="icon mr-3"><i class="fas fa-pen fa-lg"></i></span>
          <span class="has-text-weight-semibold is-size-5">modifica dati</span>
        </span>
        <span class="icon"><i class="fas fa-chevron-right"></i></span>
      </a>

    </div>

  </div>
</section>