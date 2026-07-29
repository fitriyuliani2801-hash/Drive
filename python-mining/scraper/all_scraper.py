import time
import pandas as pd
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager

# Konfigurasi Driver Browser Otomatis
options = webdriver.ChromeOptions()
# options.add_argument("--headless") # Buka jika tidak ingin jendela browser muncul fisik
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)

# Masukkan URL target media sosial/halaman publik
url = "https://example.com/halaman-target" 
driver.get(url)
time.sleep(3)

# Proses Auto-Scrolling untuk meload semua data
print("Sedang mengambil semua data dengan melakukan scrolling...")
last_height = driver.execute_script("return document.body.scrollHeight")

while True:
    # Scroll ke paling bawah halaman
    driver.execute_script("window.scrollTo(0, document.body.scrollHeight);")
    time.sleep(2) # Tunggu halaman memuat data baru
    
    new_height = driver.execute_script("return document.body.scrollHeight")
    if new_height == last_height:
        break # Berhenti jika sudah sampai paling bawah
    last_height = new_height

# Ambil elemen data yang diinginkan (sesuaikan tag/class HTML target)
elements = driver.find_elements(By.TAG_NAME, "p") 
data_list = [el.text for el in elements if el.text.strip() != ""]

driver.quit()

# Simpan ke CSV
df = pd.DataFrame(data_list, columns=["Teks"])
df.to_csv("semua_data_sosmed.csv", index=False, encoding="utf-8")
print(f"Berhasil! Total {len(data_list)} data berhasil disimpan ke semua_data_sosmed.csv")