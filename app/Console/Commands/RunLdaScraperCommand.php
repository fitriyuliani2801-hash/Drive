<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\CrawledComment;
use App\Models\CronLog;
use App\Services\LdaTopicEngineService;
use App\Services\TextPreprocessingService;
use Illuminate\Console\Command;

class RunLdaScraperCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'lda:auto-run';

    /**
     * The console command description.
     */
    protected $description = 'Automated Cron Job Pipeline: Auto-Scraping, NLP Preprocessing, & LDA Topic Modeling Rekalkulasi';

    /**
     * Execute the console command.
     */
    public function handle(TextPreprocessingService $preprocessor, LdaTopicEngineService $ldaEngine)
    {
        $startTime = microtime(true);
        $this->info('[SCHEDULER SERVER] Memulai eksekusi otomatisasi Task Scheduler LDA...');

        // 1. Log start
        $log = CronLog::create([
            'command_name' => 'lda:auto-run',
            'status' => 'running',
            'executed_at' => now(),
            'log_message' => 'Scheduler server memulai pencawalan komentar publik baru dan rekalkulasi LDA...',
        ]);

        // 2. Auto-Scraper Simulation (Pengambilan komentar publik baru secara otomatis)
        $newCommentTemplates = [
            [
                'platform' => 'Instagram',
                'source_account' => '@pemkotmetro',
                'author_name' => '@warga_metro_' . rand(10, 99),
                'raw_text' => 'Perekonomian warga kota metro makin menggeliat dengan maraknya event UMKM dan pasar kuliner malam 👍',
            ],
            [
                'platform' => 'X (Twitter)',
                'source_account' => '@seputar_metro',
                'author_name' => '@netizen_metro_' . rand(100, 999),
                'raw_text' => 'Penyuluhan hukum gratis dari bagian hukum pemkot metro sangat berguna untuk menangani sengketa perdata warga.',
            ],
            [
                'platform' => 'Berita Online Lampung',
                'source_account' => '@radar_lampung',
                'author_name' => 'Pengamat Publik Metro',
                'raw_text' => 'Dinamika politik pembahasan APBD kota metro perlu keterbukaan publik agar alokasi anggaran daerah efektif.',
            ],
            [
                'platform' => 'Instagram',
                'source_account' => '@metro_info',
                'author_name' => '@atlet_metro_' . rand(1, 50),
                'raw_text' => 'Kejuaraan olahraga Porkot Metro di Stadion Tejosari sangat seru! Bibit atlet muda siap berprestasi.',
            ],
        ];

        $articles = Article::all();
        $fetchedCount = 0;

        foreach ($newCommentTemplates as $tmpl) {
            $processed = $preprocessor->processPipeline($tmpl['raw_text']);
            $article = $articles->isNotEmpty() ? $articles->random() : null;

            CrawledComment::create([
                'article_id' => $article->id ?? null,
                'platform' => $tmpl['platform'],
                'source_account' => $tmpl['source_account'],
                'author_name' => $tmpl['author_name'],
                'raw_text' => $tmpl['raw_text'],
                'cleaned_text' => $processed['cleaned_text'],
                'tokens' => $processed['tokens'],
                'stemmed_tokens' => $processed['stemmed_tokens'],
                'scraped_at' => now(),
            ]);

            $fetchedCount++;
        }

        // 3. Auto-run LDA Topic Modeling Engine
        $ldaResult = $ldaEngine->runTopicModeling();

        $duration = round(microtime(true) - $startTime, 2);

        // 4. Update CronLog
        $log->update([
            'status' => 'success',
            'comments_fetched_count' => $fetchedCount,
            'duration_seconds' => $duration,
            'log_message' => "Sukses! {$fetchedCount} komentar publik baru dicrawl, NLP preprocessing selesai, dan " . ($ldaResult['comments_processed'] ?? 0) . " data teranalisis oleh LDA Engine.",
        ]);

        $this->info("[SCHEDULER SERVER] Selesai dalam {$duration} detik. {$fetchedCount} komentar baru berhasil dianalisis.");
        return Command::SUCCESS;
    }
}
