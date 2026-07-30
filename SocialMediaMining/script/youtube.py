import yt_dlp
import mysql.connector
import re

print("Menghubungkan ke database MySQL...")
# 1. Membuka koneksi ke database Laragon 'drive'
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",      # Kosongkan karena bawaan Laragon tidak pakai password
    database="drive"
)
cursor = db.cursor()

# 2. Daftar link berita YouTube yang ingin diambil (Isi dengan link video berita YouTube ril jika ada)
daftar_url = [
    # "https://www.youtube.com/watch?v=jNQXAC9IVRw", 
    # "https://www.youtube.com/watch?v=dQw4w9WgXcQ"
]

print("Mulai menyedot berita dari YouTube...")
ydl_opts = {'quiet': True, 'extract_flat': True, 'force_generic_extractor': False}

with yt_dlp.YoutubeDL(ydl_opts) as ydl:
    for url in daftar_url:
        info = ydl.extract_info(url, download=False)
        
        judul_berita = info.get("title", "Tidak ada judul")
        sumber = "YouTube"
        
        # Cari category_id default
        cursor.execute("SELECT id FROM categories LIMIT 1")
        row = cursor.fetchone()
        category_id = row[0] if row else 1
        
        # Buat slug unik
        slug = re.sub(r'[^a-zA-Z0-9]+', '-', judul_berita.lower()).strip('-')[:100]
        if not slug:
            slug = "youtube-video"
        
        # Cek apakah artikel dengan source_url ini sudah ada
        cursor.execute("SELECT id FROM articles WHERE source_url = %s", (url,))
        if cursor.fetchone():
            print(f"-> Lewati (sudah ada): {judul_berita}")
            continue

        # 3. Menyiapkan perintah SQL untuk memasukkan data ke tabel articles
        sql = """
        INSERT INTO articles 
        (user_id, category_id, title, slug, excerpt, content, source, source_url, platform, verdict, verdict_score, verdict_reasoning, published_at, created_at, updated_at) 
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW(), NOW())
        """
        nilai = (
            1, # user_id
            category_id,
            judul_berita,
            slug,
            f"Video rujukan dari YouTube: {judul_berita}", # excerpt
            f"Konten berita di-import otomatis dari rujukan YouTube: {judul_berita}", # content
            sumber,
            url,
            "YouTube",
            "asli",
            90.0,
            "Terverifikasi dari postingan YouTube publik."
        )
        
        # 4. Mengeksekusi penambahan data ke database
        cursor.execute(sql, nilai)
        db.commit() # Menyimpan perubahan secara permanen
        
        print(f"-> Berhasil menyimpan ke articles: {judul_berita}")

db.close()
print("=====================================================")
print("SUKSES! Semua berita YouTube telah dimasukkan ke database.")
print("=====================================================")