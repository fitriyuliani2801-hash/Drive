import yt_dlp
import mysql.connector

print("Menghubungkan ke database MySQL...")
# 1. Membuka koneksi ke database Laragon
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",      # Kosongkan karena bawaan Laragon tidak pakai password
    database="metrologi"
)
cursor = db.cursor()

# 2. Daftar link berita YouTube yang ingin diambil
daftar_url = [
    "https://www.youtube.com/watch?v=jNQXAC9IVRw", 
    "https://www.youtube.com/watch?v=dQw4w9WgXcQ"
]

print("Mulai menyedot berita dari YouTube...")
ydl_opts = {'quiet': True, 'extract_flat': True, 'force_generic_extractor': False}

with yt_dlp.YoutubeDL(ydl_opts) as ydl:
    for url in daftar_url:
        info = ydl.extract_info(url, download=False)
        
        judul_berita = info.get("title", "Tidak ada judul")
        sumber = "YouTube"
        
        # 3. Menyiapkan perintah SQL untuk memasukkan data
        sql = "INSERT INTO berita (judul, sumber_sosmed, link_url) VALUES (%s, %s, %s)"
        nilai = (judul_berita, sumber, url)
        
        # 4. Mengeksekusi penambahan data ke database
        cursor.execute(sql, nilai)
        db.commit() # Menyimpan perubahan secara permanen
        
        print(f"-> Berhasil menyimpan: {judul_berita}")

print("=====================================================")
print("SUKSES! Semua berita telah masuk ke database MySQL.")
print("=====================================================")