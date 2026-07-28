import pandas as pd

df = pd.read_csv("data/komentar.csv")

df = df.drop_duplicates()

df = df.dropna()

print(df.head())