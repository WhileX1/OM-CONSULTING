// Variabili globali
let currentPage = null;  // Aggiunto per tracciare l'indice della pagina corrente
let currentTerm = '';
let highlights = [];

// Aggiungi stile CSS
if (!document.getElementById('highlight-styles')) {
  const style = document.createElement('style');
  style.id = 'highlight-styles';
  style.textContent = `
    /* TUTTI i match della ricerca: EVIDENZIATURA gialla */
    .search-highlight {
      background-color: #ffff80 !important; /* Giallo chiaro per evidenziare */
      color: black !important;
      padding: 1px 2px !important;
      border-radius: 2px !important;
      box-shadow: 0 0 0 1px rgba(0,0,0,0.1) !important; /* Ombra sottile */
    }
    
    /* Focus: evidenziatura più intensa + animazione pulse */
    .highlight-focus {
      animation: showFullTextWithPulse 2s ease-in-out !important;
      background-color: #ffcc00 !important; /* Giallo più intenso per il focus */
      color: black !important;
      font-weight: bold !important;
      box-shadow: 0 0 2px 1px rgba(0,0,0,0.2) !important; /* Ombra più visibile */
    }
    
    /* Animazione pulse dal sidebar.css */
    @keyframes showFullTextWithPulse {
      0%, 70% {
        overflow: hidden;
        transform: scale(1);
      }
      85% {
        overflow: visible;
        transform: scale(1.03);
      }
      100% {
        overflow: visible;
        transform: scale(1);
      }
    }
  `;
  document.head.appendChild(style);
}

// Funzione per evidenziare il termine nel contenuto
function highlightTermInContent(term, tempId = null, matchInfo = {}) {
  if (!term || term.trim() === '') return [];

  // Normalizza il termine per la ricerca
  const termLower = term.toLowerCase();
  const highlights = [];
  const elements = document.querySelectorAll('p, li, h1, h2, h3, h4, h5, h6, td, th, div:not(script):not(style), span, a');
  
  console.log(`Evidenziazione per "${term}" con matchInfo:`, matchInfo);

  // Creiamo un contatore globale per gli highlight
  let globalMatchIndex = 0;
  
  elements.forEach(element => {
    if (!element.textContent || element.textContent.trim() === '') return;
    if (element.closest('script, style, noscript, iframe')) return;

    // Salva il contenuto originale se non è già stato fatto
    if (!element.dataset.original) {
      element.dataset.original = element.innerHTML;
    }
    
    // Ripristina il contenuto originale
    element.innerHTML = element.dataset.original;

    // Trova tutte le occorrenze del termine
    const text = element.textContent;
    const textLower = text.toLowerCase();
    let lastIndex = 0;
    let newHTML = '';
    let localMatchIndex = 0;
    
    // Cicla attraverso tutte le occorrenze
    while (lastIndex < text.length) {
      const matchIndex = textLower.indexOf(termLower, lastIndex);
      
      // Nessun altro match trovato
      if (matchIndex === -1) {
        newHTML += text.substring(lastIndex);
        break;
      }
      
      // Aggiungi il testo prima del match
      newHTML += text.substring(lastIndex, matchIndex);
      
      // Crea un ID univoco per questo highlight
      const uniqueId = `highlight-${currentPage}-${globalMatchIndex}`;
      
      // Aggiungi il match evidenziato SENZA SPAZI aggiuntivi
      newHTML += `<span class="search-highlight" data-highlight-id="${uniqueId}" data-doc-position="${matchIndex}" data-global-index="${globalMatchIndex}">${text.substring(matchIndex, matchIndex + term.length)}</span>`;
      
      // Aggiorna gli indici
      lastIndex = matchIndex + term.length;
      localMatchIndex++;
      globalMatchIndex++;
    }
    
    // Se sono stati trovati match in questo elemento, aggiorna il suo HTML
    if (newHTML !== text) {
      element.innerHTML = newHTML;
    }
  });

  // Raccogli tutti gli highlight
  const allHighlights = document.querySelectorAll('.search-highlight');
  allHighlights.forEach(h => highlights.push(h));

  console.log(`Creati ${highlights.length} highlight per "${term}"`);
  
  return highlights;
}

// Funzione per pulire le evidenziazioni
function clearHighlighting() {
  const elements = document.querySelectorAll('[data-original]');
  elements.forEach(element => {
    if (element.dataset.original) {
      element.innerHTML = element.dataset.original;
    }
  });
}

// Funzione per applicare il focus
function applyFocusToHighlight(index) {
  if (!highlights || highlights.length === 0) {
    console.warn("Nessun highlight trovato per applicare il focus");
    return;
  }
  
  if (index < 0 || index >= highlights.length) {
    console.warn(`Indice ${index} non valido per ${highlights.length} highlights, usando 0`);
    index = 0;
  }
  
  // Rimuovi il focus da tutti gli elementi
  highlights.forEach(h => h.classList.remove('highlight-focus'));
  
  // Verifica se l'highlight corrisponde al tempId o all'indice specifico
  try {
    // Trova l'highlight corretto
    let targetHighlight = highlights[index];
    
    // Aggiungi la classe di focus
    targetHighlight.classList.add('highlight-focus');
    
    // Scorri verso l'elemento con offset
    const yOffset = -100;
    const y = targetHighlight.getBoundingClientRect().top + window.pageYOffset + yOffset;
    
    window.scrollTo({
      top: y,
      behavior: 'smooth'
    });
    
    console.log(`✅ Focus applicato all'elemento ${index}, testo: "${targetHighlight.textContent}"`);
  } catch (error) {
    console.error(`❌ Errore nell'applicare il focus:`, error);
  }
}

// Migliora la gestione dei messaggi
window.addEventListener('message', function(event) {
  if (event.data && event.data.type === 'highlight') {
    clearHighlighting();
    highlights = [];

    // ✅ Sposta qui la funzione escapeHtml
    function escapeHtml(str) {
      return str.replace(/[&<>"']/g, function(m) {
        return ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#39;'
        })[m];
      });
    }

    if (Array.isArray(event.data.matches) && event.data.matches.length > 0) {
      // 1. Raggruppa tutti i match per elemento
      const elementMap = new Map();
      event.data.matches.forEach(match => {
        if (!match.selector) return;
        const elements = document.querySelectorAll(match.selector);
        const element = elements[match.elementIndex ?? 0];
        if (!element) return;
        const key = match.selector + '|' + match.elementIndex;
        if (!elementMap.has(key)) {
          elementMap.set(key, { element, matches: [] });
        }
        elementMap.get(key).matches.push(match);
      });

      // 2. Per ogni elemento, applica tutti gli highlight in una sola volta
      elementMap.forEach(({ element, matches }) => {
        // Salva e ripristina sempre il contenuto originale
        if (!element.dataset.original) {
          element.dataset.original = element.textContent;
        }
        const text = element.dataset.original;

        // Trova tutte le posizioni dei match SENZA modificare l'HTML
        let positions = [];
        matches.forEach(match => {
          const idx = match.posizioneMatch;
          const matchLen = match.matchText.length;
          // Verifica che il testo corrisponda esattamente al match richiesto
          if (
            idx >= 0 &&
            idx + matchLen <= text.length &&
            text.substring(idx, idx + matchLen).toLowerCase() === match.matchText.toLowerCase()
          ) {
            positions.push({ idx, matchLen });
          }
        });

        // Ordina le posizioni per idx crescente
        positions.sort((a, b) => a.idx - b.idx);

        // Ricostruisci l'HTML una sola volta, partendo dal testo originale
        let newHTML = '';
        let lastIndex = 0;
        positions.forEach(({ idx, matchLen }) => {
          newHTML += escapeHtml(text.substring(lastIndex, idx));
          newHTML += `<span class="search-highlight">${escapeHtml(text.substring(idx, idx + matchLen))}</span>`;
          lastIndex = idx + matchLen;
        });
        newHTML += escapeHtml(text.substring(lastIndex));
        element.innerHTML = newHTML;
      });

      // Dopo aver ricostruito tutti gli highlight, aggiorna highlights in ordine
      highlights = Array.from(document.querySelectorAll('.search-highlight'));

      // Applica il focus al match richiesto
      if (typeof event.data.focusIndex === 'number' && highlights.length > 0) {
        applyFocusToHighlight(event.data.focusIndex);
      }
    } else {
      // fallback: evidenzia tutto come prima
      highlights = highlightTermInContent(event.data.term);
    }
  }
});

// Nuova funzione per evidenziare solo nei titoli
function highlightTermInTitles(term) {
  if (!term || term.trim() === '') return [];
  const termLower = term.toLowerCase();
  const highlights = [];
  const elements = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
  let globalMatchIndex = 0;

  elements.forEach(element => {
    if (!element.textContent || element.textContent.trim() === '') return;
    if (!element.dataset.original) {
      element.dataset.original = element.innerHTML;
    }
    element.innerHTML = element.dataset.original;

    const text = element.textContent;
    const textLower = text.toLowerCase();
    let lastIndex = 0;
    let newHTML = '';

    while (lastIndex < text.length) {
      const matchIndex = textLower.indexOf(termLower, lastIndex);
      if (matchIndex === -1) {
        newHTML += text.substring(lastIndex);
        break;
      }
      newHTML += text.substring(lastIndex, matchIndex);
      const uniqueId = `highlight-title-${currentPage}-${globalMatchIndex}`;
      newHTML += `<span class="search-highlight" data-highlight-id="${uniqueId}" data-doc-position="${matchIndex}" data-global-index="${globalMatchIndex}">${text.substring(matchIndex, matchIndex + term.length)}</span>`;
      lastIndex = matchIndex + term.length;
      globalMatchIndex++;
    }

    if (newHTML !== text) {
      element.innerHTML = newHTML;
    }
  });

  const allHighlights = document.querySelectorAll('.search-highlight');
  allHighlights.forEach(h => highlights.push(h));
  return highlights;
}

