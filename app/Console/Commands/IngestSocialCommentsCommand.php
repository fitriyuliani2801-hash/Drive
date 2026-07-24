<?php

namespace App\Console\Commands;

use App\Services\SocialMediaIngestionService;
use Illuminate\Console\Command;

class IngestSocialCommentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:ingest {--article= : Specific article ID to run ingestion for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest & Filter Social Media Comments for Kota Metro News (Fase 1 Ingestion & Fase 2 Filtering Algorithm)';

    /**
     * Execute the console command.
     */
    public function handle(SocialMediaIngestionService $ingestionService): int
    {
        $this->info('Starting Social Media Comments Ingestion Job (Instagram, X, Facebook, YouTube)...');

        $articleId = $this->option('article') ? (int) $this->option('article') : null;

        $result = $ingestionService->runIngestion([], $articleId);

        $this->info("Ingestion completed successfully!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Comments Ingested', $result['total_ingested']],
                ['Comments Approved (Clean & Relevan)', $result['total_approved']],
                ['Comments Flagged as Spam (Bad Words)', $result['total_spam']],
                ['Comments Skipped (Duplicate ID)', $result['total_skipped']],
            ]
        );

        return Command::SUCCESS;
    }
}
