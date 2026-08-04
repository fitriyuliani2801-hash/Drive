import subprocess
import os
import sys

# Dapatkan path root project
project_root = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
sys.path.append(project_root)

print("=====================================================")
print("MEMULAI PIPELINE INTEGRASI SOCIAL MEDIA MINING")
print("=====================================================")

# 1. Jalankan script import berita otomatis dari berbagai media sosial (Instagram, TikTok, FB, YouTube)
print("\n[LANGKAH 1] Mengekstrak berita viral terbaru dari media sosial...")
try:
    script_path = os.path.join(project_root, "python-mining", "scraper", "auto_ingest_feeds.py")
    subprocess.run(["py", script_path], check=True)
except Exception as e:
    print(f"[WARN] Peringatan: Gagal menjalankan auto_ingest_feeds.py: {e}")

# 2. Jalankan db_scraper.py untuk meng-import komentar secara otomatis ke database
print("\n[LANGKAH 2] Menjalankan scraper komentar sosial media...")
try:
    scraper_path = os.path.join(project_root, "python-mining", "scraper", "db_scraper.py")
    subprocess.run(["py", scraper_path], check=True)
except Exception as e:
    print(f"[ERROR] Gagal menjalankan db_scraper.py: {e}")

print("\n=====================================================")
print("PROSES PIPELINE SELESAI!")
print("=====================================================")