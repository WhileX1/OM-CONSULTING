// Inizializzazione di Vue
const { createApp } = Vue

// Variabili globali per la navigazione dei risultati
let risultatiGlobali = [];
let currentResultIndex = -1;
let globalNavigationVisible = false;

// Creazione dell'applicazione Vue
createApp({
  // Stato dell'applicazione
  data() {
    return {
      searchTerm: '',          // Termine di ricerca corrente
      includiContenuto: false, // Flag per la ricerca nei contenuti
      inviandoRicerca: false,  // Flag per prevenire troppe richieste ravvicinate
      searchTimer: null,       // Timer per debounce ricerca
      pagine: [],              // Array delle pagine disponibili (caricato dalla sidebar)
      contenutiPagine: {},     // Cache dei contenuti delle pagine per la ricerca
      risultatiRicerca: []     // Risultati della ricerca corrente
    }
  },
  
  // Metodi dell'applicazione
  methods: {
    // Inizializzazione: richiede le pagine alla sidebar
    richiediPagine() {
      window.parent.postMessage({
        type: 'requestPages'
      }, '*');
    },
    
    // Gestisce gli eventi di input nella barra di ricerca con debounce
    onSearchInput() {
      if (this.inviandoRicerca) return;
      
      console.log("onSearchInput chiamata con:", this.searchTerm);
      
      const termine = this.searchTerm.trim().toLowerCase();
      
      if (termine === '') {
        this.risultatiRicerca = [];
        this.inviaRisultatiRicerca([], null);
        return;
      }
      
      // ✅ Cerca immediatamente nei titoli
      const risultatiTitoli = this.cercaNeiTitoli(termine);
      this.risultatiRicerca = risultatiTitoli;

      const risultatiGlobaliTitoli = this.risultatiTitoliDettagliati || [];
      this.inviaRisultatiRicerca(
        [...risultatiTitoli],
        'typing',
        risultatiGlobaliTitoli
      );
      
      // ✅ Solo se includiContenuto è attivo, aggiungi debounce per contenuti
      if (this.includiContenuto) {
        if (this.searchTimer) {
          clearTimeout(this.searchTimer);
        }
        
        this.searchTimer = setTimeout(() => {
          this.eseguiRicerca('delayed');
        }, 300);
      }
    },
    
    // Gestisce l'evento di pressione del tasto Enter
    onSearchEnter() {
      console.log("Enter premuto - eseguo ricerca immediata");

      if (this.searchTimer) {
        clearTimeout(this.searchTimer);
        this.searchTimer = null;
      }

      // ✅ Usa tutti i match dettagliati nei titoli!
      if (this.risultatiRicerca.length > 0 && !this.includiContenuto) {
        const risultatiGlobaliTitoli = this.risultatiTitoliDettagliati || [];
        this.inviaRisultatiRicerca(
          [...this.risultatiRicerca],
          'enter',
          risultatiGlobaliTitoli
        );
      } else {
        this.eseguiRicerca('enter');
      }
    },
    
    // Gestisce il toggle per includere contenuti nella ricerca
    toggleIncludiContenuto() {
      console.log(`Toggle includiContenuto: ora è ${this.includiContenuto}`);
      
      // Cancella il timer di debounce se esiste
      if (this.searchTimer) {
        clearTimeout(this.searchTimer);
        this.searchTimer = null;
      }
      
      // Esegui la ricerca immediatamente con l'azione "toggleContent"
      this.eseguiRicerca('toggleContent');
    },
    
    // Funzione principale di ricerca
    eseguiRicerca(action = null) {
      const termine = this.searchTerm.trim().toLowerCase();

      if (!termine) {
        this.risultatiRicerca = [];
        this.inviaRisultatiRicerca([], action);
        return;
      }

      if (this.pagine.length === 0) {
        console.log("Nessuna pagina disponibile per la ricerca");
        return;
      }

      // ✅ Non serve inviandoRicerca per ricerca nei titoli (è istantanea)
      const risultatiTitoli = this.cercaNeiTitoli(termine);

      // Solo per ricerca nei contenuti serve il flag
      if (!this.includiContenuto) {
        // ...gestione titoli...
        return;
      }

      // ✅ Flag solo per ricerca nei contenuti
      this.inviandoRicerca = true;

      this.cercaNeiContenuti(termine)
        .then(risultatiContenuti => {
          // ✅ CORREZIONE: ora risultatiContenuti è un oggetto {risultati, risultatiGlobali}
          const risultatiContenuto = risultatiContenuti.risultati || [];
          const risultatiGlobali = risultatiContenuti.risultatiGlobali || [];
          
          // ✅ NUOVO: Aggiungi i match dei titoli ai risultatiGlobali
          const risultatiGlobaliCompleti = [];
          
          // Prima aggiungi TUTTI i match dettagliati dei titoli
          (this.risultatiTitoliDettagliati || []).forEach(match => {
            risultatiGlobaliCompleti.push({
              ...match,
              isButtonMatch: true // per coerenza
            });
          });
          
          // Poi aggiungi i contenuti, aggiustando matchIndex
          risultatiGlobali.forEach(r => {
            risultatiGlobaliCompleti.push({
              ...r,
              matchIndex: risultatiGlobaliCompleti.length,
              isButtonMatch: false
            });
          });
          
          // Unisci i risultati dei titoli e dei contenuti senza duplicati  
          const risultatiUniti = [...risultatiTitoli];
          
          // Aggiungi risultati del contenuto che non sono già nei titoli
          risultatiContenuto.forEach(risContenuto => {
            if (!risultatiUniti.some(risTitolo => risTitolo.pageIndex === risContenuto.pageIndex)) {
              risultatiUniti.push(risContenuto);
            }
          });

          this.risultatiRicerca = risultatiUniti;

          // ✅ Invia i risultati con i risultatiGlobali completi
          this.inviaRisultatiRicerca(risultatiUniti, action, risultatiGlobaliCompleti);
        })
        .catch(error => {
          console.error("Errore durante la ricerca nei contenuti:", error);
          // In caso di errore, usa solo i risultati dei titoli
          this.risultatiRicerca = risultatiTitoli;
          this.inviaRisultatiRicerca(risultatiTitoli, action);
        })
        .finally(() => {
          // Nascondi l'indicatore di caricamento
          this.inviandoRicerca = false;
        });
    },
    
    // Cerca nei titoli delle pagine
    cercaNeiTitoli(termine) {
      if (!termine) return [];
      const risultati = [];
      const risultatiDettagliati = [];

      this.pagine.forEach((pagina, indice) => {
        const titolo = String(pagina.nome);
        const titoloLower = titolo.toLowerCase();
        let lastIndex = 0;
        while (lastIndex < titoloLower.length) {
          const idx = titoloLower.indexOf(termine, lastIndex);
          if (idx === -1) break;
          risultati.push(indice); // Per compatibilità con la vecchia logica
          risultatiDettagliati.push({
            pageIndex: indice,
            pagina: titolo,
            file: String(pagina.file),
            matchIndex: risultatiDettagliati.length,
            matchText: titolo.substr(idx, termine.length),
            posizioneMatch: idx,
            tempId: null,
            isButtonMatch: true,
            selector: 'h1',
            elementIndex: 0
          });
          lastIndex = idx + termine.length;
        }
      });

      // Rimuovi duplicati da risultati (solo indici unici)
      const risultatiUnici = [...new Set(risultati)];
      // Salva anche i dettagli per la serializzazione
      this.risultatiTitoliDettagliati = risultatiDettagliati;
      console.log(`📊 Trovati ${risultatiDettagliati.length} match nei titoli per "${termine}"`);
      return risultatiUnici;
    },
    
    // Cerca nei contenuti delle pagine
    async cercaNeiContenuti(termine) {
      const pagine = this.pagine;
      const risultati = [];
      const risultatiGlobali = [];
      const promesse = [];

      for (let i = 0; i < pagine.length; i++) {
        const paginaCorrente = pagine[i];

        promesse.push(
          this.caricaContenutoPagina(i)
            .then(({ html }) => {
              if (!html) return;
              const parser = new DOMParser();
              const doc = parser.parseFromString(html, 'text/html');
              let trovato = false;
              let domOrder = 0;

              // Cerca in titoli e contenuti in ordine di apparizione
              const tags = ['h1','h2','h3','h4','h5','h6','p','li','td','th','blockquote','div','span','a'];
tags.forEach(tag => {
  const elements = doc.querySelectorAll(tag);
  elements.forEach((el, elIdx) => {
    const text = el.textContent;
    let lastIndex = 0;
    while (lastIndex < text.length) {
      const idx = text.toLowerCase().indexOf(termine.toLowerCase(), lastIndex);
      if (idx === -1) break;
      risultatiGlobali.push({
        pageIndex: i,
        pagina: paginaCorrente.nome,
        file: paginaCorrente.file,
        matchIndex: risultatiGlobali.length,
        matchText: text.substr(idx, termine.length),
        posizioneMatch: idx,
        elementIndex: elIdx, // indice relativo al tag
        selector: tag,       // tag singolo!
        tempId: `match-${i}-${tag}-${elIdx}-${idx}`,
        isButtonMatch: ['h1','h2','h3','h4','h5','h6'].includes(tag),
      });
      trovato = true;
      lastIndex = idx + termine.length;
    }
  });
});

              if (trovato) risultati.push(i);
            })
            .catch(error => {
              console.error(`Errore nel caricamento della pagina ${i}:`, error);
            })
        );
      }

      return Promise.all(promesse)
        .then(() => {
          // Ordina i risultati per pagina e ordine nel DOM
          risultatiGlobali.sort((a, b) => {
            if (a.pageIndex !== b.pageIndex) return a.pageIndex - b.pageIndex;
            return a.domOrder - b.domOrder;
          });
          return {
            risultati: risultati,
            risultatiGlobali: risultatiGlobali
          };
        });
    },
    
    // Carica e processa il contenuto di una pagina per la ricerca
    caricaContenutoPagina(indice) {
      const pagina = this.pagine[indice];
      let url = pagina.file;
      if (!url.startsWith('/') && !url.startsWith('http')) {
        url = '../' + url;
      }
      // Usa la cache se disponibile
      if (this.contenutiPagine[url]) {
        return Promise.resolve(this.contenutiPagine[url]);
      }
      return fetch(url)
        .then(response => {
          if (!response.ok) throw new Error(`Errore caricamento ${url}: ${response.status}`);
          return response.text();
        })
        .then(html => {
          // Rimuovi script, style, commenti
          html = html.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
          html = html.replace(/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/gi, '');
          html = html.replace(/<!--[\s\S]*?-->/g, '');
          // Salva in cache sia html che testo
          this.contenutiPagine[url] = { html };
          return { html };
        })
        .catch(error => {
          console.error(`Errore nel caricamento di ${url}:`, error);
          return { html: '' };
        });
    },
    
    // Converte oggetti complessi e Proxy in oggetti JavaScript puri
    serializeForPostMessage(obj) {
      if (!obj) return obj;
      return JSON.parse(JSON.stringify(obj));
    },
    
    // Invia i risultati della ricerca alla sidebar
    inviaRisultatiRicerca(risultati, action = null, risultatiGlobali = []) {
      const messaggio = {
        type: 'searchResults',
        term: this.searchTerm,
        includiContenuto: this.includiContenuto,
        risultati: this.serializeForPostMessage(risultati),
        risultatiGlobali: this.serializeForPostMessage(risultatiGlobali),
        action: action
      };

      console.log(`Invio messaggio di ricerca:`, messaggio.term);
      
      if (window.parent && window.parent !== window) {
        window.parent.postMessage(messaggio, '*');
      }
    },
    
    // Funzioni per gestire la navigazione tra i risultati
    navigateToGlobalHighlight(index) {
      if (risultatiGlobali.length === 0) return;
      
      // Calcola l'indice corretto (con wrap-around)
      currentResultIndex = (index + risultatiGlobali.length) % risultatiGlobali.length;
      const result = risultatiGlobali[currentResultIndex];
      
      // Invia la richiesta di navigazione alla sidebar
      window.parent.postMessage({
        type: 'navigateToResult',
        resultIndex: currentResultIndex,
        result: result
      }, '*');
    },
    
    navigateToNextGlobalHighlight() {
      this.navigateToGlobalHighlight(currentResultIndex + 1);
    },
    
    navigateToPreviousGlobalHighlight() {
      this.navigateToGlobalHighlight(currentResultIndex - 1);
    }
  },
  
  // Lifecycle hooks
  mounted() {
    console.log("Componente ricerca montato");
    
    // Richiedi le pagine alla sidebar
    console.log("Richiesta pagine alla sidebar");
    window.parent.postMessage({
      type: 'requestPages'
    }, '*');
    
    // Ascolta i messaggi dalla sidebar
    window.addEventListener('message', (event) => {
      console.log("Messaggio ricevuto in ricerca:", event.data);
      
      // Ricevi le pagine dalla sidebar
      if (event.data && event.data.type === 'pages') {
        this.pagine = event.data.pages;
        console.log(`Ricevute ${this.pagine.length} pagine dalla sidebar:`, this.pagine);
      }
    });
  }
}).mount('#app-ricerca');

