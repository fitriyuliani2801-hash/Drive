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

        // 2. Import komentar dari file CSV (data/komentar.csv) hasil scraping
        $csvPath = base_path('data/komentar.csv');
        $fetchedCount = 0;

        if (file_exists($csvPath) && ($handle = fopen($csvPath, 'r')) !== false) {
            $header = fgetcsv($handle); // Read header row
            $articles = Article::all();

            while (($row = fgetcsv($handle)) !== false) {
                if (empty($row)) continue;
                
                // Determine platform & raw_text based on CSV columns
                $platform = isset($row[0]) && in_array($row[0], ['Instagram', 'TikTok', 'YouTube', 'Facebook', 'Media Sosial']) ? $row[0] : 'Media Sosial';
                $rawText = end($row); // Take comment text

                if (empty(trim($rawText)) || strlen(trim($rawText)) < 3) continue;

                // Check deduplication in crawled_comments
                $exists = CrawledComment::where('raw_text', trim($rawText))->exists();
                if (!$exists) {
                    $processed = $preprocessor->processPipeline($rawText);
                    $article = $articles->isNotEmpty() ? $articles->random() : null;

                    CrawledComment::create([
                        'article_id' => $article->id ?? null,
                        'platform' => $platform,
                        'source_account' => '@medsos_metro',
                        'author_name' => '@netizen_metro',
                        'raw_text' => trim($rawText),
                        'cleaned_text' => $processed['cleaned_text'],
                        'tokens' => $processed['tokens'],
                        'stemmed_tokens' => $processed['stemmed_tokens'],
                        'scraped_at' => now(),
                    ]);

                    $fetchedCount++;
                }
            }
            fclose($handle);
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
