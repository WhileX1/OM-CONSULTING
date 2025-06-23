import subprocess
import os
import sys

def run_command(command):
    """Esegue un comando in Git Bash e mostra l'output"""
    print(f"\n> Eseguo: {command}")
    
    # Usa shell=True per eseguire in Git Bash
    process = subprocess.Popen(
        command,
        shell=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        text=True
    )
    
    # Stampa output in tempo reale da stdout
    while True:
        output = process.stdout.readline()
        if output == '' and process.poll() is not None:
            break
        if output:
            print(output.strip())
    
    # Stampa output di stderr (che per Git può contenere anche messaggi informativi)
    stderr = process.stderr.read().strip()
    if stderr:
        print(stderr)
    
    # Ritorna il codice di uscita
    return process.poll()

def download():
    """Esegue il download (git pull)"""
    return run_command('git pull origin main')

def upload(commit_message=None):
    """Esegue l'upload (add, commit, push)"""
    # 1. Naviga alla directory
    os.chdir(r'C:\Users\Sviatoslav\Desktop\OM-CONSULTING')
    
    # 2. git add .
    if run_command('git add .') != 0:
        return False
    
    # 3. git commit
    if not commit_message:
        commit_message = input("Inserisci un messaggio di commit: ")
    
    if run_command(f'git commit -m "{commit_message}"') != 0:
        return False
    
    # 4. git push
    return run_command('git push origin main') == 0

if __name__ == "__main__":
    print("=== Script di automazione Git ===")
    print("1. Download (git pull)")
    print("2. Upload (git add, commit, push)")
    print("3. Entrambi (prima download, poi upload)")
    print("Q. Esci")
    
    scelta = input("\nScegli un'opzione: ").strip().upper()
    
    if scelta == '1':
        print("\n--- DOWNLOAD IN CORSO ---")
        download()
    
    elif scelta == '2':
        print("\n--- UPLOAD IN CORSO ---")
        commit_message = input("Inserisci un messaggio di commit: ")
        upload(commit_message)
    
    elif scelta == '3':
        print("\n--- DOWNLOAD IN CORSO ---")
        if download() == 0:
            print("\n--- UPLOAD IN CORSO ---")
            commit_message = input("Inserisci un messaggio di commit: ")
            upload(commit_message)
    
    elif scelta == 'Q':
        print("Arrivederci!")
    
    else:
        print("Opzione non valida.")