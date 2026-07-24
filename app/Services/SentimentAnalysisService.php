<?php

namespace App\Services;

class SentimentAnalysisService
{
    protected array $positiveWords = [
        'alhamdulillah', 'benefisial', 'bermanfaat', 'mantap', 'luar biasa', 'terima kasih',
        'membantu', 'naik pesat', 'bagus', 'laris', 'mengapresiasi', 'gampang', 'untung',
        'mendukung', 'jempolan', 'sukses', 'ramah', 'juara', 'apresiasi', 'senang', 'setuju',
        'mudah', 'hebat', 'solutif', 'terbaik', 'top', 'tertata', 'aman'
    ];

    protected array $negativeWords = [
        'bohong', 'penipuan', 'hoax', 'hoaks', 'bahaya', 'meresahkan', 'jelek', 'macet',
        'rusak', 'rugi', 'lambat', 'kecewa', 'parah', 'busuk', 'berantakan', 'merugikan',
        'kotor', 'malas', 'susah', 'buruk', 'gagal', 'parah', 'kecewa', 'antrean panjang',
        'sulit', 'keluhkan', 'parah'
    ];

    /**
     * Analyze Sentiment of Indonesian Public Comment
     */
    public function analyzeSentiment(string $commentText): array
    {
        $lowercase = mb_strtolower($commentText, 'UTF-8');

        $posScore = 0;
        $negScore = 0;

        foreach ($this->positiveWords as $pw) {
            if (str_contains($lowercase, $pw)) {
                $posScore += 2;
            }
        }

        foreach ($this->negativeWords as $nw) {
            if (str_contains($lowercase, $nw)) {
                $negScore += 2;
            }
        }

        if ($posScore > $negScore) {
            $sentiment = 'positif';
            $score = round(min(0.98, 0.60 + ($posScore * 0.1)), 2);
        } elseif ($negScore > $posScore) {
            $sentiment = 'negatif';
            $score = round(min(0.98, 0.60 + ($negScore * 0.1)), 2);
        } else {
            $sentiment = 'netral';
            $score = 0.50;
        }

        return [
            'sentiment' => $sentiment,
            'sentiment_score' => $score,
        ];
    }
}
