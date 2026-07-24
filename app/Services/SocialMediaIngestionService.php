<?php

namespace App\Services;

use App\Models\Article;
use App\Models\SocialComment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialMediaIngestionService
{
    protected SentimentAnalysisService $sentimentEngine;

    /**
     * Keywords for Ingestion
     */
    protected array $defaultKeywords = [
        'Kota Metro',
        '#MetroLampung',
        '#PemkotMetro',
        '@infokotametro',
        'Metro Timur',
        'Metro Pusat',
        'Tejosari',
    ];

    /**
     * Blacklist Database for Hate Speech / Bad Words Filtering (Fase 2b)
     */
    protected array $badWords = [
        'anjing', 'babi', 'bangsat', 'kontol', 'memek', 'jancok', 'asu',
        'penipu', 'tolol', 'goblok', 'gila', 'kampret', 'goblok', 'bajingan'
    ];

    /**
     * Relevancy Keywords for Kota Metro Issue Filter (Fase 2c)
     */
    protected array $relevancyKeywords = [
        'metro', 'lampung', 'pemkot', 'walikota', 'tejosari', 'pasar',
        'sekolah', 'jalan', 'porkot', 'apbd', 'umkm', 'bantuan', 'perda', 'taman'
    ];

    public function __construct(SentimentAnalysisService $sentimentEngine)
    {
        $this->sentimentEngine = $sentimentEngine;
    }

    /**
     * Execute Ingestion Algorithm (Fase 1) & Filtering Algorithm (Fase 2)
     */
    public function runIngestion(array $keywords = [], ?int $articleId = null): array
    {
        $keywords = !empty($keywords) ? $keywords : $this->defaultKeywords;
        $platforms = ['Instagram', 'X', 'Facebook', 'YouTube'];

        $totalIngested = 0;
        $totalApproved = 0;
        $totalSpam = 0;
        $totalSkipped = 0;

        Log::info("Starting Social Media Ingestion Job for Keywords: " . implode(', ', $keywords));

        $articles = $articleId 
            ? Article::where('id', $articleId)->get() 
            : Article::latest()->take(10)->get();

        foreach ($articles as $article) {
            foreach ($platforms as $platform) {
                try {
                    // FASE 1: Ingestion API Simulation / Remote Request
                    $rawComments = $this->fetchRawCommentsFromPlatform($platform, $article, $keywords);

                    foreach ($rawComments as $raw) {
                        $totalIngested++;

                        // FASE 2a: Deduplication Check
                        $exists = SocialComment::where('comment_id', $raw['comment_id'])->exists();
                        if ($exists) {
                            $totalSkipped++;
                            continue;
                        }

                        // FASE 2b: Filter Ujaran Kebencian / Bad Words
                        $isBadWord = $this->containsBadWords($raw['raw_comment']);
                        if ($isBadWord) {
                            $totalSpam++;
                            SocialComment::create([
                                'article_id' => $article->id,
                                'comment_id' => $raw['comment_id'],
                                'platform' => $platform,
                                'author_name' => $raw['author_name'],
                                'author_avatar' => $raw['author_avatar'],
                                'raw_comment' => $raw['raw_comment'],
                                'sentiment' => 'negatif',
                                'sentiment_score' => 0.95,
                                'status' => 'spam',
                                'posted_at' => $raw['posted_at'],
                            ]);
                            continue;
                        }

                        // FASE 2c: Filter Relevansi Wilayah / Isu Kota Metro
                        $isRelevan = $this->isRelevanToMetro($raw['raw_comment'], $article->title);
                        $status = $isRelevan ? 'approved' : 'pending';

                        // AI Sentiment Analysis
                        $sent = $this->sentimentEngine->analyzeSentiment($raw['raw_comment']);

                        // Save Approved / Pending Comment
                        SocialComment::create([
                            'article_id' => $article->id,
                            'comment_id' => $raw['comment_id'],
                            'platform' => $platform,
                            'author_name' => $raw['author_name'],
                            'author_avatar' => $raw['author_avatar'],
                            'raw_comment' => $raw['raw_comment'],
                            'sentiment' => $sent['sentiment'],
                            'sentiment_score' => $sent['sentiment_score'],
                            'status' => $status,
                            'posted_at' => $raw['posted_at'],
                        ]);

                        if ($status === 'approved') {
                            $totalApproved++;
                        }
                    }

                    // Recalculate Article Counters
                    $posCount = SocialComment::where('article_id', $article->id)->where('status', 'approved')->where('sentiment', 'positif')->count();
                    $negCount = SocialComment::where('article_id', $article->id)->where('status', 'approved')->where('sentiment', 'negatif')->count();
                    $neuCount = SocialComment::where('article_id', $article->id)->where('status', 'approved')->where('sentiment', 'netral')->count();

                    $article->update([
                        'positive_count' => $posCount,
                        'negative_count' => $negCount,
                        'neutral_count' => $neuCount,
                    ]);

                } catch (\Exception $e) {
                    Log::error("Failed ingestion for platform {$platform} on article #{$article->id}: " . $e->getMessage());
                }
            }
        }

        return [
            'total_ingested' => $totalIngested,
            'total_approved' => $totalApproved,
            'total_spam' => $totalSpam,
            'total_skipped' => $totalSkipped,
        ];
    }

    /**
     * Simulated Raw Comments Generator (Fase 1 API Response)
     */
    protected function fetchRawCommentsFromPlatform(string $platform, Article $article, array $keywords): array
    {
        $platformPrefix = strtolower(substr($platform, 0, 2));
        $comments = [];

        $sampleTemplates = [
            'Instagram' => [
                ['author' => '@warga_metro_selatan', 'text' => 'Program penguatan UMKM Kota Metro ini sangat membantu pedagang kecil di sentra kuliner. Lanjutkan!'],
                ['author' => '@pedagang_tejosari', 'text' => 'Semoga perbaikan fasilitas di Kota Metro bisa cepat selesai dan bebas dari macet.'],
                ['author' => '@pemuda_metro_timur', 'text' => 'Porkot Metro 2026 seru banget, banyak atlet berbakat dari seluruh kecamatan Metro!'],
            ],
            'X' => [
                ['author' => '@metro_news_net', 'text' => 'Update terkini mengenai bantuan hukum gratis dari Bagian Hukum Pemkot Metro untuk warga kurang mampu.'],
                ['author' => '@info_metrolampung', 'text' => 'Apresiasi Pemkot Metro yang tanggap mendengar keluhan warga di media sosial.'],
            ],
            'Facebook' => [
                ['author' => 'Budi Susanto (Metro Pusat)', 'text' => 'Alhamdulillah pelayanan publik di Kota Metro makin cepat dan ramah.'],
                ['author' => 'Siti Nurhaliza', 'text' => 'Mohon penerangan jalan umum di daerah Metro Barat diperbanyak agar aman di malam hari.'],
            ],
            'YouTube' => [
                ['author' => 'Metro TV Official Channel', 'text' => 'Liputan khusus pembangunan fasilitas olahraga dan infrastruktur perkotaan Kota Metro.'],
            ]
        ];

        $pool = $sampleTemplates[$platform] ?? [];

        foreach ($pool as $idx => $item) {
            $uniqueHash = substr(md5($article->id . $platform . $item['text']), 0, 8);

            $comments[] = [
                'comment_id' => $platformPrefix . '_' . $uniqueHash,
                'author_name' => $item['author'],
                'author_avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($item['author']) . '&background=random',
                'raw_comment' => $item['text'],
                'posted_at' => now()->subMinutes(rand(5, 300)),
            ];
        }

        return $comments;
    }

    /**
     * Check Hate Speech / Bad Words (Fase 2b)
     */
    protected function containsBadWords(string $text): bool
    {
        $lower = mb_strtolower($text, 'UTF-8');
        foreach ($this->badWords as $bw) {
            if (str_contains($lower, $bw)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check Region / Issue Relevancy (Fase 2c)
     */
    protected function isRelevanToMetro(string $text, string $articleTitle): bool
    {
        $lower = mb_strtolower($text . ' ' . $articleTitle, 'UTF-8');
        foreach ($this->relevancyKeywords as $rk) {
            if (str_contains($lower, $rk)) {
                return true;
            }
        }
        return false;
    }
}
