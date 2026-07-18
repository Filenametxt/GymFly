document.addEventListener('DOMContentLoaded', () => {
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
    const sidebarEl = document.querySelector('.app-sidebar');
    const isSelfProfile = sidebarEl ? sidebarEl.getAttribute('data-is-self') !== 'false' : true;
    const isClientProfile = sidebarEl ? sidebarEl.getAttribute('data-is-client') === 'true' : false;
    const isTrainerProfile = sidebarEl ? sidebarEl.getAttribute('data-is-trainer') === 'true' : false;

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
                currentPath.includes('invia-scheda')
            )) {
                activeLink = link;
            }
            if (href === 'clienti' && (
                currentPath.includes('crea-cliente') || 
                currentPath.includes('gestione-abbonamento') ||
                currentPath.includes('abbonamento') ||
                currentPath.includes('rimuovi-cliente') ||
                (!isSelfProfile && isClientProfile) ||
                (currentPath.includes('visualizza-profilo') && !isTrainerProfile && (userRole === 'allenatore' || userRole === 'amministratore')) ||
                (currentPath.includes('progressi-cliente') && !isTrainerProfile && (userRole === 'allenatore' || userRole === 'amministratore'))
            )) {
                activeLink = link;
            }
            if (href === 'allenatori' && (
                currentPath.includes('crea-allenatore') ||
                currentPath.includes('rimuovi-allenatore') ||
                (!isSelfProfile && isTrainerProfile)
            )) {
                activeLink = link;
            }
            if (href === 'esercizi' && (
                currentPath.includes('crea-esercizio') ||
                currentPath.includes('valida-esercizio') ||
                currentPath.includes('salva-esercizio') ||
                currentPath.includes('copia-esercizio') ||
                currentPath.includes('elimina-bozza') ||
                currentPath.includes('visualizza-esercizio')
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
