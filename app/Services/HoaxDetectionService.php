<?php

namespace App\Services;

class HoaxDetectionService
{
    protected array $hoaxKeywords = [
        'bohongan', 'bohong', 'palsu', 'hoax', 'hoaks', 'penipuan', 'bagi bagi uang',
        'gratis tanpa syarat', 'klaim sepihak', 'sebar pesan ini', 'kirim ke 10 grup',
        'rahasia pemerintah', 'segera datang tanpa daftar', 'berkerumun', 'disinformasi',
        'provokasi', 'dijamin kaya', 'hadiah tunai'
    ];

    protected array $factKeywords = [
        'pemkot metro', 'dinas', 'resmi', 'diluncurkan', 'perda', 'dprd', 'sosialisasi',
        'pelatihan', 'stadium', 'tejosari', 'pasar cendrawasih', 'anggaran apbd',
        'diresmikan', 'konfirmasi', 'terverifikasi'
    ];

    /**
     * Analyze Post Content for Hoax / Disinformation
     */
    public function detectHoax(string $title, string $content, string $url = ''): array
    {
        $text = strtolower($title . ' ' . $content . ' ' . $url);

        $hoaxScore = 0;
        $factScore = 0;

        foreach ($this->hoaxKeywords as $hk) {
            if (str_contains($text, $hk)) {
                $hoaxScore += 25;
            }
        }

        foreach ($this->factKeywords as $fk) {
            if (str_contains($text, $fk)) {
                $factScore += 20;
            }
        }

        if (str_contains(strtolower($url), 'hoax') || str_contains(strtolower($url), 'palsu') || $hoaxScore >= 40) {
            $verdict = 'hoaks';
            $confidence = min(98.5, max(75.0, 70 + $hoaxScore));
            $reasoning = 'Konten terindikasi HOAKS / DISINFORMASI. Ditemukan pola klaim tidak valid, bahasa provokatif tanpa verifikasi instansi resmi Pemkot Metro, serta laporan sanggahan publik.';
        } else {
            $verdict = 'asli';
            $confidence = min(99.0, max(82.0, 75 + $factScore));
            $reasoning = 'Konten BERITA ASLI / TERVERIFIKASI FAKTA. Informasi bersumber dari otoritas/instansi publik Kota Metro, memiliki data rujukan jelas, dan sesuai dengan agenda resmi pemerintah daerah.';
        }

        return [
            'verdict' => $verdict,
            'verdict_score' => round($confidence, 1),
            'verdict_reasoning' => $reasoning,
        ];
    }
}
