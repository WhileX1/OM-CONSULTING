import subprocess

class GitRepo:
    @staticmethod
    def delete_local_branch(branch):
        # Elimina un branch locale in modo sicuro.
        try:
            subprocess.check_call(
                ['git', 'branch', '-D', branch],
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, f"Branch locale '{branch}' eliminato con successo."
        except subprocess.CalledProcessError as e:
            return False, str(e)
        except Exception as e:
            return False, str(e)
    @staticmethod
    def create_and_checkout(branch):
        # Crea un nuovo branch locale e fa il checkout su di esso.
        # Se il branch remoto esiste, lo traccia, altrimenti crea solo locale.
        try:
            # Prima controlla se esiste un branch remoto con questo nome
            remote_branches = GitRepo.get_remote_branches()
            if branch in remote_branches:
                # Traccia il branch remoto
                subprocess.check_call(
                    ['git', 'checkout', '-b', branch, f'origin/{branch}'],
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
                return True, f"Creato e tracciato il branch remoto '{branch}'"
            else:
                # Crea solo locale
                subprocess.check_call(
                    ['git', 'checkout', '-b', branch],
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
                return True, f"Creato e spostato su nuovo branch locale '{branch}'"
        except subprocess.CalledProcessError as e:
            return False, str(e)
        except Exception as e:
            return False, str(e)
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
    def push(files, branch, commit_msg):
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
