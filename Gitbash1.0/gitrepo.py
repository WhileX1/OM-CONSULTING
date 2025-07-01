import subprocess
from tkinter import messagebox as mb

class GitRepo:
    @staticmethod
    def create_and_checkout(branch):
        """
        Crea un nuovo branch locale e fa il checkout su di esso.
        Restituisce (ok, msg).
        """
        try:
            subprocess.check_call(
                ['git', 'checkout', '-b', branch],
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, f"Creato e spostato su '{branch}'"
        except subprocess.CalledProcessError as e:
            # Prova a capire se il branch esiste già
            try:
                output = subprocess.check_output(
                    ['git', 'branch', '--list', branch],
                    text=True,
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
                if output.strip():
                    return False, f"Il branch '{branch}' esiste già."
            except Exception:
                pass
            return False, f"Errore nella creazione del branch: {str(e)}"
    @staticmethod
    def is_valid_repo():
        try:
            startupinfo = None
            if hasattr(subprocess, 'STARTF_USESHOWWINDOW'):
                startupinfo = subprocess.STARTUPINFO()
                startupinfo.dwFlags |= subprocess.STARTF_USESHOWWINDOW
                startupinfo.wShowWindow = subprocess.SW_HIDE
            subprocess.check_output(
                ['git', 'rev-parse', '--is-inside-work-tree'],
                stderr=subprocess.STDOUT,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0),
                startupinfo=startupinfo
            )
            return True
        except Exception:
            return False

    @staticmethod
    def has_commits():
        try:
            startupinfo = None
            if hasattr(subprocess, 'STARTF_USESHOWWINDOW'):
                startupinfo = subprocess.STARTUPINFO()
                startupinfo.dwFlags |= subprocess.STARTF_USESHOWWINDOW
                startupinfo.wShowWindow = subprocess.SW_HIDE
            subprocess.check_output(
                ['git', 'rev-parse', 'HEAD'],
                stderr=subprocess.STDOUT,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0),
                startupinfo=startupinfo
            )
            return True
        except subprocess.CalledProcessError:
            return False
        except Exception:
            return False

    @staticmethod
    def get_current_branch():
        if not GitRepo.has_commits():
            return "(nessun commit)"
        try:
            startupinfo = None
            if hasattr(subprocess, 'STARTF_USESHOWWINDOW'):
                startupinfo = subprocess.STARTUPINFO()
                startupinfo.dwFlags |= subprocess.STARTF_USESHOWWINDOW
                startupinfo.wShowWindow = subprocess.SW_HIDE
            return subprocess.check_output(
                ['git', 'rev-parse', '--abbrev-ref', 'HEAD'],
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0),
                startupinfo=startupinfo
            ).strip()
        except Exception:
            return "(nessun branch)"

    @staticmethod
    def get_current_origin():
        if not GitRepo.has_commits():
            return "(nessuna origine)"
        try:
            startupinfo = None
            if hasattr(subprocess, 'STARTF_USESHOWWINDOW'):
                startupinfo = subprocess.STARTUPINFO()
                startupinfo.dwFlags |= subprocess.STARTF_USESHOWWINDOW
                startupinfo.wShowWindow = subprocess.SW_HIDE
            return subprocess.check_output(
                ['git', 'remote', 'get-url', 'origin'],
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0),
                startupinfo=startupinfo
            ).strip()
        except Exception:
            return "(nessuna origine)"

    @staticmethod
    def fetch():
        try:
            startupinfo = None
            if hasattr(subprocess, 'STARTF_USESHOWWINDOW'):
                startupinfo = subprocess.STARTUPINFO()
                startupinfo.dwFlags |= subprocess.STARTF_USESHOWWINDOW
                startupinfo.wShowWindow = subprocess.SW_HIDE
            subprocess.check_call(
                ['git', 'fetch'],
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0),
                startupinfo=startupinfo
            )
            return True, None
        except subprocess.CalledProcessError as e:
            return False, e.output
        except Exception as e:
            return False, str(e)

    @staticmethod
    def pull(branch):
        try:
            output = subprocess.check_output(
                ['git', 'pull', 'origin', branch],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, output
        except subprocess.CalledProcessError as e:
            return False, e.output
        except Exception as e:
            return False, str(e)

    @staticmethod
    def pull_force(branch):
        try:
            subprocess.check_call(
                ['git', 'fetch', 'origin', branch],
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            subprocess.check_call(
                ['git', 'reset', '--hard', f'origin/{branch}'],
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, f"Forzato allineamento a origin/{branch}"
        except subprocess.CalledProcessError as e:
            return False, str(e)
        except Exception as e:
            return False, str(e)

    @staticmethod
    def push(files, branch, commit_msg, ask_confirm_new_remote=True):
        # Controlla se il branch remoto esiste
        def remote_branch_exists(branch):
            try:
                remotes = subprocess.check_output(
                    ['git', 'branch', '-r'], text=True, creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
                return any(f'origin/{branch}' == b.strip() for b in remotes.splitlines())
            except Exception:
                return False

        try:
            for f in files:
                subprocess.check_call(
                    ['git', 'add', f],
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
            status = subprocess.check_output(
                ['git', 'status', '--porcelain'],
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            if not status.strip():
                return False, "Nessuna modifica da committare."
            subprocess.check_call(
                ['git', 'commit', '-m', commit_msg],
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )

            # Se il branch remoto non esiste, chiedi conferma e fai push con --set-upstream
            if not remote_branch_exists(branch):
                if ask_confirm_new_remote:
                    try:
                        res = mb.askyesno(
                            "Nuovo branch remoto",
                            f"Il branch remoto 'origin/{branch}' non esiste.\nVuoi crearlo e impostare il tracking remoto?"
                        )
                        if not res:
                            return False, "Push annullato dall'utente."
                    except Exception:
                        # In caso di errore nella GUI, procedi comunque
                        pass
                subprocess.check_call(
                    ['git', 'push', '--set-upstream', 'origin', branch],
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
                return True, f"Push completato e branch remoto creato: origin/{branch}"
            else:
                subprocess.check_call(
                    ['git', 'push', 'origin', branch],
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
                return True, "Push completato con successo."
        except subprocess.CalledProcessError as e:
            return False, str(e)

    @staticmethod
    def get_remote_branches():
        try:
            remote_branches = subprocess.check_output(
                ['git', 'branch', '-r'],
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return [b.strip().replace('origin/', '') for b in remote_branches.splitlines() if '->' not in b]
        except Exception:
            return []

    @staticmethod
    def get_local_branches():
        try:
            local_branches = subprocess.check_output(
                ['git', 'branch'],
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return [b.strip().replace("* ", "") for b in local_branches.splitlines()]
        except Exception:
            return []

    @staticmethod
    def checkout(branch):
        try:
            subprocess.check_call(
                ['git', 'checkout', branch],
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, f"Ora sei su '{branch}'"
        except subprocess.CalledProcessError as e:
            return False, str(e)

    @staticmethod
    def checkout_new(branch):
        try:
            subprocess.check_call(
                ['git', 'checkout', '-b', branch, f'origin/{branch}'],
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, f"Creato e spostato su '{branch}'"
        except subprocess.CalledProcessError as e:
            return False, str(e)
