import os
import requests
import json
import re

def get_apify_token():
    try:
        current_dir = os.path.dirname(os.path.abspath(__file__))
        env_path = os.path.join(current_dir, "..", "..", ".env")
        if os.path.exists(env_path):
            with open(env_path, "r") as f:
                for line in f:
                    if line.startswith("APIFY_API_TOKEN="):
                        return line.split("=")[1].strip()
   except Exception as e:
    print(f"[WARN] Gagal membaca token Apify dari .env: {e}")

raise ValueError("APIFY_API_TOKEN tidak ditemukan di file .env")

def scrape_comments(url, platform):
    token = get_apify_token()
    valid_comments = []
    
    if platform == "Instagram":
        actor_id = "apify~instagram-comment-scraper"
        input_data = {"directUrls": [url], "resultsLimit": 25}
    elif platform == "TikTok":
        actor_id = "clockworks~tiktok-comments-scraper"
        input_data = {"postURLs": [url], "maxComments": 25}
    elif platform == "Facebook":
        actor_id = "apify~facebook-comments-scraper"
        input_data = {"startUrls": [{"url": url}], "maxComments": 25}
    else:
        return []
        
    print(f"[Apify] Memulai penambangan komentar {platform} via Cloud Actor {actor_id}...")
    
    api_url = f"https://api.apify.com/v2/actors/{actor_id}/run-sync-get-dataset-items?token={token}"
    try:
        response = requests.post(api_url, json=input_data, timeout=90)
        if response.status_code in [200, 201]:
            items = response.json()
            print(f"[Apify SUCCESS] Berhasil mendapatkan {len(items)} item data komentar.")
            for item in items:
                author = None
                text = None
                
                if platform == "Instagram":
                    author = item.get("ownerUsername") or item.get("username")
                    text = item.get("text")
                elif platform == "TikTok":
                    author_dict = item.get("author")
                    if isinstance(author_dict, dict):
                        author = author_dict.get("uniqueId") or author_dict.get("nickname")
                    if not author:
                        author = item.get("uniqueId") or item.get("nickname")
                    text = item.get("text")
                elif platform == "Facebook":
                    author = item.get("profileName") or item.get("author") or item.get("profileId")
                    text = item.get("text") or item.get("message")
                    
                if author and text:
                    author_formatted = str(author).strip().replace(" ", "_").replace(".", "").lower()
                    if not author_formatted.startswith("@"):
                        author_formatted = f"@{author_formatted}"
                    valid_comments.append({"author": author_formatted, "text": str(text).strip()})
        else:
            print(f"[Apify ERROR] Gagal menjalankan scraping: HTTP {response.status_code} - {response.text}")
    except Exception as e:
        print(f"[Apify EXCEPTION] Terjadi kegagalan komunikasi API: {e}")
        
    return valid_comments

def scrape_instagram_feed(username, limit=3):
    token = get_apify_token()
    actor_id = "apify~instagram-post-scraper"
    input_data = {"username": [username], "resultsLimit": limit, "onlyPosts": True}
    api_url = f"https://api.apify.com/v2/actors/{actor_id}/run-sync-get-dataset-items?token={token}"
    posts = []
    
    print(f"[Apify] Memindai feed Instagram @{username}...")
    try:
        response = requests.post(api_url, json=input_data, timeout=90)
        if response.status_code in [200, 201]:
            items = response.json()
            for item in items:
                short_code = item.get("shortCode") or item.get("code")
                url = item.get("url") or item.get("postUrl")
                if not url and short_code:
                    url = f"https://www.instagram.com/p/{short_code}/"
                
                # Validasi: Lewati jika bukan post/reel asli
                if not url or ("/p/" not in url and "/reel/" not in url and "/tv/" not in url):
                    continue
                    
                caption = item.get("caption") or ""
                image_url = item.get("displayUrl") or item.get("imageUrl")
                posts.append({
                    "url": url,
                    "caption": caption,
                    "image_url": image_url,
                    "author": f"@{username}"
                })
    except Exception as e:
        print(f"[Apify EXCEPTION] Instagram feed error: {e}")
    return posts

def scrape_tiktok_feed(username, limit=3):
    token = get_apify_token()
    actor_id = "apify~tiktok-scraper"
    input_data = {"profiles": [username], "resultsLimit": limit}
    api_url = f"https://api.apify.com/v2/actors/{actor_id}/run-sync-get-dataset-items?token={token}"
    videos = []
    
    print(f"[Apify] Memindai feed TikTok @{username}...")
    try:
        response = requests.post(api_url, json=input_data, timeout=90)
        if response.status_code in [200, 201]:
            items = response.json()
            for item in items:
                url = item.get("webVideoUrl") or item.get("shareUrl")
                
                # Validasi: Lewati jika bukan video asli
                if not url or "/video/" not in url:
                    continue
                    
                caption = item.get("title") or item.get("text") or ""
                
                # Extract image cover
                image_url = None
                video_dict = item.get("video")
                if isinstance(video_dict, dict):
                    image_url = video_dict.get("coverUrl")
                if not image_url:
                    image_url = item.get("coverUrl")
                    
                videos.append({
                    "url": url,
                    "caption": caption,
                    "image_url": image_url,
                    "author": f"@{username}"
                })
    except Exception as e:
        print(f"[Apify EXCEPTION] TikTok feed error: {e}")
    return videos

def scrape_facebook_feed(page_name, limit=3):
    token = get_apify_token()
    actor_id = "apify~facebook-post-scraper"
    page_url = f"https://www.facebook.com/{page_name}"
    input_data = {"startUrls": [{"url": page_url}], "resultsLimit": limit}
    api_url = f"https://api.apify.com/v2/actors/{actor_id}/run-sync-get-dataset-items?token={token}"
    posts = []
    
    print(f"[Apify] Memindai feed Facebook {page_name}...")
    try:
        response = requests.post(api_url, json=input_data, timeout=90)
        if response.status_code in [200, 201]:
            items = response.json()
            for item in items:
                url = item.get("url")
                
                # Validasi: Lewati profil utama atau link navigasi Facebook
                if not url or any(p in url.lower() for p in ["/about", "/photos_by", "/groups", "/events"]) or url.rstrip("/").endswith(page_name.lower()):
                    continue
                    
                caption = item.get("text") or item.get("message") or ""
                
                # Image extraction
                image_url = None
                images = item.get("attachedImages")
                if isinstance(images, list) and len(images) > 0:
                    image_url = images[0]
                    
                posts.append({
                    "url": url,
                    "caption": caption,
                    "image_url": image_url,
                    "author": page_name
                })
    except Exception as e:
        print(f"[Apify EXCEPTION] Facebook feed error: {e}")
    return posts
