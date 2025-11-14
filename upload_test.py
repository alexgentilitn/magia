#!/usr/bin/env python3
import ftplib
import os

# Credenziali FTP
FTP_HOST = "ftp.agstudio.digital"
FTP_USER = "agstudiodiital"
FTP_PASSWORD = "$Giulietta2019!"
FTP_REMOTE_PATH = "/agstudio.digital/magia/website-agstudio"

# File da caricare
local_file = "/home/user/ea/test-upload.txt"
remote_file = "test-upload.txt"

print(f"Tentativo di connessione a {FTP_HOST}...")
try:
    # Connessione al server FTP
    ftp = ftplib.FTP(FTP_HOST, timeout=10)
    ftp.login(FTP_USER, FTP_PASSWORD)
    print(f"✓ Connesso come {FTP_USER}")

    # Cambio directory
    try:
        ftp.cwd(FTP_REMOTE_PATH)
        print(f"✓ Entrato in {FTP_REMOTE_PATH}")
    except Exception as e:
        print(f"✗ Errore cambio directory: {e}")
        print("\nDirectory disponibili:")
        ftp.retrlines('LIST')
        ftp.quit()
        exit(1)

    # Lista file attuali
    print(f"\nFile presenti prima dell'upload:")
    files_before = []
    ftp.retrlines('LIST', files_before.append)
    for f in files_before:
        print(f"  {f}")

    # Upload del file
    print(f"\nCaricamento di {local_file}...")
    with open(local_file, 'rb') as f:
        ftp.storbinary(f'STOR {remote_file}', f)
    print(f"✓ File caricato: {remote_file}")

    # Verifica upload
    print(f"\nFile presenti dopo l'upload:")
    files_after = []
    ftp.retrlines('LIST', files_after.append)
    for f in files_after:
        print(f"  {f}")

    ftp.quit()
    print("\n✓ Upload completato con successo!")
    print(f"\nPuoi verificare il file su:")
    print(f"https://www.agstudio.digital/magia/website-agstudio/test-upload.txt")

except Exception as e:
    print(f"✗ Errore: {e}")
    import traceback
    traceback.print_exc()
