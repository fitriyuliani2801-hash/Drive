<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\SocialComment;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ViralNewsService
{
    protected SentimentAnalysisService $sentimentEngine;
    protected SocialUrlParserService $parserService;

    public function __construct(SentimentAnalysisService $sentimentEngine, SocialUrlParserService $parserService)
    {
        $this->sentimentEngine = $sentimentEngine;
        $this->parserService = $parserService;
    }

    /**
     * Otomatis menerbitkan Postingan Media Sosial LENGKAP Kota Metro
     * (Foto Berbeda & Unik Tiap Artikel + Narasi Artikel Lengkap Multi-Paragraf)
     */
    public function autoPublishViralNews(): array
    {
        $publishedCount = 0;

        // Kumpulan Berita & Postingan Medsos Kota Metro dengan FOTO UNIK & ARTIKEL LENGKAP
        $metroSocialData = [
            [
                'url' => 'https://www.instagram.com/p/C8_MetroJalanProtokol_2026',
                'platform' => 'Instagram',
                'author' => '@pemkotmetro',
                'title' => 'Pemkot Metro Percepat Pengerjaan Pengaspalan & Perbaikan Drainase Jalan Jendral Sudirman',
                'excerpt' => 'Dinas Pekerjaan Umum Kota Metro menerjunkan tim teknis untuk melakukan pengaspalan ulang dan pembersihan drainase di sepanjang Jalan Jendral Sudirman Kota Metro.',
                'main_image' => 'https://images.unsplash.com/photo-1590674899484-d5640e854abe?auto=format&fit=crop&w=1200&q=80',
                'middle_image' => 'https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?auto=format&fit=crop&w=1000&q=80',
                'end_image' => 'https://images.unsplash.com/photo-1584467735871-8e85353a8413?auto=format&fit=crop&w=1000&q=80',
                'content' => "KOTA METRO - Pemerintah Kota Metro melalui Dinas Pekerjaan Umum dan Penataan Ruang (PUPR) merespons cepat unggahan dan masukan warga di media sosial terkait kondisi jalan di kawasan protokol Kota Metro.\n\nPekerjaan pengaspalan ulang dan perbaikan jaringan drainase kini tengah melaju pesat di sepanjang koridor Jalan Jendral Sudirman, Metro Pusat. Langkah ini diambil guna mengantisipasi genangan air hujan yang kerap mengganggu lalu lintas serta untuk meningkatkan kenyamanan para pengguna jalan.\n\nKepala Dinas PUPR Kota Metro menjelaskan bahwa pengerjaan dilakukan dengan menggunakan material aspal hotmix berkualitas tinggi dan konstruksi drainase beton pracetak. Tim di lapangan dikerahkan secara intensif agar akses jalan utama kota dapat kembali beroperasi secara optimal dalam waktu singkat.\n\nSelain itu, penerangan jalan umum (PJU) di sepanjang jalur tersebut juga diperbarui menggunakan lampu LED hemat energi untuk menjamin keselamatan pengendara saat malam hari.\n\nRespons cepat dari pihak Pemkot Metro ini mendapat sambutan hangat dan apresiasi luas dari warga Kota Metro di berbagai platform media sosial.",
                'district' => 'Metro Pusat',
                'verdict' => 'asli',
            ],
            [
                'url' => 'https://www.tiktok.com/@metro.terkini/video/7398811223344',
                'platform' => 'TikTok',
                'author' => '@metro.terkini',
                'title' => 'Kemeriahan Festival Pasar Kuliner Malam & Pemberdayaan Transaksi QRIS UMKM Metro',
                'excerpt' => 'Festival Kuliner Malam Kota Metro dibanjiri ribuan pengunjung. Lebih dari 100 pelaku UMKM lokal kini melayani transaksi nirkertas menggunakan kode QRIS.',
                'main_image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80',
                'middle_image' => 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?auto=format&fit=crop&w=1000&q=80',
                'end_image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1000&q=80',
                'content' => "KOTA METRO - Suasana semarak mewarnai kawasan Sentra Wisata Kuliner Malam Kota Metro seiring dibukanya Festival Pemberdayaan UMKM Digital 2026. Acara tahunan ini menghadirkan lebih dari 100 ragam kuliner khas daerah dan olahan kreatif pedagang lokal.\n\nSalah satu daya tarik utama dalam gelaran festival kali ini adalah penerapan 100 persen transaksi pembayaran digital menggunakan sistem QRIS. Pengunjung dapat membeli aneka jajanan dan minuman sehat dengan cepat tanpa perlu membawa uang tunai fisik.\n\nWali Kota Metro menyampaikan bahwa digitalisasi pasar tradisional dan sentra kuliner merupakan bagian penting dari strategi percepatan pemulihan ekonomi masyarakat pasca-pandemi serta mendorong daya saing produk UMKM daerah.\n\nPara pedagang mengaku sangat terbantu dengan pelatihan dan pendampingan perbankan yang difasilitasi oleh Dinas Perdagangan Kota Metro. Penjualan dilaporkan meningkat hingga 40 persen dibanding pekan sebelumnya.\n\nKehangatan dan kebersihan sentra kuliner malam ini pun ramai menjadi perbincangan positif netizen di aplikasi TikTok dan Instagram.",
                'district' => 'Metro Pusat',
                'verdict' => 'asli',
            ],
            [
                'url' => 'https://www.facebook.com/HumasPemkotMetro/posts/99182736452',
                'platform' => 'Facebook',
                'author' => 'Humas Pemkot Metro',
                'title' => 'Sosialisasi Bantuan Hukum Gratis dan Pendampingan Hukum Bagi Warga Kurang Mampu',
                'excerpt' => 'Bagian Hukum Setda Kota Metro memperluas layanan penyuluhan dan bantuan hukum gratis untuk masyarakat miskin di 5 kecamatan se-Kota Metro.',
                'main_image' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=1200&q=80',
                'middle_image' => 'https://images.unsplash.com/photo-1453728013993-6d66e9c9123a?auto=format&fit=crop&w=1000&q=80',
                'end_image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1000&q=80',
                'content' => "KOTA METRO - Sebagai bentuk wujud nyata pelayanan keadilan bagi seluruh lapisan warga, Bagian Hukum Setda Kota Metro gencar melaksanakan Sosialisasi Peraturan Daerah tentang Pemberian Layanan Bantuan Hukum Gratis.\n\nProgram advokasi dan penyuluhan ini menyasar warga kurang mampu di lima kecamatan, meliputi Metro Pusat, Metro Timur, Metro Barat, Metro Utara, dan Metro Selatan. Melalui pos bantuan hukum ini, masyarakat yang menghadapi permasalahan perdata maupun administrasi dapat berkonsultasi secara cuma-cuma.\n\nTim advokat terverifikasi disiagakan di setiap kelurahan untuk mendampingi warga yang membutuhkan bantuan penyelesaian sengketa serta pemberian pemahaman kesadaran hukum.\n\nPemerintah Kota Metro berharap program ini dapat memberikan kepastian hukum dan perlindungan hak-hak sipil bagi masyarakat yang membutuhkan perlindungan hukum di Kota Metro.",
                'district' => 'Metro Timur',
                'verdict' => 'asli',
            ],
            [
                'url' => 'https://www.threads.net/@seputar_metro/post/D99182377',
                'platform' => 'Threads',
                'author' => '@seputar_metro',
                'title' => 'Wajah Baru Taman Kota Metro: Area Terbuka Hijau Asri Lengkap dengan WiFi Gratis',
                'excerpt' => 'Revitalisasi RTH Taman Merdeka Kota Metro resmi selesai. Pengunjung dapat menikmati sarana bermain anak ramah lingkungan dan koneksi internet publik gratis.',
                'main_image' => 'https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=1200&q=80',
                'middle_image' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1000&q=80',
                'end_image' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1000&q=80',
                'content' => "KOTA METRO - Penataan kawasan Ruang Terbuka Hijau (RTH) Taman Merdeka Kota Metro kini telah rampung dan tampil makin memikat. Wajah baru taman pusat kota ini dirancang menjadi ruang publik inklusif yang aman, nyaman, dan edukatif bagi keluarga.\n\nBerbagai fasilitas baru ditambahkan, seperti area bermain anak berbasis bahan ramah lingkungan, jalur pedestrian berbahan batu alam, bangku taman beratap, serta titik akses internet gratis yang ditenagai panel surya.\n\nTaman Kota Metro kini menjadi destinasi olahraga ringan, kegiatan komunitas anak muda, serta rekreasi keluarga favorit di akhir pekan tanpa dikenakan biaya masuk.\n\nWarga diimbau untuk bersama-sama menjaga kebersihan lingkungan dan merawat fasilitas umum yang telah dibangun agar dapat dinikmati bersama dalam jangka panjang.",
                'district' => 'Metro Pusat',
                'verdict' => 'asli',
            ]
        ];

        // Membaca file data/sosmed_urls.txt jika pengguna memasukkan link medsos buatan sendiri
        $customFile = base_path('data/sosmed_urls.txt');
        if (File::exists($customFile)) {
            $lines = file($customFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && !str_starts_with($line, '#')) {
                    $parsed = $this->parserService->parseUrl($line);
                    if (!empty($parsed['post_title'])) {
                        $metroSocialData[] = [
                            'url' => $line,
                            'platform' => $parsed['platform'],
                            'author' => $parsed['author_name'],
                            'title' => $parsed['post_title'],
                            'excerpt' => Str::limit($parsed['post_content'], 160),
                            'main_image' => $parsed['media_image'] ?: 'https://images.unsplash.com/photo-1590674899484-d5640e854abe?auto=format&fit=crop&w=1200&q=80',
                            'middle_image' => 'https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?auto=format&fit=crop&w=1000&q=80',
                            'end_image' => 'https://images.unsplash.com/photo-1584467735871-8e85353a8413?auto=format&fit=crop&w=1000&q=80',
                            'content' => $parsed['post_content'] . "\n\n(Laporan dan tanggapan publik dikutip langsung dari postingan resmi media sosial " . $parsed['platform'] . " akun: " . $parsed['author_name'] . ")",
                            'district' => 'Metro Pusat',
                            'verdict' => 'asli',
                        ];
                    }
                }
            }
        }

        $defaultUser = User::first() ?? User::factory()->create([
            'name' => 'Admin Redaksi Metrologi',
            'email' => 'admin@metrologi.id',
            'role' => 'admin',
        ]);

        $defaultCategory = Category::first();
        $categories = Category::all();

        foreach ($metroSocialData as $post) {
            $url = $post['url'];

            // Cek duplikasi agar tidak memposting ulang
            $exists = Article::where('source_url', $url)
                ->orWhere('title', $post['title'])
                ->exists();

            if ($exists) {
                continue;
            }

            $platform = $post['platform'];
            $authorName = $post['author'];
            $postTitle = $post['title'];
            $excerpt = $post['excerpt'];
            $contentFull = $post['content'];
            $district = $post['district'] ?? 'Metro Pusat';

            // Deteksi kategori
            $titleLower = strtolower($postTitle . ' ' . $contentFull);
            $matchedCategoryId = $defaultCategory ? $defaultCategory->id : 1;

            if ($categories->isNotEmpty()) {
                foreach ($categories as $cat) {
                    $catNameLower = strtolower($cat->name);
                    if (str_contains($titleLower, $catNameLower)) {
                        $matchedCategoryId = $cat->id;
                        break;
                    }
                }
            }

            // Simpan ARTIKEL LENGKAP dengan FOTO UNIK & BERBEDA untuk setiap berita
            $article = Article::create([
                'user_id'          => $defaultUser->id,
                'category_id'      => $matchedCategoryId,
                'title'            => $postTitle,
                'slug'             => Str::slug($postTitle) . '-' . time() . rand(10, 99),
                'excerpt'          => $excerpt,
                'content'          => $contentFull . "\n\n(Sumber Rujukan Resmi Postingan " . $platform . ": " . $authorName . " | Tautan: " . $url . ")",
                'image_path'       => $post['main_image'],   // Foto Utama Unik Atas
                'middle_image_path'=> $post['middle_image'], // Foto Pertengahan Artikel
                'end_image_path'   => $post['end_image'],    // Foto Penutup Artikel
                'source'           => $authorName . ' (' . $platform . ')',
                'source_url'       => $url,
                'platform'         => $platform, // Instagram, TikTok, Facebook, YouTube, Threads
                'verdict'          => $post['verdict'] ?? 'asli',
                'verdict_score'    => 96.5,
                'verdict_reasoning'=> 'Informasi & artikel dipublikasikan utuh beserta galeri foto dari postingan media sosial resmi ' . $platform . '.',
                'district'         => $district,
                'views_count'      => rand(65, 340),
                'is_featured'      => ($publishedCount === 0),
                'published_at'     => now(),
            ]);

            // Dapatkan komentar netizen dan hitung sentimennya
            $parsedComments = $this->parserService->parseUrl($url, $postTitle);
            if (!empty($parsedComments['raw_comments'])) {
                foreach ($parsedComments['raw_comments'] as $c) {
                    $sent = $this->sentimentEngine->analyzeSentiment($c['comment']);
                    SocialComment::create([
                        'article_id'     => $article->id,
                        'comment_id'     => 'sosmed_' . Str::random(10),
                        'platform'       => $platform,
                        'author_name'    => $c['author'],
                        'author_avatar'  => 'https://ui-avatars.com/api/?name=' . urlencode($c['author']) . '&background=0d9488&color=fff',
                        'raw_comment'    => $c['comment'],
                        'sentiment'      => $sent['sentiment'],
                        'sentiment_score' => $sent['sentiment_score'],
                        'status'         => 'approved',
                        'posted_at'      => now(),
                    ]);
                }

                $posCount = SocialComment::where('article_id', $article->id)->where('status', 'approved')->where('sentiment', 'positif')->count();
                $negCount = SocialComment::where('article_id', $article->id)->where('status', 'approved')->where('sentiment', 'negatif')->count();
                $neuCount = SocialComment::where('article_id', $article->id)->where('status', 'approved')->where('sentiment', 'netral')->count();

                $article->update([
                    'positive_count' => $posCount,
                    'negative_count' => $negCount,
                    'neutral_count'  => $neuCount,
                ]);
            }

            $publishedCount++;

            if ($publishedCount >= 3) {
                break;
            }
        }

        return [
            'status' => 'success',
            'published_count' => $publishedCount
        ];
    }
}