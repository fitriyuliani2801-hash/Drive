import instaloader
import pandas as pd

ig = instaloader.Instaloader()

# PERHATIKAN BARIS INI: Variabelnya tetap 'target_akun', isinya yang diubah
target_akun = "craftyfi.id" 

print(f"Memulai koneksi ke Instagram...")
print(f"Sedang menyedot data dari profil: {target_akun}...")

profile = instaloader.Profile.from_username(ig.context, target_akun)

data = {
    "Username": [profile.username],
    "Nama Lengkap": [profile.full_name],
    "Jumlah Followers": [profile.followers],
    "Jumlah Postingan": [profile.mediacount],
    "Teks Bio": [profile.biography]
}

df = pd.DataFrame(data)
df.to_csv("data/instagram.csv", index=False, encoding='utf-8')

print("=====================================================")
print("SUKSES! Data berhasil disimpan ke data/instagram.csv")
print("=====================================================")