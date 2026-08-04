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

# 2. Daftar channel YouTube berita/daerah Kota Metro untuk dipantau secara otomatis
daftar_channel = [
    "https://www.youtube.com/@PemerintahKotaMetro/videos"
]

print("Mulai memantau channel YouTube Kota Metro...")
# Opsi yt_dlp untuk mengekstrak flat list berisi video terbaru
ydl_opts_flat = {
    'quiet': True, 
    'extract_flat': True,
    'playlistend': 5, # Hanya ambil 5 video teratas (terbaru) dari channel
}

video_urls = []

with yt_dlp.YoutubeDL(ydl_opts_flat) as ydl:
    for channel_url in daftar_channel:
        try:
            print(f"Memindai channel: {channel_url}")
            channel_info = ydl.extract_info(channel_url, download=False)
            if 'entries' in channel_info:
                for entry in channel_info['entries']:
                    video_id = entry.get('id')
                    if video_id:
                        video_urls.append(f"https://www.youtube.com/watch?v={video_id}")
            print(f"-> Ditemukan {len(video_urls)} video terbaru dari channel.")
        except Exception as e:
            print(f"[WARN] Gagal membaca channel {channel_url}: {e}")

# Opsi untuk mengekstrak info detail video
ydl_opts_detail = {'quiet': True, 'extract_flat': False}

with yt_dlp.YoutubeDL(ydl_opts_detail) as ydl:
    for url in video_urls:
        try:
            # Cek dulu apakah video ini sudah di-import sebelumnya
            cursor.execute("SELECT id FROM articles WHERE source_url = %s", (url,))
            if cursor.fetchone():
                continue
                
            print(f"Mengekstrak detail video baru: {url}")
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
            
            # Ambil thumbnail URL
            thumbnail_url = info.get("thumbnail") or (info.get("thumbnails")[0].get("url") if info.get("thumbnails") else None)
            
            # Ambil potongan deskripsi untuk isi berita
            description = info.get("description", "")
            excerpt = description[:150] + "..." if len(description) > 150 else (description or f"Video berita Kota Metro: {judul_berita}")
            content = description or f"Konten berita di-import otomatis dari rujukan YouTube: {judul_berita}"
            
            # 3. Menyiapkan perintah SQL untuk memasukkan data ke tabel articles
            sql = """
            INSERT INTO articles 
            (user_id, category_id, title, slug, excerpt, content, image_path, source, source_url, platform, verdict, verdict_score, verdict_reasoning, published_at, created_at, updated_at) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW(), NOW())
            """
            nilai = (
                1, # user_id
                category_id,
                judul_berita,
                slug,
                excerpt,
                content,
                thumbnail_url,
                sumber,
                url,
                "YouTube",
                "asli",
                95.0,
                "Terverifikasi otomatis dari Channel Pemerintah Kota Metro resmi."
            )
            
            # 4. Mengeksekusi penambahan data ke database
            cursor.execute(sql, nilai)
            db.commit() # Menyimpan perubahan secara permanen
            print(f"-> Berhasil mengimpor berita baru: {judul_berita}")
            
        except Exception as e:
            print(f"[ERROR] Gagal mengimpor video {url}: {e}")

db.close()
print("=====================================================")
print("SUKSES! Sinkronisasi otomatis berita YouTube selesai.")
print("=====================================================")