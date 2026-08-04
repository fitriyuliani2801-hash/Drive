import os
import time
import re
import json
import random
import hashlib
import mysql.connector
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager
import yt_dlp

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

project_root = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
chrome_profile_path = os.path.join(project_root, "chrome-session")

# 2. Inisialisasi Driver Browser Chrome
print("\nMembuka browser Chrome (Selenium) untuk Auto-Ingest...")
options = webdriver.ChromeOptions()
options.add_argument("--headless")
options.add_argument("--no-sandbox")
options.add_argument("--disable-dev-shm-usage")
options.add_argument("--window-size=1920,1080")
options.add_argument("--log-level=3")
options.add_argument(f"user-data-dir={chrome_profile_path}")

# Anti-bot detection flags
options.add_experimental_option("excludeSwitches", ["enable-automation"])
options.add_experimental_option('useAutomationExtension', False)
options.add_argument("--disable-blink-features=AutomationControlled")

try:
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)
    driver.execute_cdp_cmd("Page.addScriptToEvaluateOnNewDocument", {
        "source": "Object.defineProperty(navigator, 'webdriver', {get: () => undefined})"
    })
except Exception as e:
    print(f"[ERROR] Gagal memuat Chrome Driver: {e}")
    db.close()
    exit(1)

# Ambil category_id default
cursor.execute("SELECT id FROM categories LIMIT 1")
row = cursor.fetchone()
category_id = row['id'] if row else 1

def save_article(url, platform, title, content, image_url, author):
    # Cek apakah artikel sudah ada
    cursor.execute("SELECT id FROM articles WHERE source_url = %s", (url,))
    if cursor.fetchone():
        print(f"-> Lewati (sudah ada di database): {title[:40]}...")
        return
        
    slug = re.sub(r'[^a-zA-Z0-9]+', '-', title.lower()).strip('-')[:100]
    if not slug:
        slug = f"{platform.lower()}-{random.randint(100, 999)}"
    
    # Ambil slug agar unik
    cursor.execute("SELECT id FROM articles WHERE slug = %s", (slug,))
    if cursor.fetchone():
        slug = f"{slug}-{random.randint(10, 99)}"

    excerpt = content[:150] + "..." if len(content) > 150 else content
    
    sql = """
    INSERT INTO articles 
    (user_id, category_id, title, slug, excerpt, content, image_path, source, source_url, platform, verdict, verdict_score, verdict_reasoning, published_at, created_at, updated_at) 
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW(), NOW())
    """
    values = (
        1, # user_id (Admin)
        category_id,
        title,
        slug,
        excerpt,
        content,
        image_url,
        author,
        url,
        platform,
        "asli",
        95.0,
        f"Di-import otomatis dari postingan viral {platform}.",
        now() if hasattr(time, 'strftime') else time.strftime('%Y-%m-%d %H:%M:%S')
    )
    
    try:
        # Gunakan query MySQL datetime
        import datetime
        pub_time = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        cursor.execute(sql, (1, category_id, title, slug, excerpt, content, image_url, author, url, platform, "asli", 95.0, f"Di-import otomatis dari postingan viral {platform}.", pub_time))
        db.commit()
        print(f"[SUCCESS] Berhasil mengimpor berita baru ({platform}): {title[:50]}...")
    except Exception as e:
        print(f"[ERROR] Gagal menyimpan artikel: {e}")

# ==========================================
# 1. AUTO-INGEST INSTAGRAM (infokotametrolampung)
# ==========================================
print("\n[INSTAGRAM] Memindai berita viral Kota Metro...")
try:
    ig_profile = "https://www.instagram.com/infokotametrolampung/"
    driver.get(ig_profile)
    time.sleep(6)
    
    # Kumpulkan tautan postingan spesifik (/p/ atau /reel/)
    post_links = []
    links = driver.find_elements(By.TAG_NAME, "a")
    for link in links:
        href = link.get_attribute("href")
        if href and ("/p/" in href or "/reel/" in href) and "instagram.com" in href:
            post_links.append(href)
            
    # Hapus duplikasi dan ambil 3 teratas (terbaru)
    post_links = list(dict.fromkeys(post_links))[:3]
    print(f"Ditemukan {len(post_links)} link postingan Instagram terbaru.")
    
    for url in post_links:
        try:
            # Cek duplikat sebelum membuka agar hemat waktu
            cursor.execute("SELECT id FROM articles WHERE source_url = %s", (url,))
            if cursor.fetchone():
                continue
                
            driver.get(url)
            time.sleep(5)
            
            title_text = driver.title
            caption_match = re.search(r'Instagram: "(.*)"', title_text)
            title = caption_match.group(1)[:150] if caption_match else "Postingan Viral Instagram Metro"
            content = caption_match.group(1) if caption_match else f"Postingan terbaru dari rujukan link {url}"
            
            # Cari gambar utama
            image_url = None
            imgs = driver.find_elements(By.TAG_NAME, "img")
            for img in imgs:
                src = img.get_attribute("src")
                if src and ("scontent" in src or "instagram" in src):
                    width = img.size.get('width', 0)
                    if width > 200:
                        image_url = src
                        break
                        
            save_article(url, "Instagram", title, content, image_url, "@infokotametrolampung")
        except Exception as e:
            print(f"[WARN] Gagal parse post Instagram {url}: {e}")
except Exception as e:
    print(f"[ERROR] Gagal memindai Instagram: {e}")

# ==========================================
# 2. AUTO-INGEST TIKTOK (@metro.terkini)
# ==========================================
print("\n[TIKTOK] Memindai video viral Kota Metro...")
try:
    tiktok_profile = "https://www.tiktok.com/@metro.terkini"
    driver.get(tiktok_profile)
    time.sleep(6)
    
    video_links = []
    links = driver.find_elements(By.TAG_NAME, "a")
    for link in links:
        href = link.get_attribute("href")
        if href and "/video/" in href and "tiktok.com" in href:
            video_links.append(href)
            
    video_links = list(dict.fromkeys(video_links))[:2]
    print(f"Ditemukan {len(video_links)} link video TikTok terbaru.")
    
    for url in video_links:
        try:
            cursor.execute("SELECT id FROM articles WHERE source_url = %s", (url,))
            if cursor.fetchone():
                continue
                
            driver.get(url)
            time.sleep(5)
            
            title = driver.title[:150] if driver.title else "Video Viral TikTok Metro"
            content = driver.title if driver.title else f"Video terbaru di TikTok seputar Kota Metro: {url}"
            
            # Ambil poster/thumbnail
            image_url = None
            imgs = driver.find_elements(By.TAG_NAME, "img")
            for img in imgs:
                src = img.get_attribute("src")
                if src and ("tiktokcdn" in src or "p16-sign" in src):
                    image_url = src
                    break
                    
            save_article(url, "TikTok", title, content, image_url, "@metro.terkini")
        except Exception as e:
            print(f"[WARN] Gagal parse video TikTok {url}: {e}")
except Exception as e:
    print(f"[ERROR] Gagal memindai TikTok: {e}")

# ==========================================
# 3. AUTO-INGEST FACEBOOK (HumasPemkotMetro Posts)
# ==========================================
print("\n[FACEBOOK] Memindai postingan viral Kota Metro...")
try:
    fb_profile = "https://www.facebook.com/HumasPemkotMetro"
    driver.get(fb_profile)
    time.sleep(6)
    
    post_links = []
    # Kumpulkan tautan postingan Facebook spesifik (mengandung /posts/ atau /videos/ atau watch)
    links = driver.find_elements(By.TAG_NAME, "a")
    for link in links:
        href = link.get_attribute("href")
        if href and ("facebook.com" in href or "fb.watch" in href) and ("/posts/" in href or "/videos/" in href or "/watch/" in href):
            # Bersihkan parameter url agar rapi
            clean_url = href.split('?')[0]
            post_links.append(clean_url)
            
    post_links = list(dict.fromkeys(post_links))[:2]
    print(f"Ditemukan {len(post_links)} link postingan Facebook terbaru.")
    
    for url in post_links:
        try:
            cursor.execute("SELECT id FROM articles WHERE source_url = %s", (url,))
            if cursor.fetchone():
                continue
                
            driver.get(url)
            time.sleep(5)
            
            # Ambil konten teks
            title = "Postingan Humas Pemkot Metro"
            content = "Informasi resmi pembangunan Kota Metro."
            try:
                text_el = driver.find_element(By.CSS_SELECTOR, "div[dir='auto']")
                content = text_el.text.strip()
                title = content[:150]
            except:
                if driver.title:
                    title = driver.title[:150]
                    content = driver.title
            
            image_url = None
            imgs = driver.find_elements(By.TAG_NAME, "img")
            for img in imgs:
                src = img.get_attribute("src")
                if src and ("fbcdn" in src or "facebook.com" in src):
                    width = img.size.get('width', 0)
                    if width > 200:
                        image_url = src
                        break
                        
            save_article(url, "Facebook", title, content, image_url, "Humas Pemkot Metro")
        except Exception as e:
            print(f"[WARN] Gagal parse post Facebook {url}: {e}")
except Exception as e:
    print(f"[ERROR] Gagal memindai Facebook: {e}")

# ==========================================
# 4. AUTO-INGEST YOUTUBE (PemerintahKotaMetro Videos)
# ==========================================
print("\n[YOUTUBE] Memindai video berita Kota Metro...")
try:
    daftar_channel = [
        "https://www.youtube.com/@PemerintahKotaMetro/videos"
    ]
    ydl_opts_flat = {'quiet': True, 'extract_flat': True, 'playlistend': 3}
    video_urls = []
    
    with yt_dlp.YoutubeDL(ydl_opts_flat) as ydl:
        for channel_url in daftar_channel:
            try:
                channel_info = ydl.extract_info(channel_url, download=False)
                if 'entries' in channel_info:
                    for entry in channel_info['entries']:
                        video_id = entry.get('id')
                        if video_id:
                            video_urls.append(f"https://www.youtube.com/watch?v={video_id}")
            except Exception as e:
                print(f"[WARN] Gagal memindai YouTube channel: {e}")
                
    print(f"Ditemukan {len(video_urls)} video YouTube terbaru.")
    
    ydl_opts_detail = {'quiet': True, 'extract_flat': False}
    with yt_dlp.YoutubeDL(ydl_opts_detail) as ydl:
        for url in video_urls:
            try:
                cursor.execute("SELECT id FROM articles WHERE source_url = %s", (url,))
                if cursor.fetchone():
                    continue
                    
                info = ydl.extract_info(url, download=False)
                judul_berita = info.get("title", "Tidak ada judul")
                thumbnail_url = info.get("thumbnail") or (info.get("thumbnails")[0].get("url") if info.get("thumbnails") else None)
                description = info.get("description", f"Video berita Kota Metro: {judul_berita}")
                
                save_article(url, "YouTube", judul_berita, description, thumbnail_url, "Pemerintah Kota Metro")
            except Exception as e:
                print(f"[WARN] Gagal parse video YouTube {url}: {e}")
except Exception as e:
    print(f"[ERROR] Gagal memindai YouTube: {e}")

# Tutup driver & database
driver.quit()
db.close()
print("\n=====================================================")
print("SINKRONISASI AUTO-INGEST MULTI-PLATFORM SELESAI!")
print("=====================================================")
