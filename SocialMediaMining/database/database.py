import mysql.connector

try:
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="metrologi"
    )

    if conn.is_connected():
        print("✅ Berhasil terhubung ke database!")

except mysql.connector.Error as err:
    print(f"❌ Error: {err}")

    def simpan(platform, keyword, username, comment, url):

    cursor = conn.cursor()

    sql = """
    INSERT INTO comments
    (platform,keyword,username,comment,post_url)

    VALUES(%s,%s,%s,%s,%s)
    """

    cursor.execute(sql,(platform,keyword,username,comment,url))

    conn.commit()