<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SocialAnalysis;
use App\Models\SocialComment;
use App\Services\HoaxDetectionService;
use App\Services\SentimentAnalysisService;
use App\Services\SocialUrlParserService;
use Illuminate\Database\Seeder;

class SocialAnalysisSeeder extends Seeder
{
    public function run(): void
    {
        $parser = new SocialUrlParserService();
        $hoaxDetector = new HoaxDetectionService();
        $sentimentEngine = new SentimentAnalysisService();

        $categories = Category::all()->keyBy('slug');

        $sampleUrls = [];

        foreach ($sampleUrls as $url) {
            $parsed = $parser->parseUrl($url);
            $verdictData = $hoaxDetector->detectHoax($parsed['post_title'], $parsed['post_content'], $url);

            $posCount = 0;
            $negCount = 0;
            $neuCount = 0;

            // Pick category
            if (str_contains(strtolower($parsed['post_title']), 'umkm') || str_contains(strtolower($parsed['post_title']), 'ekonomi')) {
                $catId = $categories->get('ekonomi')->id ?? null;
            } elseif (str_contains(strtolower($parsed['post_title']), 'hukum') || str_contains(strtolower($parsed['post_title']), 'perda')) {
                $catId = $categories->get('hukum')->id ?? null;
            } elseif (str_contains(strtolower($parsed['post_title']), 'olahraga') || str_contains(strtolower($parsed['post_title']), 'porkot')) {
                $catId = $categories->get('olahraga')->id ?? null;
            } else {
                $catId = $categories->get('politik')->id ?? null;
            }

            $analysis = SocialAnalysis::create([
                'url' => $url,
                'platform' => $parsed['platform'],
                'author_name' => $parsed['author_name'],
                'post_title' => $parsed['post_title'],
                'post_content' => $parsed['post_content'],
                'verdict' => $verdictData['verdict'],
                'verdict_score' => $verdictData['verdict_score'],
                'verdict_reasoning' => $verdictData['verdict_reasoning'],
                'category_id' => $catId,
            ]);

            foreach ($parsed['raw_comments'] as $c) {
                $sent = $sentimentEngine->analyzeSentiment($c['comment']);

                SocialComment::create([
                    'social_analysis_id' => $analysis->id,
                    'author_name' => $c['author'],
                    'raw_comment' => $c['comment'],
                    'sentiment' => $sent['sentiment'],
                    'sentiment_score' => $sent['sentiment_score'],
                ]);

                if ($sent['sentiment'] === 'positif') $posCount++;
                elseif ($sent['sentiment'] === 'negatif') $negCount++;
                else $neuCount++;
            }

            $analysis->update([
                'positive_count' => $posCount,
                'negative_count' => $negCount,
                'neutral_count' => $neuCount,
            ]);
        }
    }
}
