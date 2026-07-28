import requests
from bs4 import BeautifulSoup
import pandas as pd

# 1. UBAH LINK INI DENGAN WEBSITE TARGET ANDA YANG ASLI
url = "https://id.wikipedia.org/wiki/Metrologi" # <--- Ini hanya contoh link yang aktif

headers = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
}

# Mengambil halaman web
response = requests.get(url, headers=headers)

if response.status_code == 200:
    soup = BeautifulSoup(response.text, 'html.parser')
    
    # Cari elemen data yang ingin diambil
    data_list = []
    for item in soup.find_all('p'):
        data_list.append(item.text)
        
    # Simpan ke file CSV
    # 2. UBAH NAMA FILE DI BAWAH INI MENJADI data/komentar.csv
    df = pd.DataFrame(data_list, columns=['komentar'])
    df.to_csv('data/komentar.csv', index=False, encoding='utf-8')
    print("Data berhasil disimpan ke data/komentar.csv!")
else:
    print("Gagal mengakses halaman, status code:", response.status_code)