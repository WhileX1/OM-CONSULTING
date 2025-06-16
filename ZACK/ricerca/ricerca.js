// Codice legacy (con vanilla JavaScript) - può essere rimosso poiché sostituito da Vue
document.addEventListener('DOMContentLoaded', function() {
  const searchBar = document.querySelector('.search-bar');
  
  // Ricerca in tempo reale mentre l'utente digita
  searchBar.addEventListener('input', function() {
    const searchTerm = searchBar.value.trim().toLowerCase();
    
    // Invia il termine di ricerca alla pagina principale
    window.parent.postMessage({
      type: 'search',
      term: searchTerm
    }, '*');
  });
  
  // Aggiunta: gestione del tasto Invio
  searchBar.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      const searchTerm = searchBar.value.trim().toLowerCase();
      
      // Invia il termine di ricerca con un flag speciale per l'azione Invio
      window.parent.postMessage({
        type: 'search',
        term: searchTerm,
        action: 'enter'
      }, '*');
    }
  });
});

// Inizializzazione di Vue
const { createApp } = Vue

// Creazione dell'applicazione Vue
createApp({
  // Stato dell'applicazione
  data() {
    return {
      searchTerm: '',         // Termine di ricerca corrente
      includiContenuto: false, // Flag per la ricerca nei contenuti
      inviandoRicerca: false  // Flag per prevenire troppe richieste ravvicinate
    }
  },
  // Metodi dell'applicazione
  methods: {
    // Gestisce gli eventi di input nella barra di ricerca con debounce
    onSearchInput() {
      // Previene invii multipli troppo ravvicinati
      if (this.inviandoRicerca) return;
      
      // Imposta il flag e usa setTimeout per debounce
      this.inviandoRicerca = true;
      setTimeout(() => {
        // Invia messaggio alla finestra principale con il termine di ricerca
        window.parent.postMessage({
          type: 'search',
          term: this.searchTerm.trim().toLowerCase(),
          includiContenuto: this.includiContenuto
        }, '*');
        // Resetta il flag dopo l'invio
        this.inviandoRicerca = false;
      }, 100);
    },
    
    // Gestisce l'evento di pressione del tasto Enter
    onSearchEnter() {
      // Invia messaggio con flag action='enter'
      window.parent.postMessage({
        type: 'search',
        term: this.searchTerm.trim().toLowerCase(),
        includiContenuto: this.includiContenuto,
        action: 'enter'
      }, '*');
    },
    
    // Gestisce il toggle per includere contenuti nella ricerca
    toggleIncludiContenuto() {
      // Inverte lo stato del flag
      this.includiContenuto = !this.includiContenuto;
      // Attende il prossimo ciclo di rendering e invia la ricerca
      this.$nextTick(() => {
        this.onSearchInput();
      });
    }
  }
// Monta l'app nell'elemento con id 'app-ricerca'
}).mount('#app-ricerca');