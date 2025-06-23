const { createApp } = Vue;

createApp({
  data() {
    return {
      showPanel: false,
      openMenu: [false, false, false, false, false, false],
      
      // ✅ MANTENUTI SOLO I DATI NECESSARI PER MODAL 1 e 3
      fontSize: 16,
      letterSpacing: 0,
      underlineLinks: false,
      cursorType: 'normal',
      cursorSize: 1,
      cursorColor: 'default',
      trackCursor: false,
      
      // ✅ RIMOSSI DATI NON NECESSARI
      // colorMode: 'normal',
      // audioMode: null,
      // audioSpeed: 1.0,
      // highlightText: false,
      // keyboardFocus: false,
      // visibleBorders: true,
      // borderWidth: 2,
      // skipLinks: true,
      // keyboardHelp: false,
      // language: 'it',
      // autoTranslate: false,
      // localeDateFormat: true,
      
      items: [
        { icon: 'fa-solid fa-palette', menuTitle: 'Colori' },
        { icon: 'fa-solid fa-font', menuTitle: 'Dimensione testo' },
        { icon: 'fa-solid fa-volume-up', menuTitle: 'Sintesi vocale' },
        { icon: 'fa-solid fa-mouse-pointer', menuTitle: 'Cursore' },
        { icon: 'fa-solid fa-keyboard', menuTitle: 'Navigazione tastiera' },
        { icon: 'fa-solid fa-language', menuTitle: 'Lingua' }
      ],
      
      modalObserver: null,
      modalUpdateTimeout: null,
      isUpdatingModal: false
    }
  },
  
  methods: {
    // ✅ MANTENUTO: Toggle menu e funzionalità di base
    toggleMenu(idx) {
      console.log(`🎯 Tentativo apertura modal ${idx}`);
      
      if (this.isUpdatingModal) {
        console.log('⚠️ Update in corso, skip');
        return;
      }
      
      this.isUpdatingModal = true;
      
      // Feedback visivo migliorato
      const button = document.querySelector(`.hex-${idx} button`);
      if (button) {
        // Aggiungi classe temporanea per animazione click
        button.classList.add('btn-click-animation');
        
        // Effetto scale più evidente
        button.style.transform = 'scale(0.85) translateY(2px)';
        
        setTimeout(() => {
          if (button) {
            button.style.transform = '';
            
            // Se il pulsante è attivo, aggiungi classe active
            if (this.openMenu[idx]) {
              button.classList.add('active');
            } else {
              button.classList.remove('active');
            }
            
            // Rimuovi classe animazione
            setTimeout(() => {
              button.classList.remove('btn-click-animation');
            }, 300);
          }
        }, 200);
      }
      
      // Toggle stato solo della modal corrente
      const wasOpen = this.openMenu[idx];
      this.openMenu[idx] = !wasOpen;
      const newState = this.openMenu[idx];
      
      console.log(`📊 Modal ${idx} ${newState ? 'APERTA' : 'CHIUSA'}`);
      
      this.$forceUpdate();
      
      this.$nextTick(() => {
        const dropdown = document.querySelector(`.dropdown-pos-${idx}`);
        
        if (newState) {
          if (dropdown) {
            // Ritardo per vedere l'animazione del pulsante
            setTimeout(() => {
              dropdown.classList.add('active');
              console.log(`✅ Modal ${idx} aperta`);
            }, 150);
          }
        } else {
          if (dropdown) {
            dropdown.classList.remove('active');
            console.log(`❌ Modal ${idx} chiusa`);
          }
        }
        
        setTimeout(() => {
          this.isUpdatingModal = false;
          this.reconnectObserver();
        }, 300);
      });
    },
    
    // ✅ MANTENUTO: Observer per cursore
    reconnectObserver() {
      if (this.modalObserver) {
        this.modalObserver.disconnect();
      }
      
      this.modalObserver = new MutationObserver(() => {
        if (this.isUpdatingModal) return;
        
        if (this.modalUpdateTimeout) {
          clearTimeout(this.modalUpdateTimeout);
        }
        
        this.modalUpdateTimeout = setTimeout(() => {
          if (window.cursorManager) {
            window.cursorManager.applyCursorSettings();
          }
        }, 200);
      });
      
      this.modalObserver.observe(document.querySelector('.access-panel') || document.body, {
        childList: true,
        subtree: true,
        attributes: false,
        characterData: false
      });
    },
    
    // ✅ MANTENUTO: Helper per posizione dropdown
    dropdownPosition(idx) {
      return `dropdown-pos-${idx}`;
    },
    
    // ✅ RIMOSSO: setColorMode (Modal 0)
    
    // ✅ MANTENUTO: Gestione font size (Modal 1)
    setFontSize() {
      const fontSize = parseInt(this.fontSize);
      document.documentElement.style.fontSize = fontSize + 'px';
      document.body.style.fontSize = 'inherit';
      
      // Scale factor per elementi UI
      const scaleFactor = fontSize / 16;
      document.documentElement.style.setProperty('--font-scale', scaleFactor);
      
      // Aggiorna anche gli elementi specifici
      const elements = document.querySelectorAll('p, span, div, a, button, label');
      elements.forEach(el => {
        if (!el.closest('.access-panel')) {
          el.style.fontSize = 'inherit';
        }
      });
      
      console.log(`✅ Font-size: ${fontSize}px (scale: ${scaleFactor})`);
    },
    
    // ✅ MANTENUTO: Spaziatura lettere (Modal 1)
    setLetterSpacing() {
      const spacing = parseFloat(this.letterSpacing);
      document.body.style.letterSpacing = spacing + 'em';
      
      console.log(`✅ Letter-spacing: ${spacing}em`);
    },
    
    // ✅ MANTENUTO: Sottolineatura link (Modal 1)
    setUnderlineLinks() {
      if (this.underlineLinks) {
        document.body.classList.add('underline-links');
        
        // Aggiungi CSS dinamico per sottolineature
        const style = document.createElement('style');
        style.id = 'underline-links-style';
        style.textContent = `
          body.underline-links a {
            text-decoration: underline !important;
            text-decoration-thickness: 2px !important;
            text-underline-offset: 3px !important;
          }
          body.underline-links a:hover {
            text-decoration: underline !important;
            text-decoration-thickness: 3px !important;
          }
        `;
        document.head.appendChild(style);
      } else {
        document.body.classList.remove('underline-links');
        const existingStyle = document.getElementById('underline-links-style');
        if (existingStyle) {
          existingStyle.remove();
        }
      }
      
      console.log(`✅ Sottolineatura link: ${this.underlineLinks ? 'attiva' : 'disattiva'}`);
    },
    
    // ✅ MANTENUTO: Controlli cursore (Modal 3)
    setCursorType(type) {
      // Rimuovi classe active da tutti i bottoni cursore
      document.querySelectorAll('.cursor-row .cursor-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Aggiungi classe active al bottone cliccato
      event.currentTarget.classList.add('active');
      
      this.addClickFeedback(event.target.closest('.cursor-btn'));
      this.cursorType = type;
      if (window.cursorManager) {
        window.cursorManager.setCursorType(type);
      }
      
      console.log(`✅ Tipo cursore: ${type}`);
    },
    
    setCursorColor(color) {
      // Rimuovi classe active da tutti i bottoni colore
      document.querySelectorAll('.color-row-inline .color-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Aggiungi classe active al bottone cliccato
      event.currentTarget.classList.add('active');
      
      this.addClickFeedback(event.target.closest('.color-btn'));
      this.cursorColor = color;
      if (window.cursorManager) {
        window.cursorManager.setCursorColor(color);
      }
      
      console.log(`✅ Colore cursore: ${color}`);
    },
    
    setCursorSize() {
      this.cursorSize = parseInt(this.cursorSize);
      if (window.cursorManager) {
        window.cursorManager.setCursorSize(this.cursorSize);
      }
      
      console.log(`✅ Dimensione cursore: ${this.cursorSize}x`);
    },
    
    toggleCursorTracking() {
      if (window.cursorManager) {
        window.cursorManager.toggleCursorTracking(this.trackCursor);
      }
      
      console.log(`✅ Traccia cursore: ${this.trackCursor ? 'attiva' : 'disattiva'}`);
    },
    
    // ✅ RIMOSSO: Controlli audio (Modal 2)
    
    // ✅ RIMOSSO: Controlli tastiera (Modal 4)
    
    // ✅ RIMOSSO: Controlli lingua (Modal 5)
    
    // ✅ MANTENUTO: Feedback click migliorato
    addClickFeedback(element, scaleValue = 0.95, duration = 120) {
      if (!element) return;
      
      const originalTransform = element.style.transform;
      element.style.transform = `scale(${scaleValue}) translateY(1px)`;
      element.style.transition = 'transform 0.1s ease-out';
      
      setTimeout(() => {
        element.style.transform = originalTransform;
        setTimeout(() => {
          element.style.transition = '';
        }, duration);
      }, duration);
    },
    
    // ✅ MANTENUTO: Chiudi tutte le modal
    closeAllModals() {
      console.log('🔄 Chiudendo tutte le modal...');
      
      this.openMenu = [false, false, false, false, false, false];
      this.$forceUpdate();
      
      this.$nextTick(() => {
        document.querySelectorAll('.dropdown-menu').forEach(dropdown => {
          dropdown.classList.remove('active');
        });
        
        console.log('✅ Tutte le modal chiuse');
        
        if (window.cursorManager) {
          window.cursorManager.applyCursorSettings();
        }
      });
    },
    
    // ✅ MANTENUTO: Verifica CursorManager
    checkCursorManager() {
      if (window.cursorManager) {
        if (typeof window.cursorManager.applyCursorSettings !== 'function') {
          console.error('❌ CursorManager esiste ma manca il metodo applyCursorSettings');
          
          // Aggiungi il metodo mancante
          window.cursorManager.applyCursorSettings = function() {
            this.setCursorType(this.cursorType);
            this.setCursorColor(this.cursorColor);
            this.setCursorSize(this.cursorSize);
            this.toggleCursorTracking(this.trackCursor);
          };
        }
      }
    },
    
    // Nuovo metodo per cambiare i colori del tema
    changeThemeColor(colorType, colorValue) {
      // Converte un colore esadecimale in componenti RGB
      const hexToRgb = (hex) => {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? 
          `${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)}` : 
          null;
      };
      
      // Aggiorna le variabili CSS del tema
      if (colorType === 'primary') {
        document.documentElement.style.setProperty('--theme-primary', colorValue);
        document.documentElement.style.setProperty('--theme-primary-rgb', hexToRgb(colorValue));
        console.log(`✅ Colore primario aggiornato: ${colorValue}`);
      } else if (colorType === 'secondary') {
        document.documentElement.style.setProperty('--theme-secondary', colorValue);
        document.documentElement.style.setProperty('--theme-secondary-rgb', hexToRgb(colorValue));
        console.log(`✅ Colore secondario aggiornato: ${colorValue}`);
      } else if (colorType === 'dark') {
        document.documentElement.style.setProperty('--theme-dark', colorValue);
        document.documentElement.style.setProperty('--theme-dark-rgb', hexToRgb(colorValue));
        console.log(`✅ Colore scuro aggiornato: ${colorValue}`);
      } else if (colorType === 'alert') {
        document.documentElement.style.setProperty('--theme-alert', colorValue);
        document.documentElement.style.setProperty('--theme-alert-rgb', hexToRgb(colorValue));
        console.log(`✅ Colore allerta aggiornato: ${colorValue}`);
      }
    },
    
    // Per i bottoni delle impostazioni colore (Modal 0)
    setColorTheme(themeName) {
      // Prima rimuovi active da tutti i bottoni di quella modal
      document.querySelectorAll('.daltonismo-row button').forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Aggiungi classe active al bottone cliccato
      event.currentTarget.classList.add('active');
      
      switch(themeName) {
        case 'default':
          this.changeThemeColor('primary', '#30d654');
          this.changeThemeColor('secondary', '#ffffff');
          this.changeThemeColor('dark', '#1a1a1a');
          this.changeThemeColor('alert', '#ff4444');
          break;
        case 'highContrast':
          this.changeThemeColor('primary', '#ffff00');
          this.changeThemeColor('secondary', '#ffffff');
          this.changeThemeColor('dark', '#000000');
          this.changeThemeColor('alert', '#ff0000');
          break;
        case 'darkBlue':
          this.changeThemeColor('primary', '#4287f5');
          this.changeThemeColor('secondary', '#e6e6e6');
          this.changeThemeColor('dark', '#121a29');
          this.changeThemeColor('alert', '#f54242');
          break;
        // Aggiungi altri temi predefiniti secondo necessità
      }
      console.log(`🎨 Tema colore impostato: ${themeName}`);
    }
  },
  
  // ✅ SEMPLIFICATO: Mounted
  mounted() {
    console.log('🎯 Inizializzazione Vue App...');
    
    // Ripara cliccabilità pulsanti
    this.$nextTick(() => {
      // Disattiva il cursore personalizzato per risolvere i problemi di click
      document.documentElement.classList.remove('cursor-applied');
      
      // Fix bottone accessibilità
      const accessBtn = document.querySelector('.access-btn');
      if (accessBtn) {
        accessBtn.style.cursor = 'pointer';
        accessBtn.style.pointerEvents = 'auto';
      }
      
      // Fix bottoni esagono
      document.querySelectorAll('.icon-menu button').forEach(btn => {
        btn.style.cursor = 'pointer';
        btn.style.pointerEvents = 'auto';
      });
      
      // Fix bottone chiudi tutte
      const closeAllBtn = document.querySelector('.close-all-btn');
      if (closeAllBtn) {
        closeAllBtn.style.cursor = 'pointer';
        closeAllBtn.style.pointerEvents = 'auto';
        closeAllBtn.style.zIndex = '10';
      }
      
      // Fix click su bottoni modali
      document.querySelectorAll('.dropdown-menu button').forEach(btn => {
        btn.style.cursor = 'pointer';
        btn.style.pointerEvents = 'auto';
      });
      
      console.log('✅ Fix cliccabilità applicato');
    });
    
    this.checkCursorManager();
    
    // Attendi CursorManager
    const waitForCursor = () => {
      if (window.cursorManager) {
        console.log('✅ CursorManager collegato');
        
        this.checkCursorManager();
        
        // Sincronizza stati iniziali
        window.cursorManager.setCursorType(this.cursorType);
        window.cursorManager.setCursorColor(this.cursorColor);
        window.cursorManager.setCursorSize(this.cursorSize);
        window.cursorManager.toggleCursorTracking(this.trackCursor);
      } else {
        setTimeout(waitForCursor, 100);
      }
    };
    
    waitForCursor();
    this.reconnectObserver();
    
    // Imposta impostazioni iniziali
    this.setFontSize();
    this.setLetterSpacing();
    this.setUnderlineLinks();
    
    console.log('✅ Vue App completamente inizializzata');
  },
  
  // ✅ SEMPLIFICATO: Cleanup
  beforeUnmount() {
    if (this.modalObserver) {
      this.modalObserver.disconnect();
    }
    
    if (this.modalUpdateTimeout) {
      clearTimeout(this.modalUpdateTimeout);
    }
  }
}).mount('#app');