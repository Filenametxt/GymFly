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

<!-- STATO SIDEBAR IN PURO CSS (SENZA JAVASCRIPT) -->
<input type="checkbox" id="sidebar-toggle-checkbox" class="sidebar-checkbox-state" style="display: none;">

<!-- LABEL HAMBURGER COLLEGATO AL CHECKBOX -->
<label for="sidebar-toggle-checkbox" class="sidebar-toggle-flat" title="Mostra/Nascondi Menu">
    <i class="fas fa-bars"></i>
</label>

<aside class="app-sidebar">
    <!-- LOGO / BRANDING -->
    <div class="has-text-centered mb-6 mt-6">
        <strong class="is-size-3" style="color: #AFAFE2;">GymFly 🏋️‍♂️</strong>
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
            <span>Calendario Corsi</span>
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
            <a href="report" class="sidebar-menu-link">
                <i class="fas fa-chart-pie"></i>
                <span>Report & Analisi</span>
            </a>
        {/if}

    </div>

    <!-- PIEDE DELLA SIDEBAR (LOGOUT) -->
    <div class="mt-auto">
        <a href="logout" class="button is-danger is-light is-fullwidth">
            <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
            <span>Log Out</span>
        </a>
    </div>
</aside>
