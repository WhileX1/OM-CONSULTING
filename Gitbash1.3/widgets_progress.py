import tkinter as tk
from tkinter import ttk

class ProgressWindow:
    def __init__(self, parent, title="Caricamento...", max_value=100):
        self.top = tk.Toplevel(parent)
        self.top.title(title)
        self.top.geometry("420x220")
        self.top.resizable(False, False)
        self.top.configure(bg="#f8f8f8")
        self.top.transient(parent)
        self.top.grab_set()
        self.top.protocol("WM_DELETE_WINDOW", lambda: None)  # Disabilita chiusura

        tk.Label(self.top, text=title, font=("Segoe UI", 11, "bold"), bg="#f8f8f8").pack(pady=(10, 5))
        self.progress = ttk.Progressbar(self.top, orient="horizontal", length=380, mode="determinate", maximum=max_value)
        self.progress.pack(pady=5, padx=20)
        self.log_text = tk.Text(self.top, height=7, width=52, font=("Consolas", 9), state="disabled", bg="#f8f8f8")
        self.log_text.pack(padx=10, pady=(5, 10), fill="both", expand=True)
        self._cancelled = False

    def set_max(self, max_value):
        self.progress["maximum"] = max_value

    def update_progress(self, value):
        self.progress["value"] = value
        self.top.update_idletasks()

    def log(self, message):
        self.log_text.config(state="normal")
        self.log_text.insert("end", message + "\n")
        self.log_text.see("end")
        self.log_text.config(state="disabled")
        self.top.update_idletasks()

    def close(self):
        self.top.grab_release()
        self.top.destroy()
