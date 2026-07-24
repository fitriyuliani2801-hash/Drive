<?php

namespace App\Services;

class TextPreprocessingService
{
    /**
     * Indonesian Stopwords List (Tabel Kata Koba/Stopword Bahasa Indonesia)
     */
    protected array $stopwords = [
        'yang', 'di', 'ke', 'dari', 'ini', 'itu', 'dan', 'atau', 'untuk', 'pada',
        'adalah', 'bahwa', 'oleh', 'dengan', 'karena', 'dapat', 'sudah', 'lebih',
        'akan', 'bisa', 'ada', 'kita', 'saya', 'kami', 'mereka', 'anda', 'kamu',
        'ia', 'dia', 'bila', 'jika', 'sebab', 'maka', 'serta', 'hingga', 'sampai',
        'seperti', 'yaitu', 'yakni', 'sebagai', 'bagi', 'tentang', 'terhadap',
        'melalui', 'secara', 'harus', 'agar', 'supaya', 'sehingga', 'mana',
        'bagaimana', 'mengapa', 'apa', 'siapa', 'kapan', 'dimana', 'saja',
        'pun', 'lah', 'kah', 'tah', 'jadi', 'bahkan', 'malahan', 'namun',
        'tetapi', 'melainkan', 'hanya', 'cuma', 'sekedar', 'lagi', 'pula',
        'paling', 'sangat', 'amat', 'sekali', 'banyak', 'sedikit', 'kurang',
        'juga', 'kok', 'sih', 'deh', 'dong', 'nih', 'tuh', 'iya', 'tidak',
        'bukan', 'belum', 'pernah', 'selalu', 'sering', 'jarang', 'biasa',
        'mengapa', 'bagaimanakah', 'apakah', 'siapakah', 'tersebut', 'setiap',
        'sebuah', 'suatu', 'para', 'para-para', 'berbagai', 'antar', 'antara'
    ];

    /**
     * Common Indonesian Stemming Patterns (Afiksasi: Awalan, Akhiran, Kombinasi)
     */
    protected array $stemRules = [
        // Kombinasi Khusus Isu Publik
        '/^pemerintah(an)?$/' => 'perintah',
        '/^pembangunan$/' => 'bangun',
        '/^pelayanan$/' => 'layan',
        '/^kebijakan$/' => 'bijak',
        '/^perdagangan$/' => 'dagang',
        '/^penegakan$/' => 'tegak',
        '/^kejuaraan$/' => 'juara',
        '/^keolahragaan$/' => 'olahraga',
        '/^penguatan$/' => 'kuat',
        '/^pendampingan$/' => 'damping',
        '/^pendidikan$/' => 'didik',
        '/^pengawasan$/' => 'awas',
        '/^pelanggaran$/' => 'langgar',
        '/^perekonomian$/' => 'ekonomi',
        '/^pertumbuhan$/' => 'tumbuh',
        '/^peningkatan$/' => 'tingkat',
        '/^pembuka(an)?$/' => 'buka',
        '/^pelaksanaan$/' => 'laksana',
        '/^penataan$/' => 'tata',
        '/^pertanggungjawaban$/' => 'tanggungjawab',

        // Infleksi & Awalan Umum
        '/^(mem|meng|meny|men|m)(ber|per)?/' => '',
        '/^(ber|bel|be)/' => '',
        '/^(di|ter|ke)/' => '',
        '/^(pe|per|se)/' => '',
        
        // Akhiran (Suffixes)
        '/(kan|an|i)$/' => '',
    ];

    /**
     * Langkah 2.1: Case Folding & Noise Removal
     * Mengubah seluruh huruf menjadi lowercase dan menghapus URL/mention/hashtag/karakter khusus/angka.
     */
    public function cleanNoise(string $text): string
    {
        // Case folding (lowercase)
        $cleaned = mb_strtolower($text, 'UTF-8');

        // Remove URLs
        $cleaned = preg_replace('/https?:\/\/\S+/i', '', $cleaned);

        // Remove Mentions (@user) & Hashtags (#tag)
        $cleaned = preg_replace('/[@#]\w+/', '', $cleaned);

        // Remove Emojis & non-alphanumeric characters
        $cleaned = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $cleaned);

        // Remove Digits
        $cleaned = preg_replace('/\d+/', '', $cleaned);

        // Collapse multiple spaces
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        return trim($cleaned);
    }

    /**
     * Langkah 2.2 & 2.3: Tokenization & Stopword Removal
     * Memecah kalimat menjadi token kata dan menyaring kata-kata umum tanpa makna mendasar.
     */
    public function tokenizeAndRemoveStopwords(string $cleanedText): array
    {
        $words = explode(' ', $cleanedText);
        $filtered = [];

        foreach ($words as $word) {
            $w = trim($word);
            if (mb_strlen($w) > 2 && !in_array($w, $this->stopwords)) {
                $filtered[] = $w;
            }
        }

        return array_values($filtered);
    }

    /**
     * Langkah 2.4: Indonesian Stemming
     * Mengubah kata berimbuhan ke bentuk kata dasarnya.
     */
    public function stemTokens(array $tokens): array
    {
        $stemmed = [];

        foreach ($tokens as $token) {
            $word = $token;
            foreach ($this->stemRules as $pattern => $replacement) {
                if (preg_match($pattern, $word)) {
                    $replaced = preg_replace($pattern, $replacement, $word);
                    if (mb_strlen($replaced) >= 3) {
                        $word = $replaced;
                    }
                    break;
                }
            }
            $stemmed[] = $word;
        }

        return $stemmed;
    }

    /**
     * Eksekusi Pipeline Preprocessing Teks Utuh (Langkah 2)
     */
    public function processPipeline(string $rawText): array
    {
        $cleaned = $this->cleanNoise($rawText);
        $tokens = $this->tokenizeAndRemoveStopwords($cleaned);
        $stemmed = $this->stemTokens($tokens);

        return [
            'cleaned_text' => $cleaned,
            'tokens' => $tokens,
            'stemmed_tokens' => $stemmed,
        ];
    }
}
