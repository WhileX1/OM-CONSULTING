/* ========================================
   GESTIONE CURSORE PERSONALIZZATO
======================================== */

// ✅ FIX CURSORE - Funzioni essenziali
class CursorManager {
  constructor() {
    this.cursorType = 'normal';
    this.cursorSize = 1;
    this.cursorColor = 'default';
    this.trackCursor = false;
    
    this.cursorTrail = [];
    this.lastTrailUpdate = 0;
    this.mouseStopTimeout = null;
    
    this.trailContainer = null;
    this.mouseMoveHandler = null;
    
    this.colorMap = {
      'default': { color: '#ffffff', rgba: '255, 255, 255' },
      'blue': { color: '#0078d4', rgba: '0, 120, 212' },
      'red': { color: '#d13438', rgba: '209, 52, 56' },
      'green': { color: '#107c10', rgba: '16, 124, 16' },
      'purple': { color: '#881798', rgba: '136, 23, 152' },
      'orange': { color: '#ff8c00', rgba: '255, 140, 0' }
    };
    
    this.init();
  }
  
  // ✅ Inizializza il gestore cursore
  init() {
    console.log('🎯 Inizializzazione CursorManager...');
    
    // Inizializza posizione mouse al centro dello schermo
    const centerX = window.innerWidth / 2;
    const centerY = window.innerHeight / 2;
    document.documentElement.style.setProperty('--mouse-x', centerX + 'px');
    document.documentElement.style.setProperty('--mouse-y', centerY + 'px');
    
    // Event listener per movimento mouse
    this.mouseMoveHandler = this.handleMouseMove.bind(this);
    document.addEventListener('mousemove', this.mouseMoveHandler);
    
    // Inizializza cursore
    this.setupInitialCursor();
    
    // Cleanup su chiusura pagina
    window.addEventListener('beforeunload', () => {
      this.cleanup();
    });
    
    console.log('✅ CursorManager inizializzato');
  }
  
  // ✅ Imposta il cursore iniziale
  setupInitialCursor() {
    this.updateDynamicColors();
    this.updateSVGColor();
    
    // Debug stato cursore
    setTimeout(() => {
      console.log('🔍 DEBUG Cursore:', {
        bodyClasses: document.body.className,
        htmlClasses: document.documentElement.className,
        mouseX: getComputedStyle(document.documentElement).getPropertyValue('--mouse-x'),
        mouseY: getComputedStyle(document.documentElement).getPropertyValue('--mouse-y'),
        svg: getComputedStyle(document.documentElement).getPropertyValue('--cursor-svg')
      });
    }, 200);
  }
  
  // ✅ Gestisce il movimento del mouse
  handleMouseMove(e) {
    const x = e.clientX;
    const y = e.clientY;
    
    // Aggiorna posizione cursore
    document.documentElement.style.setProperty('--mouse-x', x + 'px');
    document.documentElement.style.setProperty('--mouse-y', y + 'px');
    
    // Gestisci traccia cursore se attiva
    if (this.trackCursor) {
      const now = Date.now();
      
      if (now - this.lastTrailUpdate > 8) {
        const lastPoint = this.cursorTrail[this.cursorTrail.length - 1];
        const minDistance = 2;
        
        if (!lastPoint || 
            Math.abs(x - lastPoint.x) > minDistance || 
            Math.abs(y - lastPoint.y) > minDistance) {
          
          this.cursorTrail.push({ x, y, time: now });
          
          const maxTrailLength = 15;
          if (this.cursorTrail.length > maxTrailLength) {
            this.cursorTrail.shift();
          }
          
          const maxAge = 800;
          this.cursorTrail = this.cursorTrail.filter(point => 
            now - point.time < maxAge
          );
          
          this.drawSimpleTrail();
          this.lastTrailUpdate = now;
        }
      }
      
      // Cleanup più veloce
      if (this.mouseStopTimeout) {
        clearTimeout(this.mouseStopTimeout);
      }
      
      this.mouseStopTimeout = setTimeout(() => {
        this.clearCursorTrail();
      }, 1500);
    }
  }
  
  // ✅ AGGIORNA COLORI DINAMICI
  updateDynamicColors() {
    const colorInfo = this.colorMap[this.cursorColor] || this.colorMap.default;
    document.documentElement.style.setProperty('--cursor-color', colorInfo.color);
    document.documentElement.style.setProperty('--cursor-color-rgba', colorInfo.rgba);
  }
  
  // ✅ AGGIORNA SVG CURSORE
  updateSVGColor() {
    const colorInfo = this.colorMap[this.cursorColor] || this.colorMap.default;
    const color = colorInfo.color;
    const colorEncoded = encodeURIComponent(color);
    
    let svgStr = '';
    
    if (this.cursorType === 'normal') {
      svgStr = `url("data:image/svg+xml;charset=utf-8,
        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
          <path d='m3 3 7.07 16.97 2.51-7.39 7.39-2.51L3 3z' fill='${colorEncoded}' stroke='%23000000' stroke-width='1'/>
          <path d='m13 13 6 6' stroke='%23000000' stroke-width='3'/>
          <path d='m13 13 6 6' stroke='${colorEncoded}' stroke-width='1'/>
        </svg>
      ")`;
    } else if (this.cursorType === 'cross') {
      svgStr = `url("data:image/svg+xml;charset=utf-8,
        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='${colorEncoded}' stroke-width='2'>
          <path d='M12 2v20M2 12h20'/>
        </svg>
      ")`;
    } else {
      // Default per il cursore large
      svgStr = `url("data:image/svg+xml;charset=utf-8,
        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'>
          <circle cx='12' cy='12' r='10' fill='${colorEncoded}' stroke='%23000000' stroke-width='1'/>
        </svg>
      ")`;
    }
    
    document.documentElement.style.setProperty('--cursor-svg', svgStr);
    console.log('SVG impostato per colore:', this.cursorColor);
  }
  
  // ✅ Imposta tipo cursore
  setCursorType(type) {
    this.cursorType = type;
    
    // Rimuovi classi precedenti
    document.body.classList.remove('cursor-normal', 'cursor-large', 'cursor-cross');
    
    // Aggiungi nuova classe
    document.body.classList.add(`cursor-${type}`);
    
    // Aggiorna SVG
    this.updateSVGColor();
    
    console.log(`✅ Cursore personalizzato - tipo: ${type}, colore: ${this.cursorColor}, dimensione: ${this.cursorSize}x`);
  }
  
  // ✅ Imposta colore cursore
  setCursorColor(color) {
    this.cursorColor = color;
    this.updateDynamicColors();
    this.updateSVGColor();
    
    console.log(`✅ Colore cursore: ${color}`);
  }
  
  // ✅ Imposta dimensione cursore
  setCursorSize(size) {
    this.cursorSize = size;
    document.documentElement.style.setProperty('--cursor-size', size);
    
    console.log(`✅ Dimensione cursore: ${size}x`);
  }
  
  // ✅ Attiva/disattiva tracciamento cursore
  toggleCursorTracking(active) {
    this.trackCursor = active;
    
    if (active) {
      // Implementazione attivazione trail...
    } else {
      // Implementazione disattivazione trail...
    }
    
    console.log(`✅ Traccia cursore: ${active ? 'attiva' : 'disattivata'}`);
  }
  
  // ✅ Applica tutte le impostazioni cursore
  applyCursorSettings() {
    this.setCursorType(this.cursorType);
    this.setCursorColor(this.cursorColor);
    this.setCursorSize(this.cursorSize);
    this.toggleCursorTracking(this.trackCursor);
  }
  
  // ✅ Pulizia risorse
  cleanup() {
    if (this.mouseMoveHandler) {
      document.removeEventListener('mousemove', this.mouseMoveHandler);
    }
    
    if (this.trailContainer) {
      document.body.removeChild(this.trailContainer);
    }
  }
}

// Inizializza cursore globale
window.cursorManager = new CursorManager();