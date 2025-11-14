#!/usr/bin/env python3
import ftplib
import os

# Credenziali account MAGIA
FTP_HOST = "ftp.agstudio.digital"
FTP_USER = "magia@agstudio.digital"
FTP_PASSWORD = "$magia2025!"
FTP_REMOTE_PATH = "/magia/website-agstudio"

def download_file(ftp, remote_file, local_file):
    """Scarica un file dal server FTP"""
    try:
        os.makedirs(os.path.dirname(local_file), exist_ok=True)
        with open(local_file, 'wb') as f:
            ftp.retrbinary(f'RETR {remote_file}', f.write)
        print(f"✓ Scaricato: {remote_file}")
        return True
    except Exception as e:
        print(f"✗ Errore scaricamento {remote_file}: {e}")
        return False

def download_directory(ftp, remote_path, local_path):
    """Scarica ricorsivamente una directory"""
    os.makedirs(local_path, exist_ok=True)

    try:
        ftp.cwd(remote_path)
        print(f"✓ Directory: {remote_path}")
    except Exception as e:
        print(f"✗ Errore accesso a {remote_path}: {e}")
        return

    items = []
    try:
        ftp.retrlines('NLST', items.append)
    except Exception as e:
        print(f"✗ Errore listing: {e}")
        return

    for item in items:
        if item in ['.', '..']:
            continue

        remote_item_path = f"{remote_path}/{item}" if not remote_path.endswith('/') else f"{remote_path}{item}"
        local_item = os.path.join(local_path, item)

        # Prova a determinare se è una directory
        try:
            current_dir = ftp.pwd()
            ftp.cwd(remote_item_path)
            # È una directory
            print(f"📁 Sottodirectory: {item}")
            download_directory(ftp, remote_item_path, local_item)
            ftp.cwd(current_dir)
        except:
            # È un file
            current_dir = ftp.pwd()
            download_file(ftp, item, local_item)

print(f"Connessione a {FTP_HOST}...")
try:
    ftp = ftplib.FTP(FTP_HOST, timeout=10)
    ftp.login(FTP_USER, FTP_PASSWORD)
    print(f"✓ Connesso come {FTP_USER}\n")

    # Lista directory root
    print("Directory root dell'account magia:")
    root_files = []
    ftp.retrlines('LIST', root_files.append)
    for f in root_files:
        print(f"  {f}")

    # Prova ad accedere a website-agstudio
    print(f"\n\nContenuto di {FTP_REMOTE_PATH}:")
    try:
        ftp.cwd(FTP_REMOTE_PATH)
        files = []
        ftp.retrlines('LIST', files.append)
        for f in files:
            print(f"  {f}")

        print(f"\n\nScaricamento file da {FTP_REMOTE_PATH}...\n")
        download_directory(ftp, FTP_REMOTE_PATH, "/home/user/ea/website-agstudio")

        print("\n✓ Download completato!")

    except Exception as e:
        print(f"✗ Errore accesso a {FTP_REMOTE_PATH}: {e}")

    ftp.quit()

except Exception as e:
    print(f"✗ Errore connessione: {e}")
    import traceback
    traceback.print_exc()
