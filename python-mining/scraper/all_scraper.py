import os
import time
import pandas as pd
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager

# 1. Pastikan folder penyimpanan data tersedia
os.makedirs("data", exist_ok=True)

# 2. Inisialisasi Driver Browser Chrome
options = webdriver.ChromeOptions()
# options.add_argument("--headless") # Buka komentar ini jika ingin berjalan tanpa membuka jendela Chrome

print("Membuka browser Chrome...")
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)

# 3. Ganti URL ini dengan URL postingan media sosial / situs berita target Anda
url = "https://id.wikipedia.org/wiki/Kota_Metro" # <--- Ganti dengan URL target Anda

try:
    print(f"Mengakses URL: {url}")
    driver.get(url)
    time.sleep(3)

    # 4. Auto-scroll halaman untuk memuat komentar/data lebih banyak
    print("Melakukan auto-scroll halaman...")
    last_height = driver.execute_script("return document.body.scrollHeight")
    
    scroll_attempts = 0
    max_scrolls = 5  # Batas maksimum scroll untuk pengujian

    while scroll_attempts < max_scrolls:
        try:
            driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
            time.sleep(2)
            
            new_height = driver.execute_script("return document.body.scrollHeight")
            if new_height == last_height:
                break
            last_height = new_height
            scroll_attempts += 1
        except Exception as scroll_err:
            print(f"[PERINGATAN] Auto-scroll terhenti: {scroll_err}")
            break

    # 5. Ambil semua teks paragraf / komentar
    print("Mengambil elemen data...")
    elements = driver.find_elements(By.TAG_NAME, "p")
    data_list = [el.text.strip() for el in elements if el.text.strip() != ""]

    # 6. Simpan hasil ke file CSV
    if data_list:
        df = pd.DataFrame(data_list, columns=["komentar"])
        output_path = "data/komentar.csv"
        df.to_csv(output_path, index=False, encoding="utf-8")
        print(f"[SUKSES] {len(data_list)} data berhasil disimpan ke: {output_path}")
    else:
        print("[INFO] Tidak ada data teks yang ditemukan pada halaman tersebut.")

except Exception as e:
    print(f"[ERROR] Terjadi kesalahan atau koneksi terputus: {e}")

finally:
    try:
        driver.quit()
        print("Browser ditutup.")
    except Exception:
        pass
