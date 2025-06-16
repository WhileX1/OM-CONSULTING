const { createApp } = Vue
createApp({
  data() {
    return {
      pagine: [
        { nome: 'Home', file: 'sidebar/sidebar-pagine/sidebar-home.html' },
        { nome: 'John Lennon', file: 'sidebar/sidebar-pagine/pagina1.html' },
        { nome: 'Thomas Edison', file: 'sidebar/sidebar-pagine/pagina2.html' },
        { nome: 'Leonardo da Vinci', file: 'sidebar/sidebar-pagine/pagina3.html' },
        { nome: 'Dante Alighieri, Inferno XXVI', file: 'sidebar/sidebar-pagine/pagina4.html' },
        { nome: 'Friedrich Nietzsche', file: 'sidebar/sidebar-pagine/pagina5.html' },
        { nome: 'Mahatma Gandhi', file: 'sidebar/sidebar-pagine/pagina6.html' }
      ],
      paginaAttiva: null,
      risultatiRicerca: [] // Nuovo array per tenere traccia delle pagine che corrispondono alla ricerca
    }
  },
  methods: {
    vaiAllaPagina(idx) {
      if (this.paginaAttiva === idx) {
        this.paginaAttiva = null; // Deseleziona se già attivo
      } else {
        this.paginaAttiva = idx;
      }
    },
    // Nuovo metodo per verificare se una pagina corrisponde alla ricerca
    corrispondeRicerca(indice) {
      return this.risultatiRicerca.includes(indice);
    }
  },
  mounted() {
    // Ascolta i messaggi dall'iframe di ricerca
    window.addEventListener('message', (event) => {
      if (event.data && event.data.type === 'search') {
        const searchTerm = event.data.term;
        
        if (!searchTerm) {
          // Se la ricerca è vuota, resetta i risultati
          this.risultatiRicerca = [];
          return;
        }
        
        // Cerca nelle pagine
        this.risultatiRicerca = this.pagine
          .map((pagina, indice) => 
            pagina.nome.toLowerCase().includes(searchTerm) ? indice : -1)
          .filter(indice => indice !== -1);
        
        // Aggiunta: se l'utente ha premuto Invio, vai alla prima pagina trovata
        if (event.data.action === 'enter' && this.risultatiRicerca.length > 0) {
          // Vai alla prima pagina che corrisponde alla ricerca
          this.vaiAllaPagina(this.risultatiRicerca[0]);
        }
      }
    });
  }
}).mount('#app')