import subprocess

class GitRepo:
    @staticmethod
    def delete_local_branch(branch):
        # Elimina un branch locale e restituisce direttamente l'output di git.
        try:
            output = subprocess.check_output(
                ['git', 'branch', '-D', branch],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, output.strip()
        except subprocess.CalledProcessError as e:
            return False, e.output.strip() if hasattr(e, 'output') and e.output else str(e)
    @staticmethod
    def create_and_checkout(branch):
        # Crea un nuovo branch locale e fa il checkout su di esso, mostra output git.
        try:
            remote_branches = GitRepo.get_remote_branches()
            if branch in remote_branches:
                output = subprocess.check_output(
                    ['git', 'checkout', '-b', branch, f'origin/{branch}'],
                    stderr=subprocess.STDOUT,
                    text=True,
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
                return True, output.strip()
            else:
                output = subprocess.check_output(
                    ['git', 'checkout', '-b', branch],
                    stderr=subprocess.STDOUT,
                    text=True,
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                )
                return True, output.strip()
        except subprocess.CalledProcessError as e:
            return False, e.output.strip() if hasattr(e, 'output') and e.output else str(e)
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
            output = subprocess.check_output(
                ['git', 'fetch'],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0),
                startupinfo=startupinfo
            )
            return True, output.strip()
        except subprocess.CalledProcessError as e:
            return False, e.output.strip() if hasattr(e, 'output') and e.output else str(e)

    @staticmethod
    def pull(branch):
        try:
            output = subprocess.check_output(
                ['git', 'pull', 'origin', branch],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, output.strip()
        except subprocess.CalledProcessError as e:
            return False, e.output.strip() if hasattr(e, 'output') and e.output else str(e)

    @staticmethod
    def pull_force(branch):
        try:
            output_fetch = subprocess.check_output(
                ['git', 'fetch', 'origin', branch],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            output_reset = subprocess.check_output(
                ['git', 'reset', '--hard', f'origin/{branch}'],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, (output_fetch + '\n' + output_reset).strip()
        except subprocess.CalledProcessError as e:
            return False, e.output.strip() if hasattr(e, 'output') and e.output else str(e)

    @staticmethod
    def push(files, branch, commit_msg):
        try:
            output = ""
            for f in files:
                output += subprocess.check_output(
                    ['git', 'add', f],
                    stderr=subprocess.STDOUT,
                    text=True,
                    creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
                ) or ""
            status = subprocess.check_output(
                ['git', 'status', '--porcelain'],
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            if not status.strip():
                return False, "Nessuna modifica da committare."
            output += subprocess.check_output(
                ['git', 'commit', '-m', commit_msg],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            output += subprocess.check_output(
                ['git', 'push', 'origin', branch],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, output.strip()
        except subprocess.CalledProcessError as e:
            return False, e.output.strip() if hasattr(e, 'output') and e.output else str(e)

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
            output = subprocess.check_output(
                ['git', 'checkout', branch],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, output.strip()
        except subprocess.CalledProcessError as e:
            return False, e.output.strip() if hasattr(e, 'output') and e.output else str(e)

    @staticmethod
    def checkout_new(branch):
        try:
            output = subprocess.check_output(
                ['git', 'checkout', '-b', branch, f'origin/{branch}'],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0)
            )
            return True, output.strip()
        except subprocess.CalledProcessError as e:
            return False, e.output.strip() if hasattr(e, 'output') and e.output else str(e)

    @staticmethod
    def get_github_user():
        """Restituisce l'utente GitHub autenticato tramite GitHub CLI"""
        try:
            startupinfo = None
            if hasattr(subprocess, 'STARTF_USESHOWWINDOW'):
                startupinfo = subprocess.STARTUPINFO()
                startupinfo.dwFlags |= subprocess.STARTF_USESHOWWINDOW
                startupinfo.wShowWindow = subprocess.SW_HIDE
                
            output = subprocess.check_output(
                ['gh', 'auth', 'status'],
                stderr=subprocess.STDOUT,
                text=True,
                creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0),
                startupinfo=startupinfo
            )
            
            # Estrae il nome utente dall'output
            for line in output.split('\n'):
                if 'Logged in to github.com as' in line:
                    username = line.split('as ')[-1].split(' ')[0]
                    return username
            return "(non autenticato)"
        except subprocess.CalledProcessError:
            return "(non autenticato)"
        except FileNotFoundError:
            return "(GitHub CLI non installato)"
        except Exception:
            return "(errore)"
