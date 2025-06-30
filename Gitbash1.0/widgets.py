import os
import tkinter as tk
from tkinter import filedialog
from helpers import (MouseWheelHelper, set_placeholder, clear_placeholder, 
                     count_selected_files, update_counter_var)

class FileSelectionWindow:
    def __init__(self, parent, files, num_var, on_files_saved, app_ref=None):
        self.parent = parent
        self.files = files
        self.num_var = num_var
        self.on_files_saved = on_files_saved
        self._app_ref = app_ref  # riferimento all'app principale
        self.placeholder = "Nessun path inserito"
        self.placeholder_fg = "#888"
        self.normal_fg = "#000"
        self.selected_count_var = tk.StringVar()
        self.file_entries = []
        self._row_widgets = []  # Per tracciare i widget delle righe
        self.win = tk.Toplevel(parent)
        self.win.title("Seleziona file da aggiungere")
        self.win.geometry("460x340")
        self.win.resizable(False, False)
        self.win.focus()
        self.win.attributes("-topmost", True)
        self.win.protocol("WM_DELETE_WINDOW", self.on_cancel)
        # Se la finestra viene distrutta in modo anomalo, azzera il riferimento nell'app
        if self._app_ref is not None:
            self.win.bind("<Destroy>", lambda e: self._on_destroy())
        # --- Nuovo layout: frame principale con due sezioni verticali ---
        self.main_frame = tk.Frame(self.win)
        self.main_frame.pack(fill="both", expand=True)
        # Sezione superiore: scrollabile, stile branch
        self.sugg_container = tk.Frame(self.main_frame, relief="groove", borderwidth=2)
        self.sugg_container.pack(fill="both", expand=True, padx=10, pady=(10,0))
        self.canvas = tk.Canvas(self.sugg_container, borderwidth=0, highlightthickness=0, height=180, bg="#f8f8f8")
        self.canvas.pack(side="left", fill="both", expand=True, padx=0, pady=0)
        self.vscroll = tk.Scrollbar(self.sugg_container, orient="vertical", command=self.canvas.yview, width=18)
        self.vscroll.pack(side="right", fill="y", padx=0, pady=0)
        self.canvas.configure(yscrollcommand=self.vscroll.set)
        self.files_frame = tk.Frame(self.canvas, bg="#f8f8f8")
        self.files_frame_id = self.canvas.create_window((0, 0), window=self.files_frame, anchor="nw")

        def on_canvas_configure(event):
            canvas_width = event.width
            self.canvas.itemconfig(self.files_frame_id, width=canvas_width)
        self.canvas.bind('<Configure>', on_canvas_configure)

        def update_scrollregion(event=None):
            self.canvas.configure(scrollregion=self.canvas.bbox("all"))
        self.files_frame.bind("<Configure>", update_scrollregion)
        # --- Mouse wheel scroll: ora usa la funzione helper centralizzata ---
        get_count = lambda: self.num_var.get()
        self._bind_mousewheel, self._unbind_mousewheel = MouseWheelHelper.setup(self.canvas, self.win, get_count, 9)
        if self.num_var.get() >= 9:
            self._bind_mousewheel()
        self.win.protocol("WM_DELETE_WINDOW", lambda: (self.on_cancel(), self._unbind_mousewheel()))
        self.win.bind("<Destroy>", lambda e: self._unbind_mousewheel())
        # Sezione inferiore: frame pulsanti sempre visibile
        self.close_frame = tk.Frame(self.main_frame)
        self.close_frame.pack(fill="x", pady=(8,16), padx=(24,24), side="bottom")
        self.close_frame.columnconfigure(0, weight=0)
        self.close_frame.columnconfigure(1, weight=1)
        self.close_frame.columnconfigure(2, weight=0)
        btn_annulla = tk.Button(self.close_frame, text="Annulla", command=self.on_cancel, font=("Segoe UI", 10, "bold"))
        btn_annulla.grid(row=0, column=0, padx=4)
        counter_container = tk.Frame(self.close_frame)
        counter_container.grid(row=0, column=1)
        btn_minus = tk.Button(counter_container, text="-", width=2, font=("Segoe UI", 10, "bold"), command=lambda: self.num_var.set(max(1, self.num_var.get()-1)))
        btn_minus.pack(side="left", padx=(0,2))
        tk.Label(counter_container, textvariable=self.selected_count_var, font=("Segoe UI", 10, "bold"), fg="#555", width=6, anchor="center").pack(side="left", padx=(0,2))
        btn_plus = tk.Button(counter_container, text="+", width=2, font=("Segoe UI", 10, "bold"), command=lambda: self.num_var.set(self.num_var.get()+1))
        btn_plus.pack(side="left")
        btn_salva = tk.Button(self.close_frame, text="Salva", command=self.on_save, font=("Segoe UI", 10, "bold"))
        btn_salva.grid(row=0, column=2, padx=4)
        self.num_var.trace_add('write', lambda *a: self.update_ui())
        self.update_ui()
        # Modalità dialog: sempre in primo piano ma non blocca la main window
        self.win.transient(self.parent)
        # self.win.grab_set()  # RIMOSSO: la main window resta interattiva

    # Usa i metodi statici centralizzati di GitGuiApp per i placeholder

    def _make_row(self, idx, var):
        # Crea una riga con pulsante e entry, e la aggiunge a files_frame.
        row_frame = tk.Frame(self.files_frame)
        row_frame.pack(fill="x", padx=1, pady=2)
        btn = tk.Button(row_frame, text=f"File {idx+1}", font=("Segoe UI", 10, "bold"))
        btn.pack(side="left", padx=(0, 4))
        entry_width = 24 if self.num_var.get() > 7 else 38
        entry = tk.Entry(row_frame, textvariable=var, font=("Segoe UI", 10, "bold"), fg=self.placeholder_fg, width=entry_width)
        entry.pack(side="left", fill="x", expand=True, padx=(0,4), ipadx=2)
        btn.config(command=lambda idx=idx, var=var, ent=entry: self.select_file(idx, var, ent))
        entry.bind("<FocusIn>", lambda e, var=var, ent=entry: clear_placeholder(var, ent, self.placeholder, self.normal_fg))
        entry.bind("<FocusOut>", lambda e, var=var, ent=entry: set_placeholder(var, ent, self.placeholder, self.placeholder_fg))
        var.trace_add('write', lambda *a: self.update_selected_count())
        self._row_widgets.append((row_frame, btn, entry))
        return row_frame, btn, entry

    def select_file(self, idx, var, ent):
        # Nascondi la finestra principale e la finestra di selezione prima di aprire la dialog
        self.parent.withdraw()
        self.win.withdraw()
        self.win.attributes("-topmost", False)
        # Permetti solo selezione multipla di file (NO cartelle)
        file_paths = filedialog.askopenfilenames(title=f"Seleziona uno o più file per la posizione {idx+1}")
        self.win.deiconify()
        self.win.attributes("-topmost", True)
        self.parent.deiconify()
        if not file_paths:
            var.set(self.placeholder)
            ent.config(fg=self.placeholder_fg)
            self.files[idx] = None
            return
        n_files = len(file_paths)
        # Selezionati più file del contatore? Aggiorna il contatore e la lista
        if idx + n_files > self.num_var.get():
            self.num_var.set(idx + n_files)
            # Espandi la lista file_entries e files se necessario (update_ui lo farà)
        # Se sono stati selezionati più file, li inserisce a partire da idx
        for i, fpath in enumerate(file_paths):
            if idx + i >= len(self.files):
                self.files.append(None)
            path = os.path.normpath(fpath)
            self.files[idx + i] = path
            # Aggiorna anche la variabile associata all'entry
            if idx + i < len(self.file_entries):
                self.file_entries[idx + i].set(path)
        # Aggiorna il colore dell'entry corrente
        if file_paths:
            ent.config(fg=self.normal_fg)

    def get_selected_count(self):
        # Usa la funzione helper centralizzata
        return count_selected_files([var.get() for var in self.file_entries], self.placeholder)

    def update_selected_count(self):
        update_counter_var(self.selected_count_var, self.get_selected_count, self.num_var)

    def on_cancel(self):
        # Reset contatore e svuota selezione file
        self.num_var.set(1)
        self.files.clear()
        for var in self.file_entries:
            var.set("")
        self.win.destroy()
        self.parent.deiconify()
        self.parent.focus_force()
        # Azzeramento riferimento finestra nell'app principale
        if self._app_ref is not None:
            self._app_ref._file_selection_window = None

    def on_save(self):
        for idx, var in enumerate(self.file_entries):
            val = var.get().strip()
            if val and val != self.placeholder:
                self.files[idx] = val
            else:
                self.files[idx] = None
        self.win.destroy()
        self.parent.deiconify()
        self.parent.focus_force()
        # Azzeramento riferimento finestra nell'app principale
        if self._app_ref is not None:
            self._app_ref._file_selection_window = None
        if self.on_files_saved:
            self.on_files_saved()

    def update_ui(self):
        # Prevent update if window or frame is destroyed
        if not hasattr(self, 'win') or not hasattr(self, 'files_frame'):
            return
        try:
            if not self.win.winfo_exists() or not self.files_frame.winfo_exists():
                return
        except Exception:
            return
        num = int(self.num_var.get())
        prev_values = [var.get() for var in self.file_entries] if self.file_entries else []
        # Rimuovi righe in eccesso e widget associati
        while len(self.file_entries) > num:
            idx = len(self.file_entries) - 1
            try:
                if hasattr(self, '_row_widgets') and idx < len(self._row_widgets):
                    row_frame, btn, entry = self._row_widgets[idx]
                    row_frame.destroy()
                    self._row_widgets.pop()
            except Exception:
                pass
            self.file_entries.pop()
        # Aggiungi righe mancanti tramite funzione privata
        while len(self.file_entries) < num:
            var = tk.StringVar()
            self.file_entries.append(var)
            self._make_row(len(self.file_entries)-1, var)
        # Aggiorna valori
        for i in range(num):
            if i < len(prev_values):
                self.file_entries[i].set(prev_values[i])
            elif i < len(self.files) and self.files[i]:
                self.file_entries[i].set(self.files[i])
            elif not self.file_entries[i].get():
                self.file_entries[i].set("")
        if len(self.files) < num:
            self.files.extend([None]*(num-len(self.files)))
        elif len(self.files) > num:
            del self.files[num:]
        # Imposta il placeholder se il campo è vuoto e aggiorna il colore
        try:
            for i in range(num):
                if hasattr(self, '_row_widgets') and i < len(self._row_widgets):
                    entry_widget = self._row_widgets[i][2]
                    val = self.file_entries[i].get()
                    if not val or val == self.placeholder:
                        # Usa il placeholder senza GitGuiApp
                        entry_widget.config(fg=self.placeholder_fg)
                        self.file_entries[i].set(self.placeholder)
                    else:
                        entry_widget.config(fg=self.normal_fg)
        except Exception:
            pass
        self.selected_count_var.set(f"{self.get_selected_count()}/{num}")
        # Aggiorna larghezza canvas solo se necessario
        if self.canvas is not None and self.files_frame_id is not None:
            if self.files_frame is not None:
                try:
                    # update_idletasks solo se necessario
                    if num > 8:
                        self.files_frame.update_idletasks()
                        self.win.update_idletasks()
                    canvas_width = self.canvas.winfo_width() or 340
                    self.canvas.itemconfig(self.files_frame_id, width=canvas_width)
                except tk.TclError:
                    pass
        # Gestione dinamica dei binding della rotella
        if hasattr(self, '_bind_mousewheel') and hasattr(self, '_unbind_mousewheel'):
            if num >= 9:
                self._bind_mousewheel()
            else:
                self._unbind_mousewheel()
