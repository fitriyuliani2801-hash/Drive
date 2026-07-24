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

        $sampleComments = [
            // EKONOMI
            [
                'platform' => 'Instagram',
                'source_account' => '@seputar_metro',
                'author_name' => '@rudi_kuliner_metro',
                'raw_text' => 'Alhamdulillah program pelatihan QRIS dan permodalan UMKM dari Pemkot Metro sangat membantu pedagang kecil pasar Cendrawasih! Omzet jualan naik pesat 👍🔥',
            ],
            [
                'platform' => 'X (Twitter)',
                'source_account' => '@pemkotmetro',
                'author_name' => '@anisa_metro21',
                'raw_text' => 'Semoga lapak pedagang UMKM di sentra kuliner kota metro makin banyak opsi pembayaran digitalnya, transaksi jadi gampang banget!',
            ],
            [
                'platform' => 'Berita Online Lampung',
                'source_account' => '@radar_lampung',
                'author_name' => 'Warga Metro Pusat',
                'raw_text' => 'Pasar rakyat dan UMKM lokal butuh akses permodalan usaha yang stabil agar ekonomi rakyat kota metro terus berputar positif.',
            ],
            [
                'platform' => 'Instagram',
                'source_account' => '@metro_info',
                'author_name' => '@hendra_pasar',
                'raw_text' => 'Harga komoditas pangan di pasar rakyat metro relatif stabil minggu ini, koordinasi dinas perdagangan jempolan banget.',
            ],
            [
                'platform' => 'X (Twitter)',
                'source_account' => '@radar_lampung',
                'author_name' => '@budi_ekonomi',
                'raw_text' => 'Peningkatan omzet pelaku usaha mikro dan sentra kuliner jadi indikator pertumbuhan ekonomi positif di kota metro!',
            ],

            // HUKUM
            [
                'platform' => 'Instagram',
                'source_account' => '@pemkotmetro',
                'author_name' => '@siti_rahma_law',
                'raw_text' => 'Sangat mengapresiasi adanya penyuluhan hukum gratis dan sosialisasi Perda dari Bagian Hukum Pemkot Metro untuk masyarakat kurang mampu.',
            ],
            [
                'platform' => 'Berita Online Lampung',
                'source_account' => '@seputar_metro',
                'author_name' => 'Ahmad Pengacara',
                'raw_text' => 'Posko layanan konsultasi dan bantuan hukum gratis sangat bermanfaat mengatasi sengketa lahan warga di daerah metro timur.',
            ],
            [
                'platform' => 'X (Twitter)',
                'source_account' => '@radar_lampung',
                'author_name' => '@metro_justice',
                'raw_text' => 'Penegakan peraturan daerah (Perda) dan kesadaran hukum masyarakat harus terus ditingkatkan demi keadilan publik kota metro.',
            ],
            [
                'platform' => 'Instagram',
                'source_account' => '@metro_info',
                'author_name' => '@dedi_warga',
                'raw_text' => 'Pentingnya sosialisasi aturan Perda ketertiban agar tidak ada pelanggaran hukum di kawasan umum metro.',
            ],
            [
                'platform' => 'X (Twitter)',
                'source_account' => '@pemkotmetro',
                'author_name' => '@rahmat_hakim',
                'raw_text' => 'Konsultasi bantuan hukum gratis dari bagian hukum pemkot sangat membantu warga yang awam aturan undang-undang.',
            ],

            // POLITIK
            [
                'platform' => 'Berita Online Lampung',
                'source_account' => '@radar_lampung',
                'author_name' => 'Kritikus Publik Metro',
                'raw_text' => 'Pengesahan APBD oleh DPRD dan Pemkot Metro harus difokuskan untuk efisiensi pelayanan publik dan transparansi anggaran daerah.',
            ],
            [
                'platform' => 'Instagram',
                'source_account' => '@seputar_metro',
                'author_name' => '@bagus_politik',
                'raw_text' => 'Dinamika kebijakan publik dan alokasi anggaran pemerintah kota hendaknya terus terbuka untuk diawasi warga metro.',
            ],
            [
                'platform' => 'X (Twitter)',
                'source_account' => '@pemkotmetro',
                'author_name' => '@dewi_suara',
                'raw_text' => 'Transparansi anggaran belanja daerah APBD di paripurna DPRD bentuk pertanggungjawaban politik pemerintah kepada rakyat.',
            ],
            [
                'platform' => 'Instagram',
                'source_account' => '@metro_info',
                'author_name' => '@fajar_metro',
                'raw_text' => 'Kebijakan publik yang akuntabel dari pemkot metro akan membawa dampak positif bagi kemajuan layanan birokrasi daerah.',
            ],
            [
                'platform' => 'Berita Online Lampung',
                'source_account' => '@radar_lampung',
                'author_name' => 'Warga Metro Utara',
                'raw_text' => 'Sinergi politik DPRD dan Pemkot Metro dalam menyusun anggaran daerah patut diacungi jempol.',
            ],

            // OLAHRAGA
            [
                'platform' => 'Instagram',
                'source_account' => '@seputar_metro',
                'author_name' => '@sport_metro_fans',
                'raw_text' => 'Seru banget pembukaan Pekan Olahraga Kota (Porkot) Metro 2026 di Stadion Tejosari! Atlet sepakbola muda bakatnya luar biasa ⚽🔥',
            ],
            [
                'platform' => 'X (Twitter)',
                'source_account' => '@pemkotmetro',
                'author_name' => '@riko_runner',
                'raw_text' => 'Dukungan KONI dan pemkot untuk fasilitas kejuaraan olahraga antarkecamatan bikin atlet metro makin semangat raih prestasi!',
            ],
            [
                'platform' => 'Berita Online Lampung',
                'source_account' => '@radar_lampung',
                'author_name' => 'Pelatih Atletik Metro',
                'raw_text' => 'Kompetisi cabang olahraga Porkot di stadion tejosari melahirkan bibit-bibit atlet juara yang siap tanding di tingkat provinsi.',
            ],
            [
                'platform' => 'Instagram',
                'source_account' => '@metro_info',
                'author_name' => '@maya_badminton',
                'raw_text' => 'Fasilitas gelanggang olahraga dan kejuaraan Porkot Metro 2026 sangat megah dan memotivasi pemuda berolahraga.',
            ],
            [
                'platform' => 'X (Twitter)',
                'source_account' => '@seputar_metro',
                'author_name' => '@donny_futsal',
                'raw_text' => 'Pertandingan kejuaraan olahraga antar atlet kecamatan kota metro berjalan sportif dan meriah banget!',
            ],
        ];

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
