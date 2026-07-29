<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\CrawledComment;
use App\Services\LdaTopicEngineService;
use App\Services\TextPreprocessingService;
use Illuminate\Database\Seeder;

class CrawledCommentSeeder extends Seeder
{
    public function run(): void
    {
        $preprocessor = new TextPreprocessingService();
        $articles = Article::all();

        $sampleComments = [];

        foreach ($sampleComments as $data) {
            $processed = $preprocessor->processPipeline($data['raw_text']);
            $article = $articles->random();

            CrawledComment::create([
                'article_id' => $article->id ?? null,
                'platform' => $data['platform'],
                'source_account' => $data['source_account'],
                'author_name' => $data['author_name'],
                'raw_text' => $data['raw_text'],
                'cleaned_text' => $processed['cleaned_text'],
                'tokens' => $processed['tokens'],
                'stemmed_tokens' => $processed['stemmed_tokens'],
                'scraped_at' => now()->subDays(rand(0, 10))->subHours(rand(1, 23)),
            ]);
        }

        // Run LDA Engine Service to analyze and cluster comments
        $ldaEngine = new LdaTopicEngineService($preprocessor);
        $ldaEngine->runTopicModeling();
    }
}
