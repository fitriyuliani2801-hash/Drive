import os
import time
import pandas as pd
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager

# Pastikan direktori penyimpanan data tersedia
os.makedirs("data", exist_ok=True)

def detect_platform(url):
    url_lower = url.lower()
    if "instagram.com" in url_lower or "instagr.am" in url_lower:
        return "Instagram"
    elif "tiktok.com" in url_lower:
        return "TikTok"
    elif "youtube.com" in url_lower or "youtu.be" in url_lower:
        return "YouTube"
    elif "facebook.com" in url_lower or "fb.watch" in url_lower:
        return "Facebook"
    else:
        return "Media Sosial"

# DAFTAR URL TARGET DARI MEDSOS (Instagram, TikTok, YouTube, Facebook)
# Ganti / tambahkan URL postingan media sosial Anda di bawah ini:
target_urls = [
    # "https://www.instagram.com/p/XXXXXXXX/",
    # "https://www.tiktok.com/@username/video/XXXXXXXX",
    # "https://www.youtube.com/watch?v=XXXXXXXX",
    # "https://www.facebook.com/watch/?v=XXXXXXXX",
]

# Filter link aktif (abaikan string kosong)
active_urls = [u for u in target_urls if u.strip() and not u.strip().startswith("#")]

if not active_urls:
    print("[PERINGATAN] Daftar 'target_urls' masih kosong!")
    print("Silakan buka file python-mining/scraper/sosmed_scraper.py lalu tempelkan link postingan Instagram/TikTok/YouTube/Facebook Anda di dalam array target_urls.")
    exit(0)

print("Membuka browser Chrome untuk scraping multi-platform...")
options = webdriver.ChromeOptions()
# options.add_argument("--headless") # Buka jika tidak ingin jendela browser muncul fisik

driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)

all_scraped_data = []

try:
    for url in active_urls:
        platform = detect_platform(url)
        print(f"\n[PARSING {platform.upper()}] Mengakses URL: {url}")
        
        try:
            driver.get(url)
            time.sleep(4)

            # Auto-scroll halaman untuk memuat komentar
            print(f"Melakukan auto-scroll pada postingan {platform}...")
            last_height = driver.execute_script("return document.body.scrollHeight")
            
            for _ in range(5):
                driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
                time.sleep(2)
                new_height = driver.execute_script("return document.body.scrollHeight")
                if new_height == last_height:
                    break
                last_height = new_height

            # Selector elemen teks umum / komentar
            elements = driver.find_elements(By.TAG_NAME, "p")
            if not elements or len(elements) < 3:
                elements = driver.find_elements(By.TAG_NAME, "span")

            count_for_url = 0
            for el in elements:
                text = el.text.strip()
                if text and len(text) > 8 and not text.startswith("http"):
                    all_scraped_data.append({
                        "platform": platform,
                        "source_url": url,
                        "komentar": text
                    })
                    count_for_url += 1

            print(f"[SUKSES] Berhasil mengambil {count_for_url} komentar dari {platform}.")

        except Exception as err_url:
            print(f"[ERROR] Gagal mengambil data dari {url}: {err_url}")

    # Simpan seluruh hasil scraping ke CSV
    if all_scraped_data:
        df = pd.DataFrame(all_scraped_data)
        output_file = "data/komentar.csv"
        df.to_csv(output_file, index=False, encoding="utf-8")
        print(f"\n[SELESAI] Total {len(all_scraped_data)} data berhasil disimpan ke: {output_file}")
    else:
        print("\n[INFO] Tidak ada data komentar yang berhasil diekstrak.")

except Exception as e:
    print(f"[ERROR] Terjadi kesalahan global: {e}")

finally:
    try:
        driver.quit()
        print("Browser ditutup.")
    except Exception:
        pass
