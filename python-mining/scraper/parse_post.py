import sys
import os
import json
import time
import re
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager

def parse_post(url):
    project_root = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
    chrome_profile_path = os.path.join(project_root, "chrome-session")

    options = webdriver.ChromeOptions()
    options.add_argument("--headless")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--window-size=1920,1080")
    options.add_argument("--log-level=3")
    options.add_argument(f"user-data-dir={chrome_profile_path}")

    result = {
        "success": False,
        "title": "Tidak Ada Judul",
        "content": "Konten tidak dapat dimuat.",
        "image_url": None,
        "author": "Medsos Publik",
        "platform": "Medsos Publik"
    }

    url_lower = url.lower()
    if "instagram.com" in url_lower or "instagr.am" in url_lower:
        result["platform"] = "Instagram"
    elif "facebook.com" in url_lower or "fb.watch" in url_lower:
        result["platform"] = "Facebook"
    elif "tiktok.com" in url_lower:
        result["platform"] = "TikTok"
    elif "youtube.com" in url_lower or "youtu.be" in url_lower:
        result["platform"] = "YouTube"

    driver = None
    try:
        driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)
        driver.get(url)
        time.sleep(5)

        # 1. Instagram Parsing
        if result["platform"] == "Instagram":
            title_text = driver.title
            caption_match = re.search(r'Instagram: "(.*)"', title_text)
            if caption_match:
                result["title"] = caption_match.group(1)[:150]
                result["content"] = caption_match.group(1)
            else:
                try:
                    caption_el = driver.find_element(By.CSS_SELECTOR, "h1, span._ap3a")
                    result["title"] = caption_el.text.strip()[:150]
                    result["content"] = caption_el.text.strip()
                except:
                    result["title"] = "Postingan Instagram"
                    result["content"] = f"Postingan Instagram dari rujukan link {url}"

            try:
                author_match = re.search(r'^([^@]+)\s*\((@[^\)]+)\)', title_text)
                if author_match:
                    result["author"] = author_match.group(2)
                else:
                    author_el = driver.find_element(By.CSS_SELECTOR, "a[href*='/']")
                    result["author"] = f"@{author_el.text.strip()}"
            except:
                result["author"] = "@instagram_user"

            imgs = driver.find_elements(By.TAG_NAME, "img")
            for img in imgs:
                try:
                    src = img.get_attribute("src")
                    if src and ("scontent" in src or "instagram" in src):
                        width = img.size.get('width', 0)
                        if width > 200:
                            result["image_url"] = src
                            break
                except:
                    continue

            result["success"] = True

        # 2. Facebook Parsing
        elif result["platform"] == "Facebook":
            try:
                title_el = driver.find_element(By.CSS_SELECTOR, "div[dir='auto']")
                result["title"] = title_el.text.strip()[:150]
                result["content"] = title_el.text.strip()
            except:
                result["title"] = driver.title[:150] if driver.title else "Postingan Facebook"
                result["content"] = driver.title if driver.title else "Konten Facebook"

            try:
                author_el = driver.find_element(By.CSS_SELECTOR, "strong, span strong, a span")
                result["author"] = author_el.text.strip()
            except:
                result["author"] = "Facebook User"

            imgs = driver.find_elements(By.TAG_NAME, "img")
            for img in imgs:
                try:
                    src = img.get_attribute("src")
                    if src and ("fbcdn" in src or "facebook.com" in src):
                        width = img.size.get('width', 0)
                        if width > 250:
                            result["image_url"] = src
                            break
                except:
                    continue

            result["success"] = True

        # 3. YouTube Parsing
        elif result["platform"] == "YouTube":
            try:
                title_el = driver.find_element(By.CSS_SELECTOR, "h1.ytd-video-primary-info-renderer, h1.title yt-formatted-string")
                result["title"] = title_el.text.strip()
                result["content"] = f"Video YouTube dengan judul: {title_el.text.strip()}"
            except:
                result["title"] = driver.title.replace(" - YouTube", "") if driver.title else "Video YouTube"
                result["content"] = f"Konten rujukan dari link YouTube: {url}"

            video_id_match = re.search(r'(?:v=|\/)([a-zA-Z0-9_-]{11})', url)
            if video_id_match:
                video_id = video_id_match.group(1)
                result["image_url"] = f"https://img.youtube.com/vi/{video_id}/maxresdefault.jpg"
            else:
                imgs = driver.find_elements(By.TAG_NAME, "img")
                for img in imgs:
                    src = img.get_attribute("src")
                    if src and "ytimg.com" in src:
                        result["image_url"] = src
                        break

            result["author"] = "YouTube Video"
            result["success"] = True

        # 4. TikTok Parsing
        elif result["platform"] == "TikTok":
            result["title"] = driver.title[:150] if driver.title else "Video TikTok"
            result["content"] = driver.title if driver.title else "Konten Video TikTok"
            
            imgs = driver.find_elements(By.TAG_NAME, "img")
            for img in imgs:
                src = img.get_attribute("src")
                if src and ("tiktokcdn" in src or "p16-sign" in src):
                    result["image_url"] = src
                    break
            
            result["success"] = True

    except Exception as e:
        result["error"] = str(e)
    finally:
        if driver:
            driver.quit()

    return result

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"success": False, "error": "Missing URL argument"}))
        sys.exit(1)

    url_arg = sys.argv[1]
    res = parse_post(url_arg)
    print(json.dumps(res))
