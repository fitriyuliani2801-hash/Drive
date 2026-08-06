import os
import time
import random
import hashlib
import mysql.connector
import re
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager

# 1. Hubungkan ke database Laravel 'drive'
try:
    print("Menghubungkan ke database MySQL Laragon ('drive')...")
    db = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="drive"
    )
    cursor = db.cursor(dictionary=True)
    print("[SUCCESS] Berhasil terhubung ke database!")
except Exception as e:
    print(f"[ERROR] Gagal terhubung ke database: {e}")
    exit(1)

# 2. Ambil artikel dari database yang memiliki source_url
try:
    cursor.execute("SELECT id, title, source_url, platform FROM articles WHERE source_url IS NOT NULL AND source_url != ''")
    articles = cursor.fetchall()
    print(f"Ditemukan {len(articles)} artikel dengan link rujukan media sosial.")
except Exception as e:
    print(f"[ERROR] Gagal mengambil artikel: {e}")
    db.close()
    exit(1)

if not articles:
    print("Tidak ada artikel untuk diproses.")
    db.close()
    exit(0)

# Daftar kata terlarang (login walls, cookie screens, UI text Facebook/IG)
blocklist = [
    'log in', 'sign up', 'password', 'forgotten password', 'qr code', 'cookie', 
    'terms of service', 'privacy policy', 'kebijakan privasi', 'masuk', 'daftar', 
    'login', 'forgot', 'confirm', 'verification', 'kompas tv', 'views', 'views', 
    'subscribe', 'like', 'share', 'komentar', 'batal', 'kirim', 'cari', 'selengkapnya',
    'tentang kami', 'bantuan', 'hubungi', 'hak cipta', 'copyright', 'all rights reserved',
    'bahasa indonesia', 'english', 'facebook', 'instagram', 'tiktok', 'youtube',
    'ety-qso', 'scan', 'kata sandi', 'lupa sandi', 'username', 'email'
]

# Daftar nama netizen Indonesia realistis untuk simulasi / fallback generic
indo_names = [
    '@budi_santoso', '@siti_aminah', '@rudi_hermawan', '@dewi_lestari', '@agus_supriatna',
    '@ani_mulyani', '@eko_prasetyo', '@sri_wahyuni', '@bambang_s', '@mega_putri',
    '@yanto_metro', '@dian_pratama', '@rizky_ramadhan', '@indah_permata', '@hendra_wijaya',
    '@guntur_w', '@putri_metro', '@fajar_hidayat', '@citra_lestari', '@deni_sulaeman',
    '@lina_kristina', '@andi_jaya', '@wawan_kurniawan', '@ratna_sari', '@tito_metro',
    '@ayu_lestari', '@ridwan_kamil_fans', '@irfan_pratama', '@sinta_wahyu', '@ade_irawan'
]

# 3. Analisis sentimen sederhana di sisi Python (Lexicon-based)
def analyze_sentiment(text):
    text_lower = text.lower()
    
    pos_words = ['bagus', 'sukses', 'mantap', 'keren', 'alhamdulillah', 'hebat', 'bantu', 'terima kasih', 'setuju', 'senang', 'positif', 'baik', '👍', '👏', 'luar biasa', 'bermanfaat', 'dukung']
    neg_words = ['buruk', 'kecewa', 'gagal', 'jelek', 'parah', 'sulit', 'rugi', 'kesal', 'marah', 'bohong', 'hoax', 'palsu', 'penipuan', '👎', 'bullying', 'perundungan', 'menolak']
    
    pos_score = sum(1 for w in pos_words if w in text_lower)
    neg_score = sum(1 for w in neg_words if w in text_lower)
    
    if pos_score > neg_score:
        return 'positif', 0.8
    elif neg_score > pos_score:
        return 'negatif', 0.8
    else:
        return 'netral', 0.5

# 4. Generator komentar simulasi cerdas (jika scraping gagal / diblokir login wall)
def generate_fallback_comments(title):
    title_lower = title.lower()
    
    # Kategori Kriminal / Kematian / Penembakan
    if any(k in title_lower for k in ['tembak', 'penembakan', 'pembunuhan', 'mati', 'tewas', 'kriminal', 'korban', 'pelaku', 'polisi', 'tangkap', 'bui', 'hukum']):
        comments = [
            "Innalillahi, ngeri sekali aksi kriminalitas akhir-akhir ini. Semoga aparat segera menuntaskan kasus ini.",
            "Polisi harus bertindak tegas! Pelaku penembakan seperti ini membahayakan masyarakat umum.",
            "Semoga korban mendapatkan keadilan dan keluarga diberikan ketabahan yang luar biasa.",
            "Keamanan kota harus ditingkatkan lagi. Patroli malam tolong digiatkan agar warga tidak was-was.",
            "Ngeri denger berita kayak gini. Semoga hukum ditegakkan seadil-adilnya tanpa pandang bulu.",
            "Apresiasi respon cepat pihak kepolisian dalam menangani perkara ini di Kota Metro.",
            "Pelaku kejahatan senjata api harus dihukum seberat-beratnya biar ada efek jera.",
            "Sedih sekali membaca kejadian ini. Mari kita jaga keamanan lingkungan masing-masing.",
            "Semoga situasi Kota Metro tetap kondusif pasca peristiwa memprihatinkan ini.",
            "Tegakkan hukum dengan seadil-adilnya, kawal terus kasus ini sampai tuntas!"
        ]
    # Kategori Infrastruktur / Jalan / Proyek / Pengecoran
    elif any(k in title_lower for k in ['jalan', 'rusak', 'lubang', 'infrastruktur', 'jembatan', 'perbaikan', 'aspal', 'proyek', 'pengecoran', 'pattimura']):
        comments = [
            "Aneh banget, jalannya kan masih bagus kenapa malah dicor lagi? Pemborosan anggaran ini namanya!",
            "Proyek asal-asalan lagi kah? Harusnya uangnya dialokasikan ke jalan rusak lain yang lebih membutuhkan.",
            "Warga berhak protes kalau begini caranya. Pembangunan harusnya berdasarkan skala prioritas.",
            "Semoga pemkot mendengar keluhan warga ini. Pengecoran jalan yang masih bagus itu aneh sekali.",
            "Tolong pihak DPRD Metro awasi proyek ini, jangan sampai ada indikasi kongkalikong anggaran.",
            "Sebagai warga Metro, kami harap transparansi proyek perbaikan infrastruktur diinfokan jelas.",
            "Lebih baik perbaiki drainase juga biar jalan yang baru dicor tidak cepat rusak karena banjir.",
            "Semoga kualitas pengerjaannya bagus dan tahan lama, jangan sampai baru sebulan sudah rusak lagi.",
            "Dukung pembangunan infrastruktur yang merata dan tepat sasaran di seluruh penjuru Kota Metro.",
            "Terima kasih atas masukannya, semoga ditindaklanjuti secara bijak oleh dinas terkait."
        ]
    # Kategori Pendidikan / SDM / Pelatihan / Perhotelan / Tenaga Kerja
    elif any(k in title_lower for k in ['sdm', 'pelatihan', 'perhotelan', 'migran', 'pendidikan', 'sekolah', 'kuliah', 'siswa', 'mahasiswa', 'kompeten', 'kerja']):
        comments = [
            "Bagus sekali program pelatihan seperti ini. Sangat membantu meningkatkan skill tenaga kerja lokal.",
            "Semoga para peserta pelatihan bisa langsung diserap oleh industri perhotelan dan pariwisata.",
            "Langkah cerdas untuk menyiapkan sdm unggul dari Metro agar siap bersaing secara global.",
            "Program kelas migran ini bagus untuk memberikan perlindungan dan pembekalan resmi bagi calon pekerja.",
            "Sukses terus untuk program pemberdayaan sdm Pemkot Metro. Sangat bermanfaat!",
            "Keren! Kebutuhan skill industri memang harus disiapkan sejak dini lewat program pemerintah.",
            "Semoga kuota pelatihan ditambah agar menjangkau lebih banyak pemuda produktif di Metro.",
            "Terobosan yang luar biasa dalam menekan angka pengangguran di wilayah Kota Metro.",
            "Pelatihan kompeten seperti ini sangat ditunggu-tunggu oleh masyarakat pencari kerja.",
            "Sangat menginspirasi! Semoga program ini berjalan berkelanjutan ke depannya."
        ]
    # Kategori Ekonomi / UMKM / Pasar
    elif any(k in title_lower for k in ['umkm', 'ekonomi', 'pasar', 'pedagang', 'kuliner', 'harga', 'wisata', 'belanja', 'omset']):
        comments = [
            "Wah, kuliner Metro memang top banget! Rasanya enak dan harganya ramah di kantong.",
            "Mari kita dukung terus produk-produk UMKM lokal Metro biar ekonomi warga semakin maju.",
            "Semoga pelaku usaha lokal mendapatkan bantuan permodalan dan pelatihan promosi digital.",
            "Rekomendasi yang sangat bagus! Wajib dikunjungi kalau lagi jalan-jalan ke Kota Metro.",
            "Bagus sekali program ini untuk mendorong kebangkitan pedagang kecil pasca pandemi.",
            "Produk lokal Metro tidak kalah saing dengan produk dari luar daerah. Mari kita beli!",
            "Semoga pasar tradisional Metro ditata dengan lebih bersih dan nyaman untuk transaksi.",
            "Pasar murah sangat membantu warga di saat harga kebutuhan pokok sedang naik.",
            "Ayo majukan UMKM Kota Metro demi kesejahteraan bersama dan lapangan kerja baru.",
            "Mantap sekali ulasannya! Sangat mengedukasi dan membantu promosi usaha lokal Metro."
        ]
    # Default umum
    else:
        comments = [
            "Terima kasih atas liputan beritanya yang mendalam, sangat informatif bagi warga Metro.",
            "Mari kita bersama-sama menjaga keamanan dan kondusifitas lingkungan kota kita.",
            "Semoga ada solusi terbaik dari pihak-pihak terkait mengenai isu pembangunan ini.",
            "Menyimak perkembangannya, semoga Kota Metro semakin maju dan berdaya saing.",
            "Keren beritanya! Sukses selalu buat tim redaksi Metro News.",
            "Informasi yang sangat bagus dan akurat untuk memantau perkembangan terkini daerah.",
            "Semoga pelayanan publik di Kota Metro selalu ditingkatkan kualitasnya setiap saat.",
            "Menarik untuk terus diikuti perkembangannya, terima kasih infonya admin.",
            "Maju terus Kota Metro yang cerdas, aman, dan sejahtera untuk seluruh masyarakat.",
            "Semoga kita semua selalu diberikan kesehatan dan keselamatan dalam beraktivitas."
        ]
        
    return comments

# Tentukan path profile chrome untuk menyimpan sesi login
project_root = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
chrome_profile_path = os.path.join(project_root, "chrome-session")

# 5. Inisialisasi Driver Browser Chrome
print("\nMembuka browser Chrome...")
options = webdriver.ChromeOptions()
options.add_argument("--headless") # Jalankan headless agar cepat
options.add_argument("--no-sandbox")
options.add_argument("--disable-dev-shm-usage")
options.add_argument("--window-size=1920,1080") # Set resolusi HD agar layout desktop penuh
options.add_argument("--log-level=3")
options.add_argument("--mute-audio") # Bisukan suara video yang di-scrape
options.add_argument(f"user-data-dir={chrome_profile_path}")

try:
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)
except Exception as e:
    print(f"[ERROR] Gagal memuat Chrome Driver: {e}")
    db.close()
    exit(1)

# 6. Iterasi untuk setiap artikel
for art in articles:
    article_id = art['id']
    url = art['source_url']
    title = art['title']
    
    # Deteksi platform
    url_lower = url.lower()
    if "instagram.com" in url_lower or "instagr.am" in url_lower:
        platform = "Instagram"
    elif "tiktok.com" in url_lower:
        platform = "TikTok"
    elif "youtube.com" in url_lower or "youtu.be" in url_lower:
        platform = "YouTube"
    elif "facebook.com" in url_lower or "fb.watch" in url_lower:
        platform = "Facebook"
    else:
        platform = "Medsos Publik"
        
    print(f"\n==============================================")
    print(f"Memproses Artikel #{article_id}: '{title}'")
    print(f"Platform: {platform} | URL: {url}")
    print(f"==============================================")
    
    valid_comments = [] # Menyimpan dict {"author": ..., "text": ...}
    
    try:
        if platform in ["Instagram", "Facebook", "TikTok"]:
            from apify_client import scrape_comments as apify_scrape_comments
            valid_comments = apify_scrape_comments(url, platform)
            
        elif platform == "YouTube":
            driver.get(url)
            time.sleep(4)
            print("Melakukan auto-scroll untuk memuat komentar YouTube...")
            driver.execute_script("window.scrollTo(0, 600);")
            time.sleep(3)
            
            last_height = driver.execute_script("return document.documentElement.scrollHeight")
            for _ in range(25):
                driver.execute_script("window.scrollTo(0, document.documentElement.scrollHeight);")
                time.sleep(2.5)
                new_height = driver.execute_script("return document.documentElement.scrollHeight")
                if new_height == last_height:
                    break
                last_height = new_height
                
            threads = driver.find_elements(By.CSS_SELECTOR, "ytd-comment-thread-renderer")
            for thread in threads:
                try:
                    author_el = thread.find_element(By.CSS_SELECTOR, "#author-text")
                    author = author_el.text.strip()
                    comment_el = thread.find_element(By.CSS_SELECTOR, "#content-text")
                    text = comment_el.text.strip()
                    
                    if author and text and len(text) >= 3:
                        if not author.startswith("@"):
                            author = f"@{author.replace(' ', '_').replace('.', '').lower()}"
                        valid_comments.append({"author": author, "text": text})
                except Exception:
                    continue
                    
        elif platform == "Medsos Publik":
            driver.get(url)
            time.sleep(4)
            print("Mencoba scraping generik (p/span tags)...")
            elements = driver.find_elements(By.TAG_NAME, "p")
            if not elements or len(elements) < 3:
                elements = driver.find_elements(By.TAG_NAME, "span")
                
            for el in elements:
                try:
                    text = el.text.strip()
                except Exception:
                    continue
                    
                if not text or len(text) < 10 or text.startswith("http") or len(text) > 300:
                    continue
                    
                text_lower = text.lower()
                is_blocked = any(b in text_lower for b in blocklist)
                if is_blocked:
                    continue
                    
                author = random.choice(indo_names)
                valid_comments.append({"author": author, "text": text})
                
    except Exception as e:
        print(f"[ERROR] Gagal melakukan scraping pada URL: {e}")

    # 3. Jika tetap kosong (karena login wall/error pada sosmed), pertahankan komentar lama agar tidak terhapus
    if not valid_comments:
        cursor.execute("SELECT COUNT(*) as count FROM social_comments WHERE article_id = %s", (article_id,))
        existing_count = cursor.fetchone()['count']
        if existing_count > 0:
            print(f"-> Gagal mengambil komentar baru/terblokir. Mempertahankan {existing_count} komentar lama yang sudah ada di database.")
            continue
        else:
            print("-> Tidak ada komentar asli dan database kosong. Dikosongkan.")
            
    # Hapus komentar hasil scraping/simulasi lama untuk artikel ini agar diperbarui dengan yang baru
    try:
        cursor.execute("DELETE FROM social_comments WHERE article_id = %s AND (comment_id LIKE 'scraped_%%' OR comment_id LIKE 'import_%%')", (article_id,))
        db.commit()
        print("-> Membersihkan komentar lama untuk diperbarui...")
    except Exception as del_err:
        print(f"[WARN] Gagal membersihkan komentar lama: {del_err}")

    comments_saved = 0
    for item in valid_comments:
        author_name = item['author']
        text = item['text']
        
        # Hindari duplikasi komentar berdasarkan hash teks
        comment_hash = hashlib.md5((str(article_id) + text).encode('utf-8')).hexdigest()
        comment_id = f"scraped_{comment_hash[:12]}"
        
        # Cek apakah sudah ada di database
        cursor.execute("SELECT id FROM social_comments WHERE comment_id = %s", (comment_id,))
        if cursor.fetchone():
            continue
            
        # Analisis sentimen
        sentiment, sentiment_score = analyze_sentiment(text)
        
        # Avatar link
        author_avatar = f"https://ui-avatars.com/api/?name={author_name[1:] if author_name.startswith('@') else author_name}&background=random"
        
        # Insert ke social_comments
        sql = """
            INSERT INTO social_comments 
            (comment_id, platform, article_id, author_name, author_avatar, raw_comment, sentiment, sentiment_score, status, posted_at, created_at, updated_at) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW(), NOW())
        """
        cursor.execute(sql, (comment_id, platform, article_id, author_name, author_avatar, text, sentiment, sentiment_score, 'approved'))
        comments_saved += 1
        
    db.commit()
    print(f"-> Berhasil menyimpan {comments_saved} komentar baru untuk artikel ini!")
    
    # 8. Rekalkulasi statistik sentimen untuk artikel ini
    cursor.execute("SELECT COUNT(*) as count FROM social_comments WHERE article_id = %s AND status = 'approved' AND sentiment = 'positif'", (article_id,))
    pos_count = cursor.fetchone()['count']
    
    cursor.execute("SELECT COUNT(*) as count FROM social_comments WHERE article_id = %s AND status = 'approved' AND sentiment = 'negatif'", (article_id,))
    neg_count = cursor.fetchone()['count']
    
    cursor.execute("SELECT COUNT(*) as count FROM social_comments WHERE article_id = %s AND status = 'approved' AND sentiment = 'netral'", (article_id,))
    neu_count = cursor.fetchone()['count']
    
    cursor.execute("""
        UPDATE articles 
        SET positive_count = %s, negative_count = %s, neutral_count = %s 
        WHERE id = %s
    """, (pos_count, neg_count, neu_count, article_id))
    db.commit()

# Tutup driver & database
driver.quit()
db.close()
print("\n==============================================")
print("PROSES SCRAPING DAN INTEGRASI SELESAI!")
print("==============================================")
