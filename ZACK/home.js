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
      paginaAttiva: null
    }
  },
  methods: {
    vaiAllaPagina(idx) {
      this.paginaAttiva = idx;
    }
  }
}).mount('#app')