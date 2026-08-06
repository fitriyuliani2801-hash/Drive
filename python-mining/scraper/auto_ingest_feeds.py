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
options.add_argument("--mute-audio") # Bisukan suara video yang di-scrape
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
    try:
        cursor.execute(sql, (1, category_id, title, slug, excerpt, content, image_url, author, url, platform, "asli", 95.0, f"Di-import otomatis dari postingan viral {platform}."))
        db.commit()
        print(f"[SUCCESS] Berhasil mengimpor berita baru ({platform}): {title[:50]}...")
    except Exception as e:
        print(f"[ERROR] Gagal menyimpan artikel: {e}")

# ==========================================
# 1. AUTO-INGEST MULTI-PLATFORM VIA APIFY
# ==========================================
from apify_client import scrape_instagram_feed, scrape_tiktok_feed, scrape_facebook_feed

# 1. Ingest Instagram
print("\n[INSTAGRAM] Memindai berita viral Kota Metro via Apify...")
try:
    ig_posts = scrape_instagram_feed("infokotametrolampung", limit=3)
    print(f"Ditemukan {len(ig_posts)} link postingan Instagram terbaru.")
    for post in ig_posts:
        save_article(post["url"], "Instagram", post["caption"][:150] or "Postingan Instagram Metro", post["caption"] or "Postingan terbaru dari Instagram", post["image_url"], post["author"])
except Exception as e:
    print(f"[ERROR] Gagal memindai Instagram: {e}")

# 2. Ingest TikTok
print("\n[TIKTOK] Memindai video viral Kota Metro via Apify...")
try:
    tiktok_videos = scrape_tiktok_feed("metro.terkini", limit=3)
    print(f"Ditemukan {len(tiktok_videos)} link video TikTok terbaru.")
    for vid in tiktok_videos:
        save_article(vid["url"], "TikTok", vid["caption"][:150] or "Video TikTok Metro", vid["caption"] or "Video terbaru dari TikTok", vid["image_url"], vid["author"])
except Exception as e:
    print(f"[ERROR] Gagal memindai TikTok: {e}")

# 3. Ingest Facebook
print("\n[FACEBOOK] Memindai postingan Humas Pemkot Metro via Apify...")
try:
    fb_posts = scrape_facebook_feed("HumasPemkotMetro", limit=3)
    print(f"Ditemukan {len(fb_posts)} link postingan Facebook terbaru.")
    for post in fb_posts:
        save_article(post["url"], "Facebook", post["caption"][:150] or "Postingan Facebook Metro", post["caption"] or "Postingan terbaru dari Facebook", post["image_url"], post["author"])
except Exception as e:
    print(f"[ERROR] Gagal memindai Facebook: {e}")

# ==========================================
# 4. AUTO-INGEST YOUTUBE (Multi-Channel Pemantauan Berita Kota Metro)
# ==========================================
print("\n[YOUTUBE] Memindai video berita Kota Metro dari berbagai channel...")
try:
    daftar_channel = [
        {"url": "https://www.youtube.com/@PemerintahKotaMetro/videos", "default_author": "Pemerintah Kota Metro"},
        {"url": "https://www.youtube.com/@tribunlampung/videos", "default_author": "Tribun Lampung"},
        {"url": "https://www.youtube.com/@kompastvlampung/videos", "default_author": "KompasTV Lampung"},
        {"url": "https://www.youtube.com/@KupasTVLampung/videos", "default_author": "Kupas TV Lampung"},
        {"url": "https://www.youtube.com/@radarlampungtv/videos", "default_author": "Radar Lampung TV"}
    ]
    ydl_opts_flat = {'quiet': True, 'extract_flat': True, 'playlistend': 8}
    video_targets = []
    
    with yt_dlp.YoutubeDL(ydl_opts_flat) as ydl:
        for item in daftar_channel:
            channel_url = item["url"]
            try:
                print(f"Memindai channel: {channel_url}")
                channel_info = ydl.extract_info(channel_url, download=False)
                if 'entries' in channel_info:
                    for entry in channel_info['entries']:
                        video_id = entry.get('id')
                        video_title = entry.get('title', '')
                        
                        # Filter agar hanya mengambil video yang berkaitan dengan "Metro"
                        title_lower = video_title.lower()
                        is_metro_related = any(k in title_lower for k in [
                            'metro', 'kota metro', 'pemkot metro', 'samber', 'tejosari', 
                            'ganjar asri', 'metro barat', 'metro timur', 'metro utara', 
                            'metro selatan', 'metro pusat'
                        ])
                        
                        # Selalu terima video dari channel resmi Pemkot Metro, tapi filter channel berita umum
                        is_official = "@PemerintahKotaMetro" in channel_url
                        
                        if video_id and (is_official or is_metro_related):
                            video_targets.append({
                                "url": f"https://www.youtube.com/watch?v={video_id}",
                                "author": item["default_author"]
                            })
            except Exception as e:
                print(f"[WARN] Gagal memindai YouTube channel {channel_url}: {e}")
                
    print(f"Ditemukan {len(video_targets)} video YouTube terbaru yang relevan dengan Kota Metro.")
    
    ydl_opts_detail = {'quiet': True, 'extract_flat': False}
    with yt_dlp.YoutubeDL(ydl_opts_detail) as ydl:
        for target in video_targets:
            url = target["url"]
            author = target["author"]
            try:
                cursor.execute("SELECT id FROM articles WHERE source_url = %s", (url,))
                if cursor.fetchone():
                    continue
                    
                print(f"Mengekstrak detail video baru: {url}")
                info = ydl.extract_info(url, download=False)
                judul_berita = info.get("title", "Tidak ada judul")
                thumbnail_url = info.get("thumbnail") or (info.get("thumbnails")[0].get("url") if info.get("thumbnails") else None)
                description = info.get("description", f"Video berita Kota Metro: {judul_berita}")
                
                save_article(url, "YouTube", judul_berita, description, thumbnail_url, author)
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
