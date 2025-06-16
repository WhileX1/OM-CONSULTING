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

const { createApp } = Vue

createApp({
  data() {
    return {
      searchTerm: ''
    }
  },
  methods: {
    onSearchInput() {
      // Invia il termine di ricerca alla pagina principale
      window.parent.postMessage({
        type: 'search',
        term: this.searchTerm.trim().toLowerCase()
      }, '*');
    },
    onSearchEnter() {
      // Invia il termine di ricerca con flag action quando si preme Enter
      window.parent.postMessage({
        type: 'search',
        term: this.searchTerm.trim().toLowerCase(),
        action: 'enter'
      }, '*');
    }
  }
}).mount('#app-ricerca');