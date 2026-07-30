import mysql.connector
from mysql.connector import Error

def connect():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="drive"
        )
        return conn
    except Error as err:
        print(f"[ERROR] Error: {err}")
        return None

def simpan(platform, keyword, username, comment, url):
    conn = connect()
    if conn is None:
        return
    try:
        cursor = conn.cursor()
        
        # 1. Cari apakah ada artikel yang memiliki source_url ini
        cursor.execute("SELECT id FROM articles WHERE source_url = %s", (url,))
        row = cursor.fetchone()
        article_id = row[0] if row else None
        
        # 2. Insert ke social_comments agar tampil di website
        import hashlib
        import random
        comment_hash = hashlib.md5((str(article_id or '') + comment).encode('utf-8')).hexdigest()
        comment_id = f"scraped_{comment_hash[:12]}"
        
        # Cek duplikat
        cursor.execute("SELECT id FROM social_comments WHERE comment_id = %s", (comment_id,))
        if cursor.fetchone() is None:
            # Sederhana sentimen
            pos_words = ['bagus', 'sukses', 'mantap', 'keren', 'alhamdulillah', 'hebat', 'bantu', 'terima kasih', 'setuju', 'senang', 'positif', 'baik', '👍', '👏', 'luar biasa']
            neg_words = ['buruk', 'kecewa', 'gagal', 'jelek', 'parah', 'sulit', 'rugi', 'kesal', 'marah', 'bohong', 'hoax', 'palsu', 'penipuan', '👎']
            text_lower = comment.lower()
            pos_score = sum(1 for w in pos_words if w in text_lower)
            neg_score = sum(1 for w in neg_words if w in text_lower)
            sentiment = 'positif' if pos_score > neg_score else ('negatif' if neg_score > pos_score else 'netral')
            
            author_avatar = f"https://ui-avatars.com/api/?name={username[1:] if username.startswith('@') else username}&background=random"
            
            sql = """
            INSERT INTO social_comments 
            (comment_id, platform, article_id, author_name, author_avatar, raw_comment, sentiment, sentiment_score, status, posted_at, created_at, updated_at) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW(), NOW())
            """
            cursor.execute(sql, (comment_id, platform, article_id, username, author_avatar, comment, sentiment, 0.8 if sentiment != 'netral' else 0.5, 'approved'))
            conn.commit()
            print(f"[SUCCESS] Berhasil menyimpan komentar dari {username} ke database.")
            
            # Update article comment count
            if article_id:
                cursor.execute("SELECT COUNT(*) FROM social_comments WHERE article_id = %s AND status = 'approved' AND sentiment = 'positif'", (article_id,))
                pos_count = cursor.fetchone()[0]
                cursor.execute("SELECT COUNT(*) FROM social_comments WHERE article_id = %s AND status = 'approved' AND sentiment = 'negatif'", (article_id,))
                neg_count = cursor.fetchone()[0]
                cursor.execute("SELECT COUNT(*) FROM social_comments WHERE article_id = %s AND status = 'approved' AND sentiment = 'netral'", (article_id,))
                neu_count = cursor.fetchone()[0]
                
                cursor.execute("""
                    UPDATE articles 
                    SET positive_count = %s, negative_count = %s, neutral_count = %s 
                    WHERE id = %s
                """, (pos_count, neg_count, neu_count, article_id))
                conn.commit()
        
    except Error as e:
        print(f"[ERROR] Gagal menyimpan komentar: {e}")
    finally:
        conn.close()