#!/usr/bin/env python3
"""Déploiement BOUGIES — Production"""

import os
import ftplib
import tempfile

LOCAL_PATH = os.path.expanduser("~/.openclaw/workspace/projets/bougies")
FTP_HOST = "195.35.49.242"
FTP_USER = "u417457839"
FTP_PASS = "NewProduction18@H"
DOMAIN = "les-bougies-de-seraphie.fr"

def deploy():
    print("🚀 DÉPLOIEMENT BOUGIES — Production")
    print("=" * 50)
    
    try:
        ftp = ftplib.FTP(FTP_HOST, timeout=60)
        ftp.login(FTP_USER, FTP_PASS)
        print("✅ Connecté à Hostinger")
        
        # Créer dossier bougies s'il n'existe pas
        try:
            ftp.cwd("public_html_bougies")
            print("📁 Dossier public_html_bougies existe")
        except:
            ftp.cwd("/public_html")
            ftp.mkd("../public_html_bougies")
            ftp.cwd("../public_html_bougies")
            print("📁 Dossier public_html_bougies créé")
        
        # Upload fichiers essentiels
        print("📤 Upload en cours...")
        
        files_to_upload = [
            "app", "bootstrap", "config", "database", "public", 
            "resources", "routes", "storage", "vendor",
            "artisan", "composer.json", "composer.lock", ".env"
        ]
        
        for item in files_to_upload:
            local_path = os.path.join(LOCAL_PATH, item)
            if os.path.exists(local_path):
                if os.path.isdir(local_path):
                    upload_dir(ftp, local_path, item)
                else:
                    with open(local_path, 'rb') as f:
                        ftp.storbinary(f'STOR {item}', f)
                print(f"  ✅ {item}")
        
        ftp.quit()
        print("\n✅ DÉPLOIEMENT TERMINÉ")
        print(f"🌐 {DOMAIN}")
        
    except Exception as e:
        print(f"\n❌ Erreur: {e}")
        return False
    return True

def upload_dir(ftp, local_path, remote_name):
    """Upload récursif d'un dossier"""
    try:
        ftp.mkd(remote_name)
    except:
        pass
    ftp.cwd(remote_name)
    
    for item in os.listdir(local_path):
        local_item = os.path.join(local_path, item)
        if os.path.isdir(local_item):
            upload_dir(ftp, local_item, item)
        else:
            with open(local_item, 'rb') as f:
                ftp.storbinary(f'STOR {item}', f)
    
    ftp.cwd("..")

if __name__ == "__main__":
    deploy()
