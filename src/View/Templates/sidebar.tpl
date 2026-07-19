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

<aside class="app-sidebar" 
       data-is-self="{if isset($isSelf) && !$isSelf}false{else}true{/if}"
       data-is-client="{if isset($isClient) && $isClient}true{else}false{/if}"
       data-is-trainer="{if isset($isTrainer) && $isTrainer}true{else}false{/if}">
    <!-- LOGO / BRANDING -->
    <div class="mb-5" style="display: flex; justify-content: flex-start; align-items: center; width: 100%; margin-top: -2.0rem !important; margin-left: 2.2rem !important; gap: 6px; padding: 0.5rem 0;">
        <strong class="is-size-2" style="color: var(--gymfly-text) !important; font-weight: 800;">GymFly</strong>
        <div class="gf-logo-icon" style="width: 54px; height: 54px; border-width: 2px;"></div>
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
            <a href="esercizi" class="sidebar-menu-link">
                <i class="fas fa-dumbbell"></i>
                <span>Gestione Esercizi</span>
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

<script src="js/sidebar.js"></script>
