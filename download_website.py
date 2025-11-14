#!/usr/bin/env python3
import ftplib
import os

# Provo con le credenziali del progetto principale
FTP_HOST = "ftp.agstudio.digital"
FTP_USER = "agstudiodiital"
FTP_PASSWORD = "$Giulietta2019!"

print(f"Tentativo di connessione a {FTP_HOST}...")
try:
    ftp = ftplib.FTP(FTP_HOST, timeout=10)
    ftp.login(FTP_USER, FTP_PASSWORD)
    print(f"✓ Connesso!")

    # Lista directory principali
    print("\nDirectory disponibili:")
    ftp.retrlines('LIST')

    # Prova a entrare nella cartella new
    try:
        ftp.cwd('/agstudio.digital/new')
        print("\n\n✓ Trovata cartella /agstudio.digital/new")
        print("\nContenuto:")
        files = []
        ftp.retrlines('LIST', files.append)
        for f in files:
            print(f)
    except Exception as e:
        print(f"\n✗ Errore accesso /agstudio.digital/new: {e}")

    ftp.quit()
except Exception as e:
    print(f"✗ Errore connessione: {e}")
