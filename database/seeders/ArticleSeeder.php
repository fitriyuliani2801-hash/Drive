<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\SocialComment;
use App\Models\User;
use App\Services\SentimentAnalysisService;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $econ = Category::where('slug', 'ekonomi')->first();
        $hukum = Category::where('slug', 'hukum')->first();
        $politik = Category::where('slug', 'politik')->first();
        $olahraga = Category::where('slug', 'olahraga')->first();
        $sentimentEngine = new SentimentAnalysisService();

        $sampleArticles = [
            [
                'title' => 'Penguatan Ekosistem UMKM dan Pasar Digital Sentra Kuliner Kota Metro 2026',
                'slug' => 'penguatan-ekosistem-umkm-pasar-digital-kota-metro-2026',
                'category_id' => $econ->id ?? 1,
                'excerpt' => 'Pemerintah Kota Metro mendorong digitalisasi bagi 500 pelaku UMKM lokal untuk memperluas akses pasar dan permodalan.',
                'content' => "Kota Metro Lampung terus memacu pertumbuhan ekonomi daerah melalui penguatan ekosistem Usaha Mikro, Kecil, dan Menengah (UMKM). Dalam program inovasi terbaru, Pemkot Metro bekerja sama dengan perbankan nasional memberikan pendampingan transaksi digital QRIS dan pelatihan manajemen keuangan.\n\nLangkah ini terbukti meningkatkan omzet penjualan produk olahan pangan dan kerajinan khas Kota Metro hingga 35 persen dalam kurun waktu satu semester terakhir.",
                'source' => 'Redaksi Ekonomi Metrologi',
                'district' => 'Metro Pusat',
                'platform' => 'Instagram',
                'verdict' => 'asli',
                'verdict_score' => 96.5,
                'verdict_reasoning' => 'Berita terverifikasi FAKTA. Program pelatihan QRIS dan fasilitasi UMKM ini resmi diluncurkan oleh Dinas Perdagangan Kota Metro dan terkonfirmasi laporan pelaksanaan lapangan.',
                'is_featured' => true,
                'published_at' => now()->subDays(1),
                'comments' => [
                    ['author' => '@rudi_kuliner', 'comment' => 'Alhamdulillah program ini sangat bermanfaat sekali untuk pedagang kecil seperti kami, omzet jadi naik!'],
                    ['author' => '@anisa_metro', 'comment' => 'Mantap banget Pemkot Metro, transaksi QRIS jadi gampang dan jualan makin laris 👍🔥'],
                    ['author' => '@hendra_pasar', 'comment' => 'Semoga pelatihan digital UMKM ini terus berlanjut ke seluruh kecamatan lain di kota metro.'],
                    ['author' => '@dewa_kecewa', 'comment' => 'Antreannya tadi agak panjang pas pendaftaran, mohon diperbaiki teknisnya.'],
                ],
            ],
            [
                'title' => 'Sosialisasi Perda Penyuluhan Bantuan Hukum Gratis Bagi Warga Kurang Mampu',
                'slug' => 'sosialisasi-perda-penyuluhan-bantuan-hukum-gratis-kota-metro',
                'category_id' => $hukum->id ?? 2,
                'excerpt' => 'Bagian Hukum Setda Kota Metro memberikan pendampingan dan penyuluhan kesadaran hukum bagi masyarakat di 5 kecamatan.',
                'content' => "Dalam upaya memberikan kepastian dan keadilan hukum bagi seluruh lapisan masyarakat, Bagian Hukum Bagian Administrasi Pemerintahan Kota Metro secara intensif menggelar penyuluhan Peraturan Daerah (Perda) tentang Layanan Bantuan Hukum Gratis.\n\nMelalui program ini, warga yang membutuhkan konsultasi perdata maupun sengketa lahan dapat mengakses pos penyuluhan hukum tanpa dipungut biaya.",
                'source' => 'Bagian Hukum Setda Kota Metro',
                'district' => 'Metro Timur',
                'platform' => 'Facebook',
                'verdict' => 'asli',
                'verdict_score' => 94.0,
                'verdict_reasoning' => 'Berita terverifikasi FAKTA. Kegiatan penyuluhan Perda Bantuan Hukum diawasi langsung oleh Bagian Hukum Setda Kota Metro.',
                'is_featured' => true,
                'published_at' => now()->subDays(3),
                'comments' => [
                    ['author' => 'Ahmad Pengacara', 'comment' => 'Sangat mengapresiasi adanya penyuluhan hukum gratis dari Bagian Hukum Pemkot Metro untuk warga kurang mampu.'],
                    ['author' => 'Siti Rahmawati', 'comment' => 'Konsultasi bantuan hukum gratis sangat membantu masyarakat yang awam aturan hukum perdata.'],
                    ['author' => 'Dedi Warga', 'comment' => 'Pentingnya sosialisasi aturan Perda ketertiban agar tidak ada pelanggaran di kawasan umum.'],
                ],
            ],
            [
                'title' => 'Klarifikasi Isu Pembagian Sembako Gratis Tanpa Syarat di Alun-Alun Metro',
                'slug' => 'klarifikasi-isu-pembagian-sembako-gratis-alun-alun-metro',
                'category_id' => $politik->id ?? 3,
                'excerpt' => 'Beredar kabar bohong dan hoaks yang mengatasnamakan Pemkot Metro mengenai pembagian paket sembako gratis tanpa pendaftaran.',
                'content' => "Beredar kabar burung yang menyebutkan bahwa Pemerintah Kota Metro membagikan paket sembako gratis senilai Rp 1 juta rupiah di Alun-Alun Kota Metro. Dinas Kominfo Kota Metro secara tegas menyatakan informasi tersebut adalah HOAKS dan DISINFORMASI.\n\nWarga diimbau untuk selalu menyaring keaslian berita dan tidak terprovokasi pesan berantai yang tidak jelas sumber resminya.",
                'source' => 'Diskominfo Kota Metro',
                'district' => 'Metro Pusat',
                'platform' => 'Instagram',
                'verdict' => 'hoaks',
                'verdict_score' => 98.0,
                'verdict_reasoning' => 'PERINGATAN HOAKS / DISINFORMASI. Informasi pembagian sembako tanpa syarat ini adalah hoaks penipuan yang telah disanggah resmi oleh Dinas Kominfo Kota Metro.',
                'is_featured' => false,
                'published_at' => now()->subDays(5),
                'comments' => [
                    ['author' => '@budi_warga', 'comment' => 'Bohong banget ini! Saya tadi ke alun-alun tidak ada pembagian apa-apa, hoax penipuan meresahkan!'],
                    ['author' => '@siti_metro', 'comment' => 'Wah bahaya berita begini bikin warga berkerumun kasihan yang udah datang jauh-jauh.'],
                    ['author' => '@diki_kritik', 'comment' => 'Tolong Satpol PP usut pembuat kabar bohong ini sangat meresahkan.'],
                    ['author' => '@hendra_99', 'comment' => 'Terima kasih informasinya min, hampir aja saya percaya hoax ini.'],
                ],
            ],
            [
                'title' => 'Pekan Olahraga Kota (Porkot) Metro 2026 Resmi Dibuka di Stadion Tejosari',
                'slug' => 'pekan-olahraga-kota-porkot-metro-2026-resmi-dibuka-stadion-tejosari',
                'category_id' => $olahraga->id ?? 4,
                'excerpt' => 'Ratusan atlet bertalenta dari 5 kecamatan siap berlaga di 12 cabang olahraga kejuaraan Porkot Metro.',
                'content' => "Semangat kompetisi olahraga melingkupi Kota Metro seiring dibukanya Pekan Olahraga Kota (Porkot) Metro 2026 di Stadion Tejosari. Ajang tahunan ini mempertandingkan 12 cabang olahraga unggulan seperti sepak bola, bulu tangkis, atletik, dan pencak silat.\n\nWali Kota Metro mengapresiasi tinggi antusiasme para atlet muda daerah dan berharap kejuaraan ini mampu melahirkan bibit-bibit olahraga berprestasi hingga tingkat nasional.",
                'source' => 'KONI & Redaksi Olahraga Metrologi',
                'district' => 'Metro Selatan',
                'platform' => 'TikTok',
                'verdict' => 'asli',
                'verdict_score' => 97.5,
                'verdict_reasoning' => 'Berita terverifikasi FAKTA. Kejuaraan Porkot Metro 2026 resmi dibuka oleh Wali Kota Metro di Stadion Tejosari.',
                'is_featured' => false,
                'published_at' => now()->subDays(7),
                'comments' => [
                    ['author' => '@sport_metro', 'comment' => 'Seru banget pembukaan Porkot Metro di Stadion Tejosari! Atlet sepakbola muda bakatnya luar biasa ⚽🔥'],
                    ['author' => '@riko_runner', 'comment' => 'Dukungan KONI dan pemkot bikin atlet metro makin semangat raih prestasi!'],
                    ['author' => '@maya_badminton', 'comment' => 'Fasilitas gelanggang olahraga Porkot Metro 2026 sangat memotivasi pemuda berolahraga.'],
                ],
            ],
        ];

        foreach ($sampleArticles as $artData) {
            $commentsList = $artData['comments'] ?? [];
            unset($artData['comments']);

            $article = Article::updateOrCreate(
                ['slug' => $artData['slug']],
                array_merge($artData, ['user_id' => $admin->id ?? 1])
            );

            $posCount = 0;
            $negCount = 0;
            $neuCount = 0;

            foreach ($commentsList as $c) {
                $sent = $sentimentEngine->analyzeSentiment($c['comment']);

                SocialComment::create([
                    'article_id' => $article->id,
                    'author_name' => $c['author'],
                    'raw_comment' => $c['comment'],
                    'sentiment' => $sent['sentiment'],
                    'sentiment_score' => $sent['sentiment_score'],
                ]);

                if ($sent['sentiment'] === 'positif') $posCount++;
                elseif ($sent['sentiment'] === 'negatif') $negCount++;
                else $neuCount++;
            }

            $article->update([
                'positive_count' => $posCount,
                'negative_count' => $negCount,
                'neutral_count' => $neuCount,
            ]);
        }
    }
}
