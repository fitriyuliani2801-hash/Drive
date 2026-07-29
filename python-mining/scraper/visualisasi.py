import pandas as pd
import matplotlib.pyplot as plt

# 1. Kita buat data buatan sederhana yang memiliki kolom angka (like)
data = {
    "nama": ["Andi", "Budi", "Citra", "Dewi"],
    "komentar": ["Produknya bagus", "Lumayan", "Biasa saja", "Sangat merekomendasikan!"],
    "like": [15, 8, 2, 30]  # <--- Nah, sekarang kolom "like" ini ada angkanya!
}

# 2. Ubah data tersebut menjadi format tabel Pandas
df = pd.DataFrame(data)

# 3. Jadikan kolom 'nama' sebagai label di bagian bawah grafik
df.set_index("nama", inplace=True)

# 4. Buat grafik batang (bar chart) khusus untuk kolom 'like'
df["like"].plot(kind="bar", color="skyblue")

# 5. Tambahkan judul agar rapi
plt.title("Grafik Jumlah Like per Komentar")
plt.xlabel("Nama Pengguna")
plt.ylabel("Jumlah Like")
plt.xticks(rotation=0) # Agar teks nama di bawah tidak miring

# 6. Munculkan jendela grafiknya!
plt.show()