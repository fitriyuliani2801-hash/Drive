import os
import subprocess

# Menjalankan pipeline utama dari folder main/main.py
script_path = os.path.join(os.path.dirname(__file__), "main", "main.py")
subprocess.run(["python", script_path])
