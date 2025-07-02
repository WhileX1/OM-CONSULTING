import tkinter as tk
from tkinter import filedialog, messagebox as mb
from gitrepo import GitRepo, subprocess
from widgets import FileSelectionWindow
from config import *
import time
import os, sys, atexit
import ctypes
import threading
from helpers import (save_last_dir, load_last_dir, create_scrollable_list,
                     count_selected_files, update_counter_var, get_subprocess_kwargs,
                     show_warning, show_info, show_error)

class GitGuiApp(tk.Tk):
    def reset_content_area(self):
        # Centralized removal of dynamic widgets from main_container except dir_label and button_frame.
        self.clear_dynamic_widgets(self.main_container, static_widgets=[self.dir_label, self.button_frame])
        # Ricrea content_frame se necessario.
        if not hasattr(self, 'content_frame') or not self.content_frame.winfo_exists():
            self.content_frame = tk.Frame(self.main_container)
            self.content_frame.pack(fill="both", expand=True)
        else:
            self.clear_dynamic_widgets(self.content_frame)

    @staticmethod
    def clear_dynamic_widgets(container, static_widgets=None):
        # Distrugge tutti i widget figli di container eccetto quelli in static_widgets.
        if static_widgets is None:
            static_widgets = []
        for widget in list(container.winfo_children()):
            if widget not in static_widgets:
                try:
                    widget.destroy()
                except Exception:
                    pass

    def validate_branch(self, branch):
        # Controlla se il branch è valido. Non mostra più warning personalizzati, lascia a git l'errore.
        return branch in self.branch_info


    def get_valid_files(self, files):
        # Restituisce solo i file validi. Lascia a git la gestione degli errori.
        valid = [f for f in files if f and f.strip()]
        return valid


    def validate_commit_message(self, msg):
        # Controlla se il messaggio di commit è valido. Lascia a git la gestione degli errori.
        return bool(msg)

    # --- Caching ottimizzato per ridurre chiamate frequenti ---
    _cache_timeout = 30.0  # Aumentato da 2 a 30 secondi
    _cached_branch = None
    _cached_origin = None
    _cached_github_user = None
    _cached_is_repo = None
    _cache_time = 0
    _github_user_needs_update = False  # Flag per aggiornamento utente GitHub
    _branches_fetched_on_startup = False  # Flag per evitare fetch multipli
    _login_in_progress = False  # Flag per indicare login in corso
    
    # set_placeholder e clear_placeholder ora sono in helpers.py
    def __init__(self):
        super().__init__()
        last_dir = load_last_dir()
        if last_dir:
            try:
                os.chdir(last_dir)
            except Exception:
                pass
        self.title("Git Bash Automatico")
        self.geometry("500x375")
        self.resizable(False, False)
        # Variabili persistenti per schermata push
        self._push_files = []
        self._push_num_var = tk.IntVar(value=1)
        self._push_remote_var = tk.StringVar()
        self._push_commit_msg = ""
        # Finestra selezione file (per evitare doppioni)
        self._file_selection_window = None
        # Mappa branch -> tipo (remoto, locale, entrambi)
        self._branch_info = {}
        # Persistent layout
        MAIN_PAD = 30
        self.main_container = tk.Frame(self)
        self.main_container.pack(fill="both", expand=True, padx=MAIN_PAD, pady=(0, MAIN_PAD))
        self.dir_label = tk.Label(self.main_container, text="", font=("Segoe UI", 10, "bold"), justify="left", anchor="w")
        self.dir_label.pack(pady=(15, 0), fill="x")
        self.button_frame = None
        self.content_frame = tk.Frame(self.main_container)
        self.content_frame.pack(fill="both", expand=True)
        # Aggiorna i branch solo una volta all'avvio per rimuovere quelli eliminati dal remoto
        self._update_branch_info(prune=True)
        self._branches_fetched_on_startup = True
        self.create_buttons()
        self.update_dir_label()
        self.check_repo()

    @property
    def file_selection_window(self):
        return self._file_selection_window

    @file_selection_window.setter
    def file_selection_window(self, value):
        self._file_selection_window = value

    @property
    def branch_info(self):
        return self._branch_info

    @branch_info.setter
    def branch_info(self, value):
        self._branch_info = value

    def _clear_content_frame_widgets(self):
        # Usa la funzione centralizzata
        self.reset_content_area()

    def _init_main_ui(self):
        # Usa la funzione centralizzata per pulire e ricreare l'area dinamica
        self.reset_content_area()
        if not self.button_frame or not self.button_frame.winfo_exists():
            self.create_buttons()
        self.update_dir_label()
        self.check_repo()

    def _update_branch_info(self, prune=False):
        # Recupera branch locali e remoti e costruisce la mappa branch -> tipo
        try:
            if prune:
                try:
                    kwargs = get_subprocess_kwargs()
                    subprocess.check_output(['git', 'fetch', '--prune'], text=True, **kwargs)
                except Exception:
                    pass
            local = set(GitRepo.get_local_branches())
            remote = set(GitRepo.get_remote_branches())
            all_branches = local | remote
            info = {}
            for b in all_branches:
                if b in local and b in remote:
                    info[b] = "(locale/remoto)"
                elif b in local:
                    info[b] = "(locale)"
                elif b in remote:
                    info[b] = "(remoto)"
            self._branch_info = info
        except Exception:
            self._branch_info = {}

    def create_buttons(self):
        BUTTON_PAD_INNER = 8
        button_frame = tk.Frame(self.main_container)
        button_frame.pack(side="bottom", fill="x")
        btn_opts = dict(width=20, height=2, font=("Segoe UI", 10, "bold"))
        row1 = tk.Frame(button_frame)
        row1.pack(fill="x", pady=0)
        self.btn_pull = tk.Button(row1, text="Pull", command=self.do_pull, **btn_opts)
        self.btn_pull.pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
        self.btn_push = tk.Button(row1, text="Push", command=self.do_push, **btn_opts)
        self.btn_push.pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
        row2 = tk.Frame(button_frame)
        row2.pack(fill="x", pady=0)
        self.btn_branch = tk.Button(row2, text="Cambia branch", command=self.do_branch, **btn_opts)
        self.btn_branch.pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
        self.btn_dir = tk.Button(row2, text="Cambia directory", command=self.change_directory, **btn_opts)
        self.btn_dir.pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
        
        # Nuova riga per il pulsante account
        row3 = tk.Frame(button_frame)
        row3.pack(fill="x", pady=0)
        self.btn_account = tk.Button(row3, text="Cambia account", command=self.do_account, **btn_opts)
        self.btn_account.pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
        # Pulsante disabilitato per mantenere lo stile
        tk.Button(row3, state="disabled", **btn_opts).pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
        
        # Riempi la riga successiva con pulsanti disabilitati per mantenere lo stile
        for _ in range(1):
            row = tk.Frame(button_frame)
            row.pack(fill="x", pady=0)
            tk.Button(row, state="disabled", **btn_opts).pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
            tk.Button(row, state="disabled", **btn_opts).pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
        self.button_frame = button_frame

    def update_dir_label(self, force_refresh=False):
        now = time.time()
        cache_expired = (now - self._cache_time > self._cache_timeout)
        
        # Aggiorna branch e origin solo se necessario
        if force_refresh or cache_expired or self._cached_branch is None or self._cached_origin is None:
            self._cached_branch = GitRepo.get_current_branch()
            self._cached_origin = GitRepo.get_current_origin()
            self._cache_time = now
        
        # Aggiorna utente GitHub solo se richiesto esplicitamente
        if self._github_user_needs_update or self._cached_github_user is None:
            self._cached_github_user = GitRepo.get_github_user()
            self._github_user_needs_update = False
        
        branch = self._cached_branch
        origin = self._cached_origin
        github_user = self._cached_github_user
        cwd = os.getcwd()
        self.dir_label.config(text=f"📁 Directory: {cwd}\n ➥ Branch: {branch}\n🔍 Origine: {origin}\n 👤 GitHub: {github_user}")

    def invalidate_cache(self):
        """Invalida la cache per branch e origin (non per utente GitHub)"""
        self._cached_branch = None
        self._cached_origin = None
        self._cached_is_repo = None
        self._cache_time = 0

    def invalidate_github_user_cache(self):
        """Invalida solo la cache dell'utente GitHub"""
        self._cached_github_user = None
        self._github_user_needs_update = True

    @staticmethod
    def is_valid_branch(branch, branches):
        return branch in branches

    def check_repo(self, force_refresh=False):
        now = time.time()
        if force_refresh or (self._cached_is_repo is None) or (now - self._cache_time > self._cache_timeout):
            self._cached_is_repo = GitRepo.is_valid_repo()
            self._cache_time = now
        if not self._cached_is_repo:
            self.btn_pull.config(state="disabled")
            self.btn_push.config(state="disabled")
            self.btn_branch.config(state="disabled")
            show_warning("Attenzione", "La directory corrente non è una repository git valida.")
        else:
            self.btn_pull.config(state="normal")
            self.btn_push.config(state="normal")
            self.btn_branch.config(state="normal")

    def do_pull(self):
        # Non aggiornare la lista branch all'apertura della sezione Pull
        self._show_branch_section(
            title="Seleziona o filtra il branch da cui fare Pull:",
            action_btn_text="Esegui Pull",
            action_callback=self._do_pull_action,
            extra_widgets=lambda bottom_frame, entry_var, action_callback: self._common_extra_widgets(
                bottom_frame=bottom_frame,
                entry_var=entry_var,
                action_callback=action_callback,
                action_text="Esegui Pull",
                show_force=True
            ),
            show_delete_branch=False
        )
    def _do_pull_action(self, branch, force_var=None):
        if not self.validate_branch(branch):
            return
        force = force_var.get() if force_var is not None else False
        if force:
            ok, msg = GitRepo.pull_force(branch)
        else:
            ok, msg = GitRepo.pull(branch)
        if ok:
            self.invalidate_cache()
            self.update_dir_label(force_refresh=True)
            if msg and "already up to date" in msg.lower():
                show_info("Pull Output", "Branch locale allineato con il branch remoto.")
            else:
                show_info("Pull Output", msg)
        else:
            show_error("Errore Pull", msg)

    def do_push(self):
        self.clear_content_frame()
        self.button_frame.pack_forget()
        branch_row = tk.Frame(self.main_container)
        branch_row.pack(pady=16, anchor="center", fill="x")
        tk.Label(branch_row, text="Branch remoto:", font=("Segoe UI", 10, "bold"), anchor="w").grid(row=0, column=0, padx=(0, 8), sticky="w")
        remote_var = self._push_remote_var
        current_branch = self._cached_branch if self._cached_branch else GitRepo.get_current_branch()
        if current_branch and current_branch != "(nessun commit)" and current_branch != "(nessun branch)":
            if not remote_var.get() or remote_var.get() != current_branch:
                remote_var.set(current_branch)
        remote_entry = tk.Entry(branch_row, textvariable=remote_var, font=("Segoe UI", 10, "bold"), width=22)
        remote_entry.grid(row=0, column=1)
        files = self._push_files
        num_var = self._push_num_var

        def get_selected_count():
            return count_selected_files(files)
        file_counter_var = tk.StringVar()

        def update_file_counter(*args):
            update_counter_var(file_counter_var, get_selected_count, num_var)
        update_file_counter()
        num_var.trace_add("write", update_file_counter)

        def after_files_saved():
            update_file_counter()
        btn_select_file = tk.Button(
            branch_row,
            text="Seleziona file",
            command=lambda: self.ensure_file_selection_window(files, num_var, after_files_saved),
            font=("Segoe UI", 10, "bold")
        )
        btn_select_file.grid(row=0, column=2, padx=(16,0))
        tk.Label(branch_row, textvariable=file_counter_var, font=("Segoe UI", 10, "bold"), width=6, anchor="center").grid(row=0, column=3, padx=(6,0))
        
        def periodic_update():
            update_file_counter()
            self.after(200, periodic_update)
        periodic_update()
        tk.Label(self.main_container, text="Messaggio di commit:", font=("Segoe UI", 10, "bold")).pack(pady=5)
        commit_text = tk.Text(self.main_container, height=5, width=60, font=("Segoe UI", 10, "bold"))
        commit_text.pack(pady=5)
        if self._push_commit_msg:
            commit_text.delete("1.0", "end")
            commit_text.insert("1.0", self._push_commit_msg)
        
        bottom_frame = tk.Frame(self.main_container)
        bottom_frame.pack(side="bottom", fill="x", pady=10)
        
        # Aggiungi il checkbox Force push nella stessa riga dei pulsanti
        force_var = tk.BooleanVar(value=False)
        
        def do_push_action():
            selected_files = self.get_valid_files(files)
            if selected_files is None:
                return
            branch_name = remote_var.get().strip()
            if not branch_name:
                show_error("Errore", "Il campo 'Branch remoto' è vuoto. Specifica un branch remoto.")
                return
            msg = commit_text.get("1.0", "end").strip()
            if not self.validate_commit_message(msg):
                return
            self._push_commit_msg = msg
            
            # Ottieni il valore del checkbox force
            force_push = force_var.get()
            
            unchanged_files = []
            changed_files = []
            # Espandi cartelle in lista file
            def expand_dirs(paths):
                result = []
                for p in paths:
                    if os.path.isdir(p):
                        for root, dirs, files in os.walk(p):
                            for file in files:
                                result.append(os.path.join(root, file))
                    else:
                        result.append(p)
                return result

            expanded_files = expand_dirs(selected_files)
            try:
                kwargs = get_subprocess_kwargs()
                status_lines = subprocess.check_output(
                    ['git', 'status', '--porcelain'], text=True, **kwargs
                ).splitlines()
                abs_selected = [os.path.abspath(f) for f in expanded_files]
                repo_root = subprocess.check_output(
                    ['git', 'rev-parse', '--show-toplevel'], text=True, **kwargs
                ).strip()
                rel_selected = [os.path.relpath(f, repo_root).replace('\\', '/') for f in abs_selected]
                
                # Ottieni lista dei file tracciati da git
                try:
                    tracked_files = subprocess.check_output(
                        ['git', 'ls-tree', '-r', '--name-only', 'HEAD'], text=True, **kwargs
                    ).splitlines()
                    tracked_files = [f.replace('\\', '/') for f in tracked_files]
                except subprocess.CalledProcessError:
                    # Se non ci sono commit, non ci sono file tracciati
                    tracked_files = []
                
                for idx, f in enumerate(expanded_files):
                    rel_path = rel_selected[idx]
                    found_in_status = False
                    
                    # Controlla se il file è modificato secondo git status
                    for line in status_lines:
                        line_path = line[3:] if len(line) > 3 else line.strip()
                        if os.path.basename(f) == os.path.basename(line_path) or \
                           line_path.replace('\\', '/') == rel_path:
                            found_in_status = True
                            break
                    
                    # Se il file è modificato O non è tracciato, consideralo come "changed"
                    if found_in_status or rel_path not in tracked_files:
                        changed_files.append(f)
                    else:
                        unchanged_files.append(f)
            except Exception:
                changed_files = expanded_files
                unchanged_files = []
            if not changed_files:
                show_info("Nessuna modifica", "File {} senza modifiche".format(", ".join(os.path.basename(f) for f in unchanged_files)))
                return
            
            # Passa il parametro force al metodo push
            ok, push_msg = GitRepo.push(changed_files, branch_name, msg, force=force_push)
            if ok:
                self.invalidate_cache()
                show_info("Successo", f"Push eseguito con successo al branch {branch_name}")
            else:
                show_error("Errore Push", push_msg)
            self.update_dir_label(force_refresh=True)
        
        def on_back():
            self._push_commit_msg = commit_text.get("1.0", "end").strip()
            self.show_menu()
        
        tk.Button(bottom_frame, text="Indietro", command=on_back, font=("Segoe UI", 10, "bold")).pack(side="left", padx=10)
        
        # Checkbox Force push al centro
        force_chk = tk.Checkbutton(
            bottom_frame, 
            text="Force push", 
            variable=force_var,
            font=("Segoe UI", 10, "bold")
        )
        force_chk.pack(side="left", expand=True, padx=10)
        
        tk.Button(bottom_frame, text="Esegui Push", command=do_push_action, font=("Segoe UI", 10, "bold")).pack(side="right", padx=10)

    def ensure_file_selection_window(self, files, num_var, after_files_saved):
        # Gestione DRY della finestra di selezione file: solleva se già esiste, crea se non esiste o è stata chiusa.
        win = self.file_selection_window
        if win is not None:
            try:
                if not win.win.winfo_exists():
                    self.file_selection_window = None
            except Exception:
                self.file_selection_window = None
        if self.file_selection_window is not None:
            try:
                self.file_selection_window.win.lift()
                self.file_selection_window.win.focus_force()
            except Exception:
                self.file_selection_window = None
            return
        self.file_selection_window = FileSelectionWindow(self, files, num_var, after_files_saved, app_ref=self)
        # Nessun codice UI qui: solo gestione della finestra di selezione file

    def do_branch(self):
        # Non aggiornare la lista branch all'apertura della sezione Branch
        self._show_branch_section(
            title="Seleziona o filtra il branch:",
            action_btn_text="Cambia branch",
            action_callback=self._do_checkout_action,
            extra_widgets=lambda bottom_frame, entry_var, action_callback: self._common_extra_widgets(
                bottom_frame=bottom_frame,
                entry_var=entry_var,
                action_callback=action_callback,
                action_text="Cambia branch",
                show_force=False
            ),
            show_delete_branch=True
        )

    def _do_checkout_action(self, branch, _force_var=None):
        # Se il branch non esiste, mostra solo la domanda di creazione, senza doppio avviso
        if branch not in self.branch_info:
            res = mb.askyesno(
                "Branch non trovato",
                f"Il branch '{branch}' non esiste.\n\nVuoi creare un nuovo branch con questo nome?"
            )
            if res:
                ok, msg = GitRepo.create_and_checkout(branch)
                if ok:
                    self.invalidate_cache()
                    self._update_branch_info(prune=False)  # Aggiorna la lista dei branch
                    self.update_dir_label(force_refresh=True)
                    self.check_repo(force_refresh=True)
                    show_info("Nuovo branch", f"{msg}")
                    # Aggiorna la UI della lista branch in tempo reale
                    self._show_branch_section(
                        title="Seleziona o filtra il branch:",
                        action_btn_text="Cambia branch",
                        action_callback=self._do_checkout_action,
                        extra_widgets=lambda bottom_frame, entry_var, action_callback: self._common_extra_widgets(
                            bottom_frame=bottom_frame,
                            entry_var=entry_var,
                            action_callback=action_callback,
                            action_text="Cambia branch",
                            show_force=False
                        ),
                        show_delete_branch=True
                    )
                else:
                    show_error("Errore creazione branch", msg)
            return
        ok, msg = GitRepo.checkout(branch)
        if ok:
            self.invalidate_cache()
            self.update_dir_label(force_refresh=True)
            self.check_repo(force_refresh=True)
            show_info("Cambio branch", msg)
        else:
            show_error("Errore cambio branch", msg)

    def _common_extra_widgets(self, bottom_frame, entry_var, action_callback, action_text, show_force=False):
        # Crea solo widget extra (es. force checkbox), NON pulsanti di azione.
        # Ritorna la funzione di conferma da collegare all'evento <Return>.
        force_var = tk.BooleanVar(value=False) if show_force else None
        if show_force:
            force_chk = tk.Checkbutton(
                bottom_frame, text="Force pull", variable=force_var,
                font=("Segoe UI", 10, "bold"), anchor="center"
            )
            force_chk.pack(side="left", expand=True, padx=10)
        def on_confirm():
            branch = entry_var.get().strip()
            if show_force:
                action_callback(branch, force_var)
            else:
                action_callback(branch)
        return on_confirm

    def _show_branch_section(self, title, action_btn_text, action_callback, extra_widgets, show_delete_branch=False):
        # Centralized UI for branch selection (used by Pull and Branch)
        self.clear_content_frame()
        self.button_frame.pack_forget()
        tk.Label(self.main_container, text=title, font=("Segoe UI", 10, "bold")).pack(pady=5)
        branches = list(self.branch_info.keys())
        entry_var = tk.StringVar()
        entry = tk.Entry(self.main_container, textvariable=entry_var, font=("Segoe UI", 10, "bold"))
        entry.pack(pady=3, padx=10, fill="x")
        get_count = lambda: len(branches)
        sugg_container, canvas, btn_frame, update_mousewheel = create_scrollable_list(
            self.main_container, height=130, threshold=5, item_count_func=get_count, parent_win=self
        )
        sugg_container.pack(pady=(10,0), padx=10, fill="x")

        def on_suggestion_click(branch):
            entry_var.set(branch)
            entry.focus()
            entry.icursor(tk.END)
            entry.selection_range(0, tk.END)

        def update_buttons(*args):
            for widget in btn_frame.winfo_children():
                widget.destroy()
            filtro = entry_var.get().lower()
            found = False
            for branch in branches:
                if filtro in branch.lower():
                    label = branch
                    if branch in self.branch_info:
                        label += f" {self.branch_info[branch]}"
                    b = tk.Button(btn_frame, text=label, width=32, anchor="w", font=("Segoe UI", 10, "bold"),
                                  command=lambda br=branch: on_suggestion_click(br))
                    b.pack(pady=1, fill="x")
                    found = True
            if not found:
                tk.Label(btn_frame, text="Nessun branch trovato.", font=("Segoe UI", 10, "bold")).pack()
        entry_var.trace_add("write", update_buttons)
        update_buttons()

        # Rebind mousewheel if branch count changes (filtering)
        def on_update_mousewheel(*args):
            update_mousewheel()
        entry_var.trace_add("write", lambda *a: on_update_mousewheel())

        bottom_frame = tk.Frame(self.main_container)
        bottom_frame.pack(side="bottom", fill="x", pady=10)

        # --- Pulsante Elimina branch ---
        def on_delete_branch():
            branch = entry_var.get().strip()
            if not branch:
                show_error("Errore", "Nessun branch selezionato.")
                return
            # Solo branch locale
            local_branches = GitRepo.get_local_branches()
            if branch not in local_branches:
                show_error("Errore", f"Il branch '{branch}' non esiste tra i branch locali.")
                return
            if branch == GitRepo.get_current_branch():
                show_error("Errore", "Non puoi eliminare il branch attualmente attivo.")
                return
            res = mb.askyesno("Conferma eliminazione", f"Vuoi eliminare il branch locale '{branch}'?\nQuesta azione non è reversibile.")
            if not res:
                return
            ok, msg = GitRepo.delete_local_branch(branch)
            if ok:
                self.invalidate_cache()
                # Non fare prune qui, solo aggiorna la lista branch senza fetch
                self._update_branch_info(prune=False)
                self.update_dir_label(force_refresh=True)
                show_info("Branch eliminato", msg)
                entry_var.set("")
                update_buttons()
                # Aggiorna la sezione branch dopo eliminazione
                self._show_branch_section(
                    title="Seleziona o filtra il branch:",
                    action_btn_text="Cambia branch",
                    action_callback=self._do_checkout_action,
                    extra_widgets=lambda bottom_frame, entry_var, action_callback: self._common_extra_widgets(
                        bottom_frame=bottom_frame,
                        entry_var=entry_var,
                        action_callback=action_callback,
                        action_text="Cambia branch",
                        show_force=False
                    ),
                    show_delete_branch=True
                )
            else:
                show_error("Errore eliminazione branch", msg)

        # --- Pulsanti azione branch: Indietro | Cambia branch | (opzionale) Elimina branch ---
        for widget in bottom_frame.winfo_children():
            widget.destroy()

        # Indietro
        btn_back = tk.Button(bottom_frame, text="Indietro", command=self.show_menu, font=("Segoe UI", 10, "bold"))
        btn_back.pack(side="left", padx=10)

        # Cambia branch
        def on_confirm():
            branch = entry_var.get().strip()
            action_callback(branch)
        btn_change = tk.Button(bottom_frame, text=action_btn_text, command=on_confirm, font=("Segoe UI", 10, "bold"))
        btn_change.pack(side="right", padx=10)

        # Elimina branch SOLO se richiesto
        if show_delete_branch:
            btn_delete = tk.Button(bottom_frame, text="Elimina branch", command=on_delete_branch, font=("Segoe UI", 10, "bold"), fg="red")
            btn_delete.pack(side="right", padx=10)

        # Setup extra widgets (e.g., force checkbox for pull) SOLO se non sono già presenti
        # (Evita duplicazione: extra_widgets non deve aggiungere altri pulsanti azione)
        # Se extra_widgets aggiunge solo widget "extra" (es. force), va bene:
        on_confirm_extra = extra_widgets(bottom_frame, entry_var, action_callback)
        # Se extra_widgets restituisce una funzione di conferma, usala per <Return>
        entry.bind("<Return>", lambda event: (on_confirm_extra() if on_confirm_extra else on_confirm()))
        entry.focus()
        if self.main_container.winfo_toplevel() is not self:
            self.main_container.winfo_toplevel().resizable(False, False)

        # Centralized cleanup: destroy scrollable widgets and unbind mousewheel on section change
        def cleanup():
            try:
                update_mousewheel()  # ensure unbinding if needed
            except Exception:
                pass
            try:
                sugg_container.destroy()
            except Exception:
                pass
            try:
                bottom_frame.destroy()
            except Exception:
                pass
        self._current_section_cleanup = cleanup

    def clear_content_frame(self):
        # Centralized cleanup for scrollable/mousewheel widgets
        if hasattr(self, '_current_section_cleanup') and self._current_section_cleanup:
            try:
                self._current_section_cleanup()
            except Exception:
                pass
            self._current_section_cleanup = None
        # Usa la funzione centralizzata per pulire l'area dinamica
        self.reset_content_area()

    def show_menu(self):
        # Mostra la schermata principale senza distruggere main_container
        self.clear_content_frame()
        # Assicurati che i pulsanti siano visibili
        if self.button_frame and not self.button_frame.winfo_ismapped():
            self.button_frame.pack(side="bottom", fill="x")
        self._init_main_ui()
        # Porta la finestra principale in primo piano e forza il focus
        def bring_to_front():
            try:
                self.deiconify()
                self.lift()
                self.focus_force()
            except Exception:
                pass
        self.after_idle(bring_to_front)

    # open_files_window rimane come unico punto di gestione della finestra file
    def open_files_window(self, files, num_var, update_counter):
        # Gestione unificata della finestra di selezione file
        def on_files_saved():
            if update_counter:
                update_counter()

        # Chiudi la finestra precedente se esiste
        if self.file_selection_window is not None:
            try:
                if self.file_selection_window.win.winfo_exists():
                    self.file_selection_window.win.destroy()
            except Exception:
                pass
            self.file_selection_window = None

        self.file_selection_window = FileSelectionWindow(self, files, num_var, on_files_saved, app_ref=self)

    # _build_files_frame eliminata: la gestione della selezione file è ora centralizzata in FileSelectionWindow

    def change_directory(self):
        new_dir = filedialog.askdirectory(title="Seleziona nuova directory di lavoro")
        if new_dir:
            try:
                os.chdir(new_dir)
                save_last_dir(new_dir)
                # Invalida TUTTA la cache dopo cambio directory
                self.invalidate_cache()
                self.invalidate_github_user_cache()  # Potrebbe cambiare anche l'utente GitHub
                self.update_dir_label(force_refresh=True)
                self.check_repo(force_refresh=True)
                show_info("Cambio directory", f"Directory cambiata in:\n{os.getcwd()}")
            except Exception as e:
                show_error("Errore", f"Impossibile cambiare directory:\n{e}")

    def do_account(self):
        # Mostra la schermata di gestione account con pulsanti Login e Logout
        self.clear_content_frame()
        self.button_frame.pack_forget()
        
        # Titolo della sezione
        tk.Label(self.main_container, text="Gestione Account GitHub:", font=("Segoe UI", 12, "bold")).pack(pady=10)
        
        # Frame centrale per i pulsanti principali (stesso stile del menu)
        action_frame = tk.Frame(self.main_container)
        action_frame.pack(expand=True, fill="both", pady=10)
        
        # Usa lo stesso stile del menu principale
        BUTTON_PAD_INNER = 8
        btn_opts = dict(width=20, height=2, font=("Segoe UI", 10, "bold"))
        
        # Frame per la riga dei pulsanti (stesso stile di row1, row2 del menu)
        button_row = tk.Frame(action_frame)
        button_row.pack(fill="x", pady=0)
        
        # Pulsante LOGIN (senza colore)
        self.btn_login = tk.Button(button_row, text="Login", command=self._do_login, **btn_opts)
        self.btn_login.pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
        
        # Pulsante LOGOUT (senza colore)
        self.btn_logout = tk.Button(button_row, text="Logout", command=self._do_logout, **btn_opts)
        self.btn_logout.pack(side="left", expand=True, fill="x", pady=6, padx=BUTTON_PAD_INNER)
        
        # Frame per il pulsante indietro
        bottom_frame = tk.Frame(self.main_container)
        bottom_frame.pack(side="bottom", fill="x", pady=10)
        
        tk.Button(bottom_frame, text="Indietro", command=self.show_menu, 
                 font=("Segoe UI", 10, "bold")).pack(side="left", padx=10)

    def _do_login(self):
        """Esegue il login a GitHub in modalità non bloccante"""
        try:
            # Controlla se c'è già un login in corso
            if self._login_in_progress:
                show_info("Login in corso", "C'è già un processo di login in corso. Attendi che si completi.")
                return
            
            # Informa l'utente che si aprirà una console
            result = mb.askyesno("Login GitHub", 
                               "Si aprirà una finestra del terminale per completare il login.\n"
                               "L'app rimarrà utilizzabile durante il processo.\n\n"
                               "Vuoi continuare?")
            if not result:
                return
            
            # Avvia il login in un thread separato
            self._login_in_progress = True
            self._update_login_button_state()
            
            def login_thread():
                try:
                    # Usa GitHub CLI per il login - MOSTRA la console per l'interazione
                    result = subprocess.run(
                        ['gh', 'auth', 'login'],
                        capture_output=False,
                        text=True
                        # Rimuovo CREATE_NO_WINDOW per permettere l'interazione
                    )
                    
                    # Pianifica l'aggiornamento dell'UI nel thread principale
                    def update_ui():
                        self._login_in_progress = False
                        self._update_login_button_state()
                        
                        if result.returncode == 0:
                            # Aggiorna SOLO l'utente GitHub dopo il login
                            self.invalidate_github_user_cache()
                            self.update_dir_label()
                            show_info("Login", "Login a GitHub eseguito con successo!")
                        else:
                            show_error("Errore Login", "Errore durante il login. Assicurati di avere GitHub CLI installato.")
                    
                    # Esegue l'aggiornamento UI nel thread principale
                    self.after(0, update_ui)
                    
                except FileNotFoundError:
                    def show_error_ui():
                        self._login_in_progress = False
                        self._update_login_button_state()
                        show_error("GitHub CLI non trovato", 
                                  "GitHub CLI non è installato o non è nel PATH.\n"
                                  "Scaricalo da: https://cli.github.com/")
                    self.after(0, show_error_ui)
                    
                except Exception as e:
                    def show_error_ui():
                        self._login_in_progress = False
                        self._update_login_button_state()
                        show_error("Errore", f"Errore durante il login: {str(e)}")
                    self.after(0, show_error_ui)
            
            # Avvia il thread del login
            thread = threading.Thread(target=login_thread, daemon=True)
            thread.start()
            
        except Exception as e:
            self._login_in_progress = False
            self._update_login_button_state()
            show_error("Errore", f"Errore durante l'avvio del login: {str(e)}")

    def _update_login_button_state(self):
        """Aggiorna lo stato del pulsante login in base al processo in corso"""
        if hasattr(self, 'btn_login') and self.btn_login.winfo_exists():
            if self._login_in_progress:
                self.btn_login.config(text="Login in corso...", state="disabled")
            else:
                self.btn_login.config(text="Login", state="normal")

    def _do_logout(self):
        """Esegue il logout da GitHub"""
        try:
            # Conferma logout
            result = mb.askyesno("Conferma Logout", 
                               "Sei sicuro di voler effettuare il logout da GitHub?")
            if not result:
                return
            
            # Prima ottieni l'utente corrente
            current_user = GitRepo.get_github_user()
            if current_user == "(non autenticato)" or current_user == "(GitHub CLI non installato)" or current_user == "(errore)":
                show_info("Logout", "Non sei attualmente autenticato su GitHub.")
                return
            
            # Prova diversi approcci per il logout
            logout_success = False
            error_messages = []
            
            # Primo tentativo: logout con utente specifico
            try:
                result = subprocess.run(
                    ['gh', 'auth', 'logout', '--hostname', 'github.com', '--user', current_user],
                    capture_output=True,
                    text=True,
                    input='y\n',
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
                if result.returncode == 0:
                    logout_success = True
                else:
                    error_messages.append(f"Logout con utente: {result.stderr}")
            except Exception as e:
                error_messages.append(f"Errore logout con utente: {str(e)}")
            
            # Secondo tentativo: logout generico se il primo fallisce
            if not logout_success:
                try:
                    result = subprocess.run(
                        ['gh', 'auth', 'logout', '--hostname', 'github.com'],
                        capture_output=True,
                        text=True,
                        input='y\n',
                        creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                    )
                    if result.returncode == 0:
                        logout_success = True
                    else:
                        error_messages.append(f"Logout generico: {result.stderr}")
                except Exception as e:
                    error_messages.append(f"Errore logout generico: {str(e)}")
            
            # Terzo tentativo: logout forzato se gli altri falliscono
            if not logout_success:
                try:
                    result = subprocess.run(
                        ['gh', 'auth', 'logout', '--hostname', 'github.com', '--force'],
                        capture_output=True,
                        text=True,
                        creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                    )
                    if result.returncode == 0:
                        logout_success = True
                    else:
                        error_messages.append(f"Logout forzato: {result.stderr}")
                except Exception as e:
                    error_messages.append(f"Errore logout forzato: {str(e)}")
            
            if logout_success:
                # Aggiorna SOLO l'utente GitHub dopo il logout
                self.invalidate_github_user_cache()
                self.update_dir_label()
                show_info("Logout", "Logout da GitHub eseguito con successo!")
            else:
                show_error("Errore Logout", f"Tutti i tentativi di logout sono falliti:\n" + "\n".join(error_messages))
                
        except FileNotFoundError:
            show_error("GitHub CLI non trovato", 
                      "GitHub CLI non è installato o non è nel PATH.")
        except Exception as e:
            show_error("Errore", f"Errore durante il logout: {str(e)}")

if __name__ == "__main__":
    lockfile = os.path.join(os.getenv('TEMP') or '.', 'gitbash_auto.lock')


    def is_pid_running(pid):
        try:
            if pid <= 0:
                return False
            if os.name == 'nt':
                PROCESS_QUERY_INFORMATION = 0x0400
                process = ctypes.windll.kernel32.OpenProcess(PROCESS_QUERY_INFORMATION, 0, pid)
                if process != 0:
                    ctypes.windll.kernel32.CloseHandle(process)
                    return True
                else:
                    return False
            else:
                os.kill(pid, 0)
                return True
        except Exception:
            return False


    def check_gui_visible(app):
        # Controlla se la GUI Tkinter è visibile per questa istanza
        # Dopo la creazione, la finestra principale potrebbe non essere ancora "mapped".
        # Forziamo l'update e controlliamo se è visibile.
        app.update_idletasks()
        app.update()
        return app.winfo_exists() and (app.state() != 'withdrawn')


    # Kill any previous process of this app (if running)
    if os.path.exists(lockfile):
        try:
            with open(lockfile, 'r') as f:
                pid_str = f.read().strip()
                pid = int(pid_str) if pid_str.isdigit() else None
        except Exception:
            pid = None
        if pid and pid != os.getpid():
            try:
                if os.name == 'nt':
                    import ctypes
                    PROCESS_TERMINATE = 0x0001
                    handle = ctypes.windll.kernel32.OpenProcess(PROCESS_TERMINATE, 0, pid)
                    if handle:
                        ctypes.windll.kernel32.TerminateProcess(handle, -1)
                        ctypes.windll.kernel32.CloseHandle(handle)
                else:
                    os.kill(pid, 9)
            except Exception:
                pass
        try:
            os.remove(lockfile)
        except Exception:
            pass

    with open(lockfile, 'w') as f:
        f.write(str(os.getpid()))

    def remove_lock():
        try:
            if os.path.exists(lockfile):
                os.remove(lockfile)
        except Exception:
            pass
    atexit.register(remove_lock)

    try:
        # Controllo ambiente Tkinter
        try:
            import tkinter
            root = tkinter.Tk()
            root.withdraw()
            root.update()
            root.destroy()
        except Exception as tkerr:
            try:
                mb.showerror("Errore ambiente Tkinter", f"Tkinter non funziona correttamente:\n{tkerr}")
            except Exception:
                pass
            remove_lock()
            sys.exit(1)

        app = GitGuiApp()
        if not check_gui_visible(app):
            try:
                mb.showerror("Errore GUI", "La finestra principale non è visibile.\nControlla che non ci siano errori di Tkinter o di ambiente.")
            except Exception:
                pass
            remove_lock()
            sys.exit(1)
        app.mainloop()
    except Exception as e:
        try:
            mb.showerror("Errore avvio app", f"Errore durante l'avvio dell'app:\n{e}")
        except Exception:
            pass
        raise
    finally:
        remove_lock()