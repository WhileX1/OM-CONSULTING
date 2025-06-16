// Importa Vue dal global scope
const { createApp } = Vue

// Crea una nuova applicazione Vue
createApp({
  // Stato dell'applicazione
  data() {
    return {
      // Array delle pagine disponibili con nome e percorso del file
      pagine: [
        { nome: 'Home', file: 'sidebar/sidebar-pagine/sidebar-home.html' },
        { nome: 'John Lennon', file: 'sidebar/sidebar-pagine/pagina1.html' },
        { nome: 'Thomas Edison', file: 'sidebar/sidebar-pagine/pagina2.html' },
        { nome: 'Leonardo da Vinci', file: 'sidebar/sidebar-pagine/pagina3.html' },
        { nome: 'Dante Alighieri, Inferno XXVI', file: 'sidebar/sidebar-pagine/pagina4.html' },
        { nome: 'Friedrich Nietzsche', file: 'sidebar/sidebar-pagine/pagina5.html' },
        { nome: 'Mahatma Gandhi', file: 'sidebar/sidebar-pagine/pagina6.html' }
      ],
      paginaAttiva: null,        // Indice della pagina selezionata (null = nessuna)
      risultatiRicerca: [],      // Array degli indici delle pagine che corrispondono alla ricerca
      ricercaInCorso: false,     // Flag per tenere traccia dello stato della ricerca
      contenutiPagine: {}        // Cache dei contenuti delle pagine (chiave = URL, valore = testo)
    }
  },
  // Metodi dell'applicazione
  methods: {
    // Gestisce la selezione/deselezione di una pagina dalla sidebar
    vaiAllaPagina(idx) {
      if (this.paginaAttiva === idx) {
        this.paginaAttiva = null; // Deseleziona se già attiva
      } else {
        this.paginaAttiva = idx;  // Seleziona la nuova pagina
        
        // Aggiorna lo stile se la pagina è anche un risultato di ricerca
        this.$nextTick(() => {
          if (this.corrispondeRicerca(idx)) {
            const buttons = document.querySelectorAll('.sidebar button');
            if (buttons[idx]) {
              // Riapplica le classi nell'ordine corretto per lo stile appropriato
              buttons[idx].classList.remove('active');
              buttons[idx].classList.remove('search-match');
              buttons[idx].classList.add('search-match');
              buttons[idx].classList.add('active');
            }
          }
        });
      }
    },
    
    // Verifica se una pagina corrisponde ai risultati di ricerca correnti
    corrispondeRicerca(indice) {
      return this.risultatiRicerca.includes(indice);
    },
    
    // Carica e processa il contenuto di una pagina per la ricerca
    caricaContenutoPagina(indice) {
      const pagina = this.pagine[indice];
      const url = pagina.file;
      
      // Usa la cache se disponibile
      if (this.contenutiPagine[url]) {
        return Promise.resolve(this.contenutiPagine[url]);
      }
      
      // Altrimenti carica il contenuto con fetch
      return fetch(url)
        .then(response => {
          if (!response.ok) {
            throw new Error(`Errore caricamento ${url}: ${response.status}`);
          }
          return response.text();
        })
        .then(html => {
          // Rimuovi i commenti CDATA e altri commenti HTML per evitare falsi positivi
          html = html.replace(/\/\/\s*<!\[CDATA\[[\s\S]*?\]\]>/g, '');
          html = html.replace(/<!--[\s\S]*?-->/g, '');
          
          // Usa DOMParser per estrarre il testo in modo più affidabile
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          
          // Estrai solo il testo dal corpo della pagina, non dagli script
          let testo = '';
          
          // Seleziona solo elementi di testo visibili, ignorando script e stili
          Array.from(doc.body.querySelectorAll('p, h1, h2, h3, h4, h5, h6, span, div, li, a, blockquote'))
            .forEach(el => {
              testo += el.textContent + ' ';
            });
          
          // Fallback: se non ci sono elementi specifici, usa tutto il body text
          if (!testo.trim()) {
            testo = doc.body.textContent;
          }
          
          testo = testo.trim();
          
          // Salva in cache per futuri utilizzi
          this.contenutiPagine[url] = testo;
          return testo;
        })
        .catch(error => {
          console.error(`Errore nel caricamento di ${url}:`, error);
          return ''; // Ritorna stringa vuota in caso di errore
        });
    },
    
    // Cerca il termine specificato nei contenuti di tutte le pagine
    cercaNeiContenuti(termine) {
      this.ricercaInCorso = true;
      const promesse = [];
      
      console.log(`Cercando "${termine}" nei contenuti...`);
      
      // Usa regex per trovare corrispondenze più precise (parole intere o parziali)
      const regex = new RegExp(`\\b${termine}\\b|\\b${termine}|${termine}\\b`, 'i');
      
      // Per ogni pagina, carica il contenuto e cerca il termine
      for (let i = 0; i < this.pagine.length; i++) {
        promesse.push(
          this.caricaContenutoPagina(i)
            .then(testo => {
              // Usa la regex per una ricerca più accurata
              const trovato = regex.test(testo);
              console.log(`Pagina ${i} (${this.pagine[i].nome}): ${trovato ? "TROVATO" : "non trovato"}`);
              
              // Se trovato, mostra il contesto per debug
              if (trovato) {
                const match = testo.match(regex);
                if (match) {
                  const pos = match.index;
                  const estratto = testo.substring(
                    Math.max(0, pos - 30),
                    Math.min(testo.length, pos + termine.length + 30)
                  );
                  console.log(`Contesto: "...${estratto}..."`);
                }
              }
              
              // Ritorna l'indice se trovato, altrimenti -1
              return trovato ? i : -1;
            })
        );
      }
      
      // Attendi che tutte le ricerche siano completate
      return Promise.all(promesse)
        .then(risultati => {
          // Filtra solo gli indici validi (quelli trovati)
          const indiciTrovati = risultati.filter(indice => indice !== -1);
          console.log(`Risultati nei contenuti:`, indiciTrovati);
          return indiciTrovati;
        })
        .finally(() => {
          // Resetta il flag al termine della ricerca
          this.ricercaInCorso = false;
        });
    },
    
    // Funzione principale di ricerca che coordina titoli e contenuti
    eseguiRicerca(termine, includiContenuto) {
      // Se il termine è vuoto, resetta i risultati
      if (!termine) {
        this.risultatiRicerca = [];
        return Promise.resolve([]);
      }
      
      // Prima cerca nei titoli delle pagine
      const risultatiTitoli = this.pagine
        .map((pagina, indice) => 
          pagina.nome.toLowerCase().includes(termine) ? indice : -1)
        .filter(indice => indice !== -1);
      
      // Se non serve cercare nei contenuti, ritorna subito i risultati dei titoli
      if (!includiContenuto) {
        this.risultatiRicerca = risultatiTitoli;
        return Promise.resolve(risultatiTitoli);
      }
      
      // Altrimenti cerca anche nei contenuti
      return this.cercaNeiContenuti(termine)
        .then(risultatiContenuti => {
          // Unisci i risultati dei titoli e dei contenuti senza duplicati
          const risultatiUniti = [...new Set([...risultatiTitoli, ...risultatiContenuti])];
          this.risultatiRicerca = risultatiUniti;
          
          // Se la pagina attiva è tra i risultati, aggiorna il suo stile
          if (this.paginaAttiva !== null && risultatiUniti.includes(this.paginaAttiva)) {
            this.$nextTick(() => {
              const buttons = document.querySelectorAll('.sidebar button');
              if (buttons[this.paginaAttiva]) {
                buttons[this.paginaAttiva].classList.add('search-match');
              }
            });
          }
          
          return risultatiUniti;
        });
    }
  },
  // Lifecycle hook: quando l'app è montata
  mounted() {
    // Ascolta i messaggi dall'iframe di ricerca
    window.addEventListener('message', (event) => {
      if (event.data && event.data.type === 'search') {
        const searchTerm = event.data.term;
        const includiContenuto = event.data.includiContenuto;
        
        // Ignora messaggi con includiContenuto undefined (duplicati)
        if (includiContenuto === undefined) {
          return;
        }
        
        // Esegui la ricerca
        this.eseguiRicerca(searchTerm, includiContenuto)
          .then(risultati => {
            // Se è stato premuto Enter e ci sono risultati, attiva il primo
            if (event.data.action === 'enter' && risultati.length > 0) {
              this.paginaAttiva = risultati[0];
              
              // Aggiorna lo stile del bottone attivato
              this.$nextTick(() => {
                const buttons = document.querySelectorAll('.sidebar button');
                if (buttons[risultati[0]]) {
                  buttons[risultati[0]].classList.add('search-match');
                }
              });
            }
          });
      }
    });
  }
// Monta l'app all'elemento con id "app"
}).mount('#app')