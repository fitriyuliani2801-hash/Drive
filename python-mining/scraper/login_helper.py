import os
import sys
import time
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

# Tentukan path project root
project_root = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
chrome_profile_path = os.path.join(project_root, "chrome-session")

print("==================================================================")
print("  ASISTEN LOGIN MEDIA SOSIAL (INSTAGRAM, FACEBOOK, & TIKTOK)")
print("==================================================================")
print(f"Sesi login Anda akan disimpan di:\n{chrome_profile_path}\n")

options = webdriver.ChromeOptions()
options.add_argument(f"user-data-dir={chrome_profile_path}")
options.add_argument("--start-maximized")

# --- Pengaturan Khusus untuk Melewati Proteksi Bot / Deteksi Automation ---
options.add_experimental_option("excludeSwitches", ["enable-automation"])
options.add_experimental_option('useAutomationExtension', False)
options.add_argument("--disable-blink-features=AutomationControlled")
# -------------------------------------------------------------------------

try:
    print("Membuka browser Chrome...")
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)
    
    # Sembunyikan navigator.webdriver di level browser
    driver.execute_cdp_cmd("Page.addScriptToEvaluateOnNewDocument", {
        "source": """
            Object.defineProperty(navigator, 'webdriver', {
                get: () => undefined
            })
        """
    })
    
    # 1. Buka Instagram login di tab pertama
    print("\n[INFO] Membuka halaman login Instagram...")
    driver.get("https://www.instagram.com/accounts/login/")
    time.sleep(2)
    
    # 2. Buka Facebook login di tab baru (tab kedua)
    print("[INFO] Membuka halaman login Facebook di tab baru...")
    driver.execute_script("window.open('https://www.facebook.com/', '_blank');")
    time.sleep(1)
    
    # 3. Buka TikTok login di tab baru (tab ketiga)
    print("[INFO] Membuka halaman login TikTok di tab baru...")
    driver.execute_script("window.open('https://www.tiktok.com/login', '_blank');")
    time.sleep(1)
    
    print("\n-----------------------------------------------------")
    print("PETUNJUK PENGGUNAAN:")
    print("1. Pastikan Anda sudah LOGIN ke Instagram di Tab Pertama.")
    print("2. Pindah ke Tab Kedua, silakan login ke akun Facebook Anda.")
    print("3. Pindah ke Tab Ketiga (TikTok), klik opsi 'Continue with Facebook' atau daftar/login biasa.")
    print("4. JANGAN menutup jendela browser Chrome secara manual.")
    print("5. Jika sudah selesai login ke semua akun, silakan kembali ke terminal ini.")
    print("-----------------------------------------------------")
    
    input("\nTekan [ENTER] di terminal ini jika Anda sudah selesai login ke semua akun...")
    
except Exception as e:
    print(f"[ERROR] Terjadi kesalahan: {e}")
finally:
    try:
        driver.quit()
        print("\nBrowser ditutup. Sesi login Instagram, Facebook & TikTok berhasil disimpan!")
    except Exception:
        pass
