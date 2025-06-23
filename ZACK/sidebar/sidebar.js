// Importa Vue dal global scope
const { createApp } = Vue

// Variabili globali essenziali
let currentPage = null;
let currentSearchTerm = '';
let globalNavigationVisible = false;
let risultatiGlobali = [];
let currentResultIndex = 0;
let app;
let navigating = false;
let navigationTimeout = null;

// UNICA funzione per creare il pannello di navigazione
function createSimpleNavigationPanel(results) {
  if (!results || results.length === 0) {
    console.error("Impossibile creare il pannello: nessun risultato disponibile");
    return;
  }
  
  console.log("Creazione pannello di navigazione per", results.length, "risultati");
  
  const existingPanel = document.getElementById('global-highlight-navigation');
  if (existingPanel) existingPanel.remove();
  
  const navPanel = document.createElement('div');
  navPanel.id = 'global-highlight-navigation';
  
  navPanel.style.cssText = `
    position: fixed; bottom: 20px; right: 20px; z-index: 9999;
    background-color: white; box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    border-radius: 8px; padding: 12px; display: flex; flex-direction: column;
    gap: 10px; font-family: Arial, sans-serif; width: 170px;
  `;
  
  const navButtons = document.createElement('div');
  navButtons.style.cssText = `
    display: flex; align-items: center; justify-content: space-between; width: 100%;
  `;
  
  const prevBtn = document.createElement('button');
  prevBtn.innerText = '<';
  prevBtn.style.cssText = `
    width: 30px; height: 30px; border: none; background-color: #f0f0f0;
    border-radius: 4px; cursor: pointer; font-weight: bold; flex-shrink: 0;
  `;
  
  const counter = document.createElement('span');
  counter.id = 'nav-counter';
  counter.innerText = `${currentResultIndex + 1} di ${results.length}`;
  counter.style.cssText = `min-width: 70px; text-align: center; flex-shrink: 0;`;
  
  const nextBtn = document.createElement('button');
  nextBtn.innerText = '>';
  nextBtn.style.cssText = prevBtn.style.cssText;
  
  const closeBtn = document.createElement('button');
  closeBtn.innerText = '✕';
  closeBtn.style.cssText = prevBtn.style.cssText + 'margin-left: auto;';
  
  const pageInfo = document.createElement('div');
  pageInfo.id = 'nav-page';
  pageInfo.innerText = results[currentResultIndex].pageName;
  pageInfo.style.cssText = `
    font-weight: bold; color: #0066cc; text-align: center; margin-top: 8px;
    padding: 0 5px; overflow: hidden; text-overflow: ellipsis; 
    white-space: nowrap; width: 100%;
  `;
  pageInfo.title = results[currentResultIndex].pageName;
  
  // Event handlers semplificati
  prevBtn.onclick = () => {
    if (navigating) return;
    navigating = true;
    currentResultIndex = (currentResultIndex - 1 + results.length) % results.length;
    counter.innerText = `${currentResultIndex + 1} di ${results.length}`;
    window.navigateToResult(currentResultIndex);
    setTimeout(() => { navigating = false; }, 300);
  };
  
  nextBtn.onclick = () => {
    if (navigating) return;
    navigating = true;
    currentResultIndex = (currentResultIndex + 1) % results.length;
    counter.innerText = `${currentResultIndex + 1} di ${results.length}`;
    window.navigateToResult(currentResultIndex);
    setTimeout(() => { navigating = false; }, 300);
  };
  
  closeBtn.onclick = () => {
    navPanel.remove();
    globalNavigationVisible = false;
  };
  
  navButtons.appendChild(prevBtn);
  navButtons.appendChild(counter);
  navButtons.appendChild(nextBtn);
  navButtons.appendChild(closeBtn);
  navPanel.appendChild(navButtons);
  navPanel.appendChild(pageInfo);
  document.body.appendChild(navPanel);
  
  globalNavigationVisible = true;
  console.log("Pannello di navigazione creato con successo");
  return navPanel;
}

// UNICA funzione per aggiornare il pannello
function updateNavigationPanel(result) {
  const counter = document.getElementById('nav-counter');
  const pageInfo = document.getElementById('nav-page');
  
  if (counter && pageInfo) {
    counter.innerText = `${currentResultIndex + 1} di ${risultatiGlobali.length}`;
    pageInfo.innerText = result.pageName;
  }
}

// Carica il JSON delle pagine prima di creare l'app Vue
fetch('sidebar/sidebar-pagine/sidebar-pagine.json')
  .then(response => response.json())
  .then(pagineData => {
    app = Vue.createApp({
      data() {
        return {
          pagine: pagineData,
          paginaAttiva: null,
          isSidebarNarrow: false,
          isSidebarExpanded: false,
          risultatiRicerca: [] // <--- AGGIUNGI QUESTO!
        }
      },
      computed: {
        paginaCorrenteFile() {
          if (this.paginaAttiva !== null && this.pagine[this.paginaAttiva]) {
            return this.pagine[this.paginaAttiva].file;
          }
          return '';
        }
      },
      methods: {
        vaiAllaPagina(idx) {
          this.paginaAttiva = idx;
        },
        corrispondeRicerca(indice) {
          return this.risultatiRicerca.includes(indice);
        },
        toggleSidebarWidth() {
          this.isSidebarNarrow = !this.isSidebarNarrow;
        }
      },
      mounted() {
        const appInstance = this;
        
        // UNICA funzione di navigazione globale
        window.navigateToResult = function(index) {
          if (!risultatiGlobali || risultatiGlobali.length === 0) return;
          
          const result = risultatiGlobali[index];
          
          // Semplifica la logica: se pageIndex corrisponde alla pagina corrente, evidenzia
          // altrimenti carica la nuova pagina
          if (currentPage !== result.pageIndex) {
            loadPageDirectly(result.pageIndex, result.pageName, result.pageFile);
          } else {
            // Evidenzia nella pagina corrente
            highlightInCurrentPage(result);
          }
          
          currentResultIndex = index;
          updateNavigationPanel(result);
        };
        
        // UNICO event listener per i messaggi
        window.addEventListener('message', (event) => {
          // Riduci il logging verboso
          if (event.data?.type !== 'searchResults') {
            console.log("Messaggio ricevuto in sidebar:", event.data?.type);
          }
          
          if (event.data && event.data.type === 'requestPages') {
            console.log("Richiesta pagine ricevuta, invio elenco pagine");
            const pagineSerializzabili = appInstance.pagine.map(p => ({
              nome: p.nome,
              file: p.file
            }));
            
            const iframe = document.querySelector('.iframe-ricerca');
            if (iframe && iframe.contentWindow) {
              iframe.contentWindow.postMessage({
                type: 'pages',
                pages: pagineSerializzabili
              }, '*');
            }
          }
          
          else if (event.data && event.data.type === 'searchResults') {
            // Rimuovi questi debug verbosi
            // console.log("DEBUG risultati ricevuti in sidebar:");
            // event.data.risultatiGlobali.slice(0, 3).forEach((r, i) => {
            //   console.log(`[${i}] pagina: "${r.pagina}", file: "${r.file}", isButtonMatch: ${r.isButtonMatch || false}`);
            // });

            currentSearchTerm = event.data.term;
            appInstance.risultatiRicerca = event.data.risultati || [];
            
            risultatiGlobali = (event.data.risultatiGlobali || []).map(r => ({
              pageIndex: r.pageIndex,
              pageName: r.pagina,
              pageFile: r.file,
              matchIndex: r.matchIndex,
              matchText: r.matchText,
              posizioneMatch: r.posizioneMatch,
              tempId: r.tempId,
              isButtonMatch: r.isButtonMatch || false
            }));
            
            // Mantieni solo log essenziale
            console.log(`Ricevuti: ${appInstance.risultatiRicerca.length} pagine, ${risultatiGlobali.length} occorrenze`);
            
            const includiContenuto = event.data.includiContenuto;
            console.log(`Ricevuti risultati di ricerca: ${appInstance.risultatiRicerca.length} pagine, ${risultatiGlobali.length} occorrenze globali`);
            
            const existingNav = document.getElementById('global-highlight-navigation');
            if (existingNav) existingNav.remove();
            
            appInstance.$nextTick(() => {
              
              // ✅ CORREZIONE: Non creare il pannello di navigazione se tutti i risultati sono solo bottoni e includiContenuto è false
              const hasSoloTitoli = risultatiGlobali.every(r => r.isButtonMatch === true);
              
              if (event.data.action === 'enter') {
                if (risultatiGlobali && risultatiGlobali.length > 0) {
                  // CREA SEMPRE il pannello di navigazione, anche solo per i titoli
                  console.log("Creazione controlli di navigazione per", risultatiGlobali.length, "risultati");
                  currentResultIndex = 0;
                  currentSearchTerm = event.data.term;
                  createSimpleNavigationPanel(risultatiGlobali);

                  setTimeout(() => {
                    console.log("Avvio navigazione al primo risultato con ritardo");
                    try {
                      window.navigateToResult(0);
                    } catch (error) {
                      console.error("Errore durante la navigazione al primo risultato:", error);
                    }
                  }, 500);
                }
              }
              
              // ✅ CORREZIONE: Stessa logica per toggleContent
              else if (event.data.action === 'toggleContent') {
                if (risultatiGlobali && risultatiGlobali.length > 0) {
                  console.log("Creazione controlli di navigazione per", risultatiGlobali.length, "risultati");
                  currentResultIndex = 0;
                  currentSearchTerm = event.data.term;
                  createSimpleNavigationPanel(risultatiGlobali);
                  
                  setTimeout(() => {
                    console.log("Avvio navigazione al primo risultato con ritardo");
                    try {
                      window.navigateToResult(0);
                    } catch (error) {
                      console.error("Errore durante la navigazione al primo risultato:", error);
                    }
                  }, 500);
                }
              }
            });
          }
          
          else if (event.data && event.data.type === 'highlightsCount') {
            console.log(`Ricevuto conteggio risultati: ${event.data.count} occorrenze per "${event.data.term}" nella pagina ${event.data.pageUrl}`);
          }
        });
        
        // Watcher per gestire la classe .narrow della sidebar
        this.$watch('isSidebarNarrow', (newVal) => {
          const sidebar = document.querySelector('.sidebar');
          
          if (sidebar) {
            if (newVal) {
              sidebar.classList.add('narrow');
            } else {
              sidebar.classList.remove('narrow');
            }
          }
        });
      }
    }).mount('#app');
  });

// ✅ NUOVO: Funzione per caricare direttamente una pagina
function loadPageDirectly(pageIndex, pageName, pageFile) {
  const container = document.querySelector('.content');
  if (!container) return;

  // Nascondi il preview
  const preview = container.querySelector('.page-title-preview');
  if (preview) preview.style.display = 'none';

  // Cerca un iframe già esistente
  let iframe = container.querySelector('.page-content');

  // Se l'iframe esiste e la src è già quella giusta, non fare nulla
  if (iframe && iframe.src && iframe.src.endsWith(pageFile)) {
    // Solo highlight se serve
    setTimeout(() => {
      highlightInCurrentPage(risultatiGlobali[currentResultIndex]);
    }, 100);
    return;
  }

  // Se esiste un iframe ma la src è diversa, aggiorna la src senza ricreare l'elemento
  if (iframe) {
    iframe.onload = function() {
      setTimeout(() => {
        iframe.contentWindow.postMessage({ type: 'checkHighlighter' }, '*');
        if (currentSearchTerm && risultatiGlobali && risultatiGlobali.length > 0) {
          highlightInCurrentPage(risultatiGlobali[currentResultIndex]);
        }
      }, 20);
    };
    iframe.src = pageFile;
  } else {
    // Se non esiste, crealo
    iframe = document.createElement('iframe');
    iframe.className = 'page-content';
    iframe.style.cssText = 'width: 100%; height: 100%; border: none; display: block;';
    iframe.onload = function() {
      setTimeout(() => {
        iframe.contentWindow.postMessage({ type: 'checkHighlighter' }, '*');
        if (currentSearchTerm && risultatiGlobali && risultatiGlobali.length > 0) {
          highlightInCurrentPage(risultatiGlobali[currentResultIndex]);
        }
      }, 20);
    };
    container.appendChild(iframe);
    iframe.src = pageFile;
  }
}

// Funzione per evidenziare nella pagina corrente
function highlightInCurrentPage(result) {
  const iframe = document.querySelector('.page-content');
  if (!iframe || !iframe.contentWindow) {
    console.error("Impossibile trovare l'iframe o il contentWindow");
    return;
  }

  // Trova tutti i risultati della pagina corrente
  const pageResults = risultatiGlobali.filter(r => r.pageIndex === result.pageIndex);

  // Costruisci l'array matches per tutti i risultati della pagina
  const matches = pageResults.map(r => ({
    selector: r.selector
    ? r.selector
    : (r.isButtonMatch ? 'h1,h2,h3,h4,h5,h6' : 'p,li,td,th,blockquote,div,span,a'),
    matchText: r.matchText,
    posizioneMatch: r.posizioneMatch,
    elementIndex: r.elementIndex ?? 0
  }));

  // Trova l'indice del match corrente tra quelli della pagina
  const focusIndex = pageResults.findIndex(r =>
    r.matchIndex === result.matchIndex &&
    r.posizioneMatch === result.posizioneMatch &&
    r.matchText === result.matchText
  );

  iframe.contentWindow.postMessage({
    type: 'highlight',
    term: currentSearchTerm,
    matches: matches,
    scrollToMatch: true,
    focusIndex: focusIndex >= 0 ? focusIndex : 0
  }, '*');
}