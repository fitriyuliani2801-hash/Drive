<?php

namespace App\Console\Commands;

use App\Services\ViralNewsService;
use Illuminate\Console\Command;

class AutoPublishNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-publish-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menarik dan menerbitkan berita terkini seputar Kota Metro dari saluran media sosial & media publik';

    /**
     * Execute the console command.
     */
    public function handle(ViralNewsService $viralNewsService): int
    {
        $this->info('[SCHEDULER] Memulai penarikan berita & media sosial Kota Metro...');

        $result = $viralNewsService->autoPublishViralNews();

        $count = $result['published_count'] ?? 0;
        if ($count > 0) {
            $this->info("[SUKSES] Berhasil menerbitkan {$count} berita Kota Metro terbaru ke portal web!");
        } else {
            $this->info('[INFO] Tidak ada berita baru atau semua berita terkini sudah terbit di portal web.');
        }

        return Command::SUCCESS;
    }
}
