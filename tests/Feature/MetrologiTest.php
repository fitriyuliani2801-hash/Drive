<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\HoaxDetectionService;
use App\Services\SentimentAnalysisService;
use App\Services\SocialUrlParserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetrologiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('METRO');
    }

    public function test_articles_index_is_accessible(): void
    {
        $response = $this->get('/berita');
        $response->assertStatus(200);
        $response->assertSee('Berita & Artikel Isu Perkotaan');
    }

    public function test_public_article_reader_shows_verdict_and_comments(): void
    {
        $article = Article::where('verdict', 'asli')->first();
        $response = $this->get('/berita/' . $article->slug);

        $response->assertStatus(200);
        $response->assertSee($article->title);
        $response->assertSee('BERITA ASLI (FAKTA)');
        $response->assertSee('Respon Positif');
    }

    public function test_hoax_detection_service(): void
    {
        $hoaxDetector = new HoaxDetectionService();
        $result = $hoaxDetector->detectHoax(
            'Kabar Bohong Sembako Gratis di Alun-Alun',
            'VIRAL! Beredar berita bohong dan hoaks pembagian uang tunai gratis tanpa pendaftaran.',
            'https://www.facebook.com/posts/hoax_palsu'
        );

        $this->assertEquals('hoaks', $result['verdict']);
        $this->assertGreaterThan(70, $result['verdict_score']);
    }

    public function test_sentiment_analysis_service(): void
    {
        $sentimentEngine = new SentimentAnalysisService();
        $posResult = $sentimentEngine->analyzeSentiment('Alhamdulillah program pelatihan QRIS ini sangat membantu pedagang dan mantap luar biasa!');
        $negResult = $sentimentEngine->analyzeSentiment('Bohong penipuan jelek banget macet dan meresahkan warga!');

        $this->assertEquals('positif', $posResult['sentiment']);
        $this->assertEquals('negatif', $negResult['sentiment']);
    }

    public function test_admin_can_import_and_publish_article_from_link(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/articles/import-link', [
            'url' => 'https://www.instagram.com/p/C_test_admin_auto_import_123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'source_url' => 'https://www.instagram.com/p/C_test_admin_auto_import_123',
        ]);
    }

    public function test_admin_dashboard_requires_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Redaksi Berita Metrologi');
    }
}
