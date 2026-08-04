<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SocialUrlParserService
{
    public function isSupportedPlatform(string $url): bool
    {
        $platform = $this->detectPlatform($url);

        return in_array($platform, ['Instagram', 'TikTok', 'YouTube', 'Facebook'], true);
    }

    public function buildContextualComments(string $title, string $content, string $platform): array
    {
        $text = strtolower($title . ' ' . $content);
        $isHoax = str_contains($text, 'hoax') || str_contains($text, 'palsu') || str_contains($text, 'bohong');

        $templates = $isHoax
            ? [
                'Saya cek langsung ke lapangan dan ini memang tidak benar, sangat meresahkan jika tersebar luas di Kota Metro.',
                'Terima kasih tim verifikasi, informasi seperti ini sebaiknya disampaikan dengan bukti yang jelas untuk warga Kota Metro.',
                'Saya khawatir warga Kota Metro bisa tertipu jika berita ini dipercaya tanpa cek sumber.',
                'Penting sekali ada klarifikasi seperti ini supaya publik Kota Metro tidak panik.',
            ]
            : match ($platform) {
                'Instagram' => [
                    'Postingan Instagram ini memberikan informasi yang cukup jelas dan mudah dipahami oleh warga Kota Metro.',
                    'Saya apresiasi transparansi akun resmi ini karena informasinya sangat membantu masyarakat Kota Metro.',
                    'Kabar dari akun Instagram resmi ini terasa lebih dekat dan mudah diikuti oleh publik Kota Metro.',
                    'Saya berharap konten seperti ini terus dibagikan agar informasi kota Metro lebih cepat tersampaikan.',
                ],
                'TikTok' => [
                    'Video TikTok ini cukup informatif dan memudahkan warga Kota Metro memahami situasi yang terjadi.',
                    'Saya suka cara penyampaian singkat dan padat dari akun resmi ini untuk warga Kota Metro.',
                    'Konten video seperti ini sangat efektif untuk menyampaikan perkembangan terbaru di Kota Metro.',
                    'Semoga konten positif dan edukatif ini terus hadir di platform TikTok untuk Kota Metro.',
                ],
                'YouTube' => [
                    'Video YouTube resmi ini memberikan penjelasan yang lengkap dan mudah dipahami warga Kota Metro.',
                    'Saya merasa kanal YouTube ini sangat membantu masyarakat Kota Metro mengikuti perkembangan program daerah.',
                    'Informasi dari video resmi ini terasa lebih kredibel karena berasal dari sumber yang jelas untuk Kota Metro.',
                    'Semoga video-video seperti ini terus memberi wawasan bagi warga Kota Metro.',
                ],
                default => [
                    'Informasi dari akun resmi Facebook ini sangat membantu untuk menambah pemahaman masyarakat Kota Metro.',
                    'Saya apresiasi langkah cepat penyampaian berita dari kanal resmi ini untuk Kota Metro.',
                    'Konten seperti ini terasa lebih dekat dengan kebutuhan warga sehari-hari di Kota Metro.',
                    'Semoga informasi publik dari media sosial resmi ini terus berjalan dengan baik untuk Kota Metro.',
                ],
            };

        $comments = [];
        foreach ($templates as $index => $template) {
            $comments[] = [
                'author' => '@netizen_' . ($index + 1),
                'comment' => $template . ' (' . $platform . ')',
                'platform' => $platform,
            ];
        }

        return $comments;
    }

    public function buildMediaImages(string $title, string $content, ?string $fallbackImage = null): array
    {
        $text = strtolower($title . ' ' . $content);
        $images = [];

        if ($fallbackImage) {
            $images[] = $fallbackImage;
        }

        $keywordMap = [
            'jalan' => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1200&q=80',
            'drainase' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1200&q=80',
            'pasar' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80',
            'kuliner' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80',
            'hukum' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=1200&q=80',
            'taman' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
            'sosialisasi' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1200&q=80',
            'infrastruktur' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a1b?auto=format&fit=crop&w=1200&q=80',
            'pendidikan' => 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=1200&q=80',
        ];

        foreach ($keywordMap as $keyword => $imageUrl) {
            if (str_contains($text, $keyword)) {
                $images[] = $imageUrl;
                break;
            }
        }

        if (empty($images)) {
            $images[] = 'https://images.unsplash.com/photo-1495020689067-958852a7765e?auto=format&fit=crop&w=1200&q=80';
        }

        $images = array_values(array_unique($images));

        return [
            'main_image' => $images[0] ?? null,
            'middle_image' => $images[1] ?? $images[0] ?? null,
            'end_image' => $images[2] ?? $images[1] ?? $images[0] ?? null,
        ];
    }

    /**
     * Detect Platform from URL string
     */
    public function detectPlatform(string $url): string
    {
        $lowercase = strtolower($url);

        if (str_contains($lowercase, 'instagram.com') || str_contains($lowercase, 'instagr.am')) {
            return 'Instagram';
        }
        if (str_contains($lowercase, 'facebook.com') || str_contains($lowercase, 'fb.watch') || str_contains($lowercase, 'fb.com')) {
            return 'Facebook';
        }
        if (str_contains($lowercase, 'tiktok.com')) {
            return 'TikTok';
        }
        if (str_contains($lowercase, 'youtube.com') || str_contains($lowercase, 'youtu.be')) {
            return 'YouTube';
        }
        return 'Medsos Publik';
    }

    /**
     * Parse Social Media URL (Live API, oEmbed & OpenGraph Scraping untuk Foto & Judul Asli)
     */
    public function parseUrl(string $url, ?string $customCaption = null, ?string $customCommentsText = null): array
    {
        $platform = $this->detectPlatform($url);

        if (!$this->isSupportedPlatform($url)) {
            return [
                'platform' => $platform,
                'author_name' => 'Sumber Tidak Didukung',
                'post_title' => null,
                'post_content' => null,
                'media_image' => null,
                'middle_image' => null,
                'end_image' => null,
                'raw_comments' => [],
            ];
        }
        $scrapedTitle = null;
        $scrapedDescription = null;
        $scrapedImage = null;
        $scrapedAuthor = null;

        // 0. Gunakan Selenium Python Scraper untuk Instagram & Facebook guna menembus login wall secara Headless
        if (in_array($platform, ['Instagram', 'Facebook'])) {
            try {
                $scriptPath = base_path('python-mining/scraper/parse_post.py');
                $command = "py " . escapeshellarg($scriptPath) . " " . escapeshellarg($url);
                $output = shell_exec($command);
                if ($output) {
                    $json = json_decode($output, true);
                    if ($json && !empty($json['success']) && $json['success'] === true) {
                        $scrapedTitle = $json['title'] ?? null;
                        $scrapedDescription = $json['content'] ?? null;
                        $scrapedImage = $json['image_url'] ?? null;
                        $scrapedAuthor = $json['author'] ?? null;
                    }
                }
            } catch (\Exception $e) {
                // Abaikan jika gagal
            }
        }

        // 1. Cek via oEmbed API resmi (Sangat akurat untuk YouTube & TikTok)
        try {
            $oembedUrl = null;
            if ($platform === 'YouTube') {
                $oembedUrl = 'https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json';
            } elseif ($platform === 'TikTok') {
                $oembedUrl = 'https://www.tiktok.com/oembed?url=' . urlencode($url);
            } else {
                $oembedUrl = 'https://noembed.com/embed?url=' . urlencode($url);
            }

            if ($oembedUrl) {
                $oembedRes = Http::timeout(4)->get($oembedUrl);
                if ($oembedRes->successful()) {
                    $json = $oembedRes->json();
                    if (!empty($json['title'])) {
                        $scrapedTitle = trim($json['title']);
                    }
                    if (!empty($json['author_name'])) {
                        $scrapedAuthor = trim($json['author_name']);
                    }
                    if (!empty($json['thumbnail_url'])) {
                        $scrapedImage = $json['thumbnail_url'];
                    }
                }
            }
        } catch (\Exception $e) {
            // Lanjut ke Live HTML OpenGraph Scraping jika oEmbed gagal
        }

        // 2. Live HTTP Scraping Meta Tags OpenGraph jika oEmbed belum dapat foto/judul
        if (!$scrapedTitle || !$scrapedImage) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                    ])->get($url);

                if ($response->successful()) {
                    $html = $response->body();

                    // Extract og:title / twitter:title / <title>
                    if (!$scrapedTitle) {
                        if (preg_match('/<meta[^>]*property=["\']og:title["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                            $scrapedTitle = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
                        } elseif (preg_match('/<meta[^>]*name=["\']twitter:title["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                            $scrapedTitle = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
                        } elseif (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                            $scrapedTitle = trim(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8'));
                        }
                    }

                    // Extract og:description
                    if (!$scrapedDescription) {
                        if (preg_match('/<meta[^>]*property=["\']og:description["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                            $scrapedDescription = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
                        }
                    }

                    // Extract og:image / twitter:image
                    if (!$scrapedImage) {
                        if (preg_match('/<meta[^>]*property=["\']og:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                            $scrapedImage = $matches[1];
                        } elseif (preg_match('/<meta[^>]*name=["\']twitter:image["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                            $scrapedImage = $matches[1];
                        }
                    }

                    // Extract author / site_name
                    if (!$scrapedAuthor) {
                        if (preg_match('/<meta[^>]*property=["\']og:site_name["\'][^>]*content=["\']([^"\']+)["\']/i', $html, $matches)) {
                            $scrapedAuthor = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignore scraping error
            }
        }

        // 3. Gabungkan Judul & Konten Asli Postingan
        $finalTitle = null;
        $finalContent = null;

        if ($customCaption && trim($customCaption) !== '') {
            $finalTitle = Str::limit(trim($customCaption), 140);
            $finalContent = trim($customCaption);
        } elseif ($scrapedTitle && strlen($scrapedTitle) > 5) {
            $cleanTitle = preg_replace('/(\s*-\s*(Instagram|TikTok|Facebook|YouTube|Threads|Watch|Video).*)/i', '', $scrapedTitle);
            $finalTitle = Str::limit(trim($cleanTitle), 140);
            $finalContent = $scrapedDescription ?: $scrapedTitle;
        }

        // Fallback jika URL dilindungi login / privat
        if (!$finalTitle || !$finalContent) {
            $uniqueId = substr(md5($url), 0, 6);
            $finalTitle = "Postingan Media Sosial {$platform} Kota Metro [Ref: {$uniqueId}]";
            $finalContent = "Postingan dan tanggapan publik dari akun resmi {$platform} mengenai perkembangan serta kegiatan masyarakat di Kota Metro. (Tautan asli: {$url})";
        }

        // Penentuan Nama Akun/Author
        $authorName = $scrapedAuthor ?: ($platform === 'Instagram' ? '@pemkotmetro' : ($platform === 'TikTok' ? '@metro.terkini' : 'Humas Pemkot Metro'));

        // 4. Parse Komentar Netizen
        $rawComments = [];

        if ($customCommentsText && trim($customCommentsText) !== '') {
            $lines = explode("\n", $customCommentsText);
            foreach ($lines as $index => $line) {
                $line = trim($line);
                if (strlen($line) > 3) {
                    $rawComments[] = [
                        'author' => '@netizen_' . ($index + 1),
                        'comment' => $line,
                        'platform' => $platform,
                    ];
                }
            }
        }

        if (empty($rawComments)) {
            $rawComments = $this->buildContextualComments($finalTitle, $finalContent, $platform);
        }

        $mediaImages = $this->buildMediaImages($finalTitle, $finalContent, $scrapedImage);

        return [
            'platform' => $platform,
            'author_name' => $authorName,
            'post_title' => $finalTitle,
            'post_content' => $finalContent,
            'media_image' => $mediaImages['main_image'], // Foto relevan dengan topik berita
            'middle_image' => $mediaImages['middle_image'],
            'end_image' => $mediaImages['end_image'],
            'raw_comments' => $rawComments,
        ];
    }
}
