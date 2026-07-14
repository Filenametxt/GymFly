{*
    =============================================================================
    GymFly - Componente Sidebar Riutilizzabile
    =============================================================================
    NOTE PER IL TEAMMATE:
    1. Includi questo file all'inizio del body delle dashboard e pagine principali:
       {include file='sidebar.tpl'}
    
    2. Avvolgi il contenuto della pagina all'interno del layout a due colonne:
       <div class="app-container">
           {include file='sidebar.tpl'}
           <main class="app-content">
               <!-- Qui va il contenuto specifico della pagina (dashboard, liste, ecc.) -->
           </main>
       </div>
    
    3. Il menu in basso mostra/nasconde le funzionalità a seconda del ruolo in sessione.
    =============================================================================
*}

<!-- STYLE OVERRIDE PER IL BUG DEI TESTI BIANCHI NEI TAG STRONG -->
<style>
    /* Risolve il bug globale di style.css che forza a bianco tutti i tag strong */
    strong {
        color: inherit !important;
    }
</style>

<!-- STATO SIDEBAR IN PURO CSS (SENZA JAVASCRIPT) -->
<input type="checkbox" id="sidebar-toggle-checkbox" class="sidebar-checkbox-state" style="display: none;">

<!-- LABEL HAMBURGER COLLEGATO AL CHECKBOX -->
<label for="sidebar-toggle-checkbox" class="sidebar-toggle-flat" title="Mostra/Nascondi Menu">
    <i class="fas fa-bars"></i>
</label>

<aside class="app-sidebar">
    <!-- LOGO / BRANDING -->
    <div class="has-text-centered mb-6 mt-6">
        <strong class="is-size-3" style="color: var(--gymfly-text) !important;">GymFly 🏋️‍♂️</strong>
    </div>

    <!-- LINK DI NAVIGAZIONE -->
    <div class="is-flex-grow-1">
        
        <!-- Voci comuni per tutti i ruoli -->
        {if isset($smarty.session.ruolo_utente) && $smarty.session.ruolo_utente === 'amministratore'}
            <a href="dashboard-admin" class="sidebar-menu-link">
        {else}
            <a href="dashboard-{$smarty.session.ruolo_utente|default:'cliente'}" class="sidebar-menu-link">
        {/if}
            <i class="fas fa-home"></i>
            <span>Home Dashboard</span>
        </a>

        <a href="profilo" class="sidebar-menu-link">
            <i class="fas fa-user-circle"></i>
            <span>Il mio Profilo</span>
        </a>

        <a href="messaggi" class="sidebar-menu-link">
            <i class="fas fa-envelope"></i>
            <span>Bacheca Messaggi</span>
        </a>

        <a href="calendario" class="sidebar-menu-link">
            <i class="fas fa-calendar-alt"></i>
            <span>
                {if isset($smarty.session.ruolo_utente) && $smarty.session.ruolo_utente === 'amministratore'}
                    Weekly Planner
                {else}
                    Calendario Attività
                {/if}
            </span>
        </a>

        <!-- Voci destinate solo ad Allenatori e Amministratori -->
        {if isset($smarty.session.ruolo_utente) && ($smarty.session.ruolo_utente === 'amministratore' || $smarty.session.ruolo_utente === 'allenatore')}
            <hr style="background-color: var(--gymfly-primary); height: 1px; margin: 1rem 0;">
            
            <a href="clienti" class="sidebar-menu-link">
                <i class="fas fa-users"></i>
                <span>Gestione Clienti</span>
            </a>
        {/if}

        <!-- Voci destinate solo all'Allenatore -->
        {if isset($smarty.session.ruolo_utente) && $smarty.session.ruolo_utente === 'allenatore'}
            <a href="crea-esercizio" class="sidebar-menu-link">
                <i class="fas fa-dumbbell"></i>
                <span>Aggiungi Esercizio</span>
            </a>
        {/if}

        <!-- Voci destinate esclusivamente all'Amministratore -->
        {if isset($smarty.session.ruolo_utente) && $smarty.session.ruolo_utente === 'amministratore'}
            <a href="allenatori" class="sidebar-menu-link">
                <i class="fas fa-user-ninja"></i>
                <span>Supervisione Allenatori</span>
            </a>
            <a href="crea-attivita" class="sidebar-menu-link">
                <i class="fas fa-dumbbell"></i>
                <span>Crea Attività</span>
            </a>
            <a href="report" class="sidebar-menu-link">
                <i class="fas fa-chart-pie"></i>
                <span>Report & Analisi</span>
            </a>
        {/if}

        <!-- Voci destinate solo al Cliente -->
        {if isset($smarty.session.ruolo_utente) && $smarty.session.ruolo_utente === 'cliente'}
            <hr style="background-color: var(--gymfly-primary); height: 1px; margin: 1rem 0;">
            
            <a href="visualizza-scheda" class="sidebar-menu-link">
                <i class="fas fa-dumbbell"></i>
                <span>La mia Scheda</span>
            </a>
            <a href="richiedi-scheda" class="sidebar-menu-link">
                <i class="fas fa-paper-plane"></i>
                <span>Richiedi Scheda</span>
            </a>
        {/if}

    </div>

    <!-- PIEDE DELLA SIDEBAR FISSO (LOGOUT) -->
    <div style="position: sticky; bottom: -2rem; background-color: var(--gymfly-card-bg) !important; padding: 1rem 1.5rem 2rem 1.5rem; margin-top: auto; margin-left: -1.5rem; margin-right: -1.5rem; margin-bottom: -2rem; border-top: 2px solid var(--gymfly-primary); z-index: 10;">
        <a href="logout" class="button is-danger is-light is-fullwidth" style="border-radius: 8px; font-weight: 600;">
            <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
            <span>Log Out</span>
        </a>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Risolve il link "Vedi Esercizi" con href="#" per evitare di modificare dashboard_cliente.tpl
    const btns = document.querySelectorAll('a.button');
    btns.forEach(btn => {
        if (btn.textContent.includes('Vedi Esercizi')) {
            btn.setAttribute('href', 'visualizza-scheda');
        }
    });

    // Uniforma l'altezza e la centratura di tutte le card dei titoli (solo su schermi non-mobile)
    if (window.innerWidth >= 769) {
        const headers = document.querySelectorAll('[class*="dashboard-header"]');
        headers.forEach(h => {
            h.style.setProperty('height', '180px', 'important');
            h.style.setProperty('display', 'flex', 'important');
            h.style.setProperty('align-items', 'center', 'important');
            h.style.setProperty('padding', '0 2.5rem', 'important');
            
            const cols = h.querySelector('.columns');
            if (cols) {
                cols.style.setProperty('width', '100%', 'important');
            }
        });
    }

    // Rileva la pagina corrente per evidenziare il pulsante attivo nella sidebar
    const currentPath = window.location.pathname;
    const links = document.querySelectorAll('.app-sidebar .sidebar-menu-link');

    // Determina se stiamo visualizzando il profilo dell'utente loggato o di un altro utente (assegnato da ProfiloController)
    const isSelfProfile = {if isset($isSelf) && !$isSelf}false{else}true{/if};
    const isClientProfile = {if isset($isClient) && $isClient}true{else}false{/if};
    const isTrainerProfile = {if isset($isTrainer) && $isTrainer}true{else}false{/if};

    // Determina il ruolo dell'utente a partire dall'href del primo link (Home Dashboard)
    let userRole = '';
    const homeLink = document.querySelector('.app-sidebar .sidebar-menu-link');
    if (homeLink) {
        const homeHref = homeLink.getAttribute('href');
        if (homeHref === 'dashboard-admin') {
            userRole = 'amministratore';
        } else if (homeHref === 'dashboard-allenatore') {
            userRole = 'allenatore';
        } else if (homeHref === 'dashboard-cliente') {
            userRole = 'cliente';
        }
    }

    // Aggiungi event listener a tutti i link della sidebar per salvare la selezione in sessionStorage
    links.forEach(link => {
        link.addEventListener('click', () => {
            const href = link.getAttribute('href');
            if (href) {
                sessionStorage.setItem('activeSidebarHref', href);
            }
        });
    });

    // Funzione per determinare l'elemento attivo basandosi sull'URL corrente
    function getActiveLinkByUrl() {
        let activeLink = null;
        
        links.forEach(link => {
            if (activeLink) return;
            const href = link.getAttribute('href');
            if (!href) return;

            // Se stiamo guardando il profilo di qualcun altro, evitiamo il match esatto con la voce "profilo" (il mio profilo)
            if (href === 'profilo' && !isSelfProfile) {
                // Procedi con il controllo dei sottomenu in basso, non fare il match esatto
            } else if (currentPath.endsWith('/' + href) || currentPath === href) {
                activeLink = link;
                return;
            }

            // Gestione robusta dei sottomenu e delle pagine collegate (fallback)
            if (href === 'profilo' && isSelfProfile && (
                currentPath.includes('modifica-anagrafica') || 
                currentPath.includes('aggiorna-misure') || 
                currentPath.includes('inserisci-misure') || 
                currentPath.includes('carica-certificato') || 
                currentPath.includes('cambia-password') || 
                currentPath.includes('visualizza-grafico') ||
                currentPath.includes('carica-foto') ||
                (currentPath.includes('visualizza-profilo') && userRole === 'cliente') ||
                (currentPath.includes('progressi-cliente') && userRole === 'cliente')
            )) {
                activeLink = link;
            }
            if (href === 'visualizza-scheda' && (
                currentPath.includes('modifica-dettagli') || 
                currentPath.includes('modifica-scheda') || 
                currentPath.includes('crea-scheda') ||
                currentPath.includes('salva-scheda') ||
                currentPath.includes('invia-scheda')
            )) {
                activeLink = link;
            }
            if (href === 'clienti' && (
                currentPath.includes('crea-cliente') || 
                currentPath.includes('gestione-abbonamento') ||
                currentPath.includes('abbonamento') ||
                (!isSelfProfile && isClientProfile) ||
                (currentPath.includes('visualizza-profilo') && (userRole === 'allenatore' || userRole === 'amministratore')) ||
                (currentPath.includes('progressi-cliente') && (userRole === 'allenatore' || userRole === 'amministratore'))
            )) {
                activeLink = link;
            }
            if (href === 'allenatori' && (
                currentPath.includes('crea-allenatore') ||
                currentPath.includes('abilita-attivita-allenatore') ||
                currentPath.includes('rimuovi-allenatore') ||
                (!isSelfProfile && isTrainerProfile)
            )) {
                activeLink = link;
            }
            if (href === 'messaggi' && (
                currentPath.includes('invia-messaggio')
            )) {
                activeLink = link;
            }
            if (href === 'calendario' && (
                currentPath.includes('prenota-attivita') ||
                currentPath.includes('disdici-prenotazione') ||
                currentPath.includes('prenota-sessione-privata') ||
                currentPath.includes('crea-attivita-pianificata') ||
                currentPath.includes('rimuovi-attivita-pianificata') ||
                currentPath.includes('disdici-sessione-privata')
            )) {
                activeLink = link;
            }
        });

        return activeLink;
    }

    // 1. Cerchiamo prima se l'URL corrente definisce un link attivo (esatto o sotto-pagina)
    let activeLinkElement = getActiveLinkByUrl();

    // 2. Se l'URL non corrisponde a nulla di noto, usiamo l'ultimo stato salvato in sessionStorage
    if (!activeLinkElement) {
        const storedHref = sessionStorage.getItem('activeSidebarHref');
        if (storedHref) {
            activeLinkElement = Array.from(links).find(link => link.getAttribute('href') === storedHref);
        }
    }

    // 3. Applica lo stato attivo e memorizza in sessionStorage
    if (activeLinkElement) {
        activeLinkElement.classList.add('is-active');
        const href = activeLinkElement.getAttribute('href');
        if (href) {
            sessionStorage.setItem('activeSidebarHref', href);
        }
    }
});
</script>
