import tkinter as tk
from tkinter import ttk

class ProgressWindow:

    def __init__(self, parent, title="Caricamento...", max_value=100):
        self.top = tk.Toplevel(parent)
        self.top.title(title)
        self.top.geometry("420x240")
        self.top.resizable(False, False)
        self.top.configure(bg="#f8f8f8")
        self.top.transient(parent)
        self.top.grab_set()
        self.top.protocol("WM_DELETE_WINDOW", lambda: None)  # Disabilita chiusura

        tk.Label(self.top, text=title, font=("Segoe UI", 11, "bold"), bg="#f8f8f8").pack(pady=(10, 5))

        # Barra verde: espansione file
        self.progress_files = ttk.Progressbar(self.top, orient="horizontal", length=380, mode="determinate", maximum=max_value)
        self.progress_files.pack(pady=(5, 2), padx=20)
        self.progress_files_label = tk.Label(self.top, text="Espansione file/cartelle", font=("Segoe UI", 9), fg="#228B22", bg="#f8f8f8")
        self.progress_files_label.pack()

        # Barra blu: commit/push
        self.progress_push = ttk.Progressbar(self.top, orient="horizontal", length=380, mode="determinate", maximum=3)
        self.progress_push.pack(pady=(8, 2), padx=20)
        self.progress_push_label = tk.Label(self.top, text="Commit & Push", font=("Segoe UI", 9), fg="#0057b7", bg="#f8f8f8")
        self.progress_push_label.pack()

        self.log_text = tk.Text(self.top, height=6, width=52, font=("Consolas", 9), state="disabled", bg="#f8f8f8")
        self.log_text.pack(padx=10, pady=(5, 10), fill="both", expand=True)
        self._cancelled = False

    def set_max(self, max_value):
        self.progress_files["maximum"] = max_value

    def update_progress_files(self, value):
        self.progress_files["value"] = value
        self.top.update_idletasks()

    def update_progress_push(self, value):
        self.progress_push["value"] = value
        self.top.update_idletasks()

    def set_push_mode(self, mode):
        # mode: "determinate" or "indeterminate"
        self.progress_push.config(mode=mode)
        if mode == "indeterminate":
            self.progress_push.start(10)
        else:
            self.progress_push.stop()

    def log(self, message):
        self.log_text.config(state="normal")
        self.log_text.insert("end", message + "\n")
        self.log_text.see("end")
        self.log_text.config(state="disabled")
        self.top.update_idletasks()

    def close(self):
        self.top.grab_release()
        self.top.destroy()
