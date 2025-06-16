document.addEventListener('DOMContentLoaded', function() {
  const searchBar = document.querySelector('.search-bar');
  
  searchBar.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      const searchTerm = searchBar.value.trim().toLowerCase();
      
      // Comunica con la pagina principale per eseguire la ricerca
      window.parent.postMessage({
        type: 'search',
        term: searchTerm
      }, '*');
    }
  });
});