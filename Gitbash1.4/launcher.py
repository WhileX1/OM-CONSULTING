import os
import sys
import atexit

# Import necessari per la GUI e messaggi
try:
    import tkinter as tk
    from tkinter import messagebox as mb
except ImportError:
    tk = None
    mb = None

# Import dell'app principale
try:
    from main import GitGuiApp
except ImportError:
    GitGuiApp = None

def is_pid_running(pid):
    try:
        if pid <= 0:
            return False
        if os.name == 'nt':
            import ctypes
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
    app.update_idletasks()
    app.update()
    return app.winfo_exists() and (app.state() != 'withdrawn')

def remove_lock(lockfile):
    try:
        if os.path.exists(lockfile):
            os.remove(lockfile)
    except Exception:
        pass

def main():
    lockfile = os.path.join(os.getenv('TEMP') or '.', 'gitbash_auto.lock')

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

    atexit.register(lambda: remove_lock(lockfile))

    try:
        # Controllo ambiente Tkinter
        if tk is None:
            raise ImportError("Tkinter non disponibile")
        try:
            root = tk.Tk()
            root.withdraw()
            root.update()
            root.destroy()
        except Exception as tkerr:
            try:
                if mb:
                    mb.showerror("Errore ambiente Tkinter", f"Tkinter non funziona correttamente:\n{tkerr}")
            except Exception:
                pass
            remove_lock(lockfile)
            sys.exit(1)

        if GitGuiApp is None:
            if mb:
                mb.showerror("Errore import", "Impossibile importare GitGuiApp da main.py.")
            remove_lock(lockfile)
            sys.exit(1)

        app = GitGuiApp()
        if not check_gui_visible(app):
            try:
                if mb:
                    mb.showerror("Errore GUI", "La finestra principale non è visibile.\nControlla che non ci siano errori di Tkinter o di ambiente.")
            except Exception:
                pass
            remove_lock(lockfile)
            sys.exit(1)
        app.mainloop()
    except Exception as e:
        try:
            if mb:
                mb.showerror("Errore avvio app", f"Errore durante l'avvio dell'app:\n{e}")
        except Exception:
            pass
        raise
    finally:
        remove_lock(lockfile)

if __name__ == "__main__":
    main()