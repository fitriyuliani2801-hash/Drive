import os
import requests
from bs4 import BeautifulSoup
import pandas as pd

# Pastikan folder data tersedia
os.makedirs("data", exist_ok=True)

# 1. Ganti URL ini dengan URL target Anda yang aktif
url = "https://id.wikipedia.org/wiki/Kota_Metro"

headers = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
}

try:
    print(f"Mengakses URL: {url}")
    response = requests.get(url, headers=headers, timeout=10)

    if response.status_code == 200:
        soup = BeautifulSoup(response.text, 'html.parser')
        
        data_list = []
        for item in soup.find_all('p'):
            text = item.text.strip()
            if text:
                data_list.append(text)
            
        if data_list:
            df = pd.DataFrame(data_list, columns=['komentar'])
            df.to_csv('data/komentar.csv', index=False, encoding='utf-8')
            print(f"[SUKSES] {len(data_list)} data berhasil disimpan ke data/komentar.csv")
        else:
            print("[INFO] Tidak ada data teks yang ditemukan.")
    else:
        print(f"[ERROR] Gagal mengakses halaman, status code: {response.status_code}")

except Exception as e:
    print(f"[ERROR] Terjadi kesalahan HTTP request: {e}")
