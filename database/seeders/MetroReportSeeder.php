<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Report;
use App\Models\ReportLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MetroReportSeeder extends Seeder
{
    public function run(): void
    {
        $infra = Category::where('slug', 'infrastruktur')->first();
        $env = Category::where('slug', 'lingkungan')->first();
        $fasum = Category::where('slug', 'fasilitas-umum')->first();
        $social = Category::where('slug', 'sosial-ketertiban')->first();
        $econ = Category::where('slug', 'ekonomi-pasar')->first();

        $budi = User::where('email', 'budi@warga.metro.id')->first();
        $siti = User::where('email', 'siti@warga.metro.id')->first();
        $admin = User::where('email', 'admin@metrologi.go.id')->first();

        $sampleReports = [
            [
                'ticket_code' => 'MTR-202607-001',
                'user_id' => $budi->id ?? null,
                'category_id' => $infra->id,
                'reporter_name' => 'Budi Santoso',
                'reporter_phone' => '085211223344',
                'title' => 'Jalan Berlubang Cukup Dalam di Jalan AH Nasution 21A',
                'description' => 'Terdapat lubang jalan berdiameter sekitar 80cm dengan kedalaman 15cm tepat di depan Indomaret 21A. Sangat membahayakan pengendara motor terutama saat malam hari karena penerangan minim.',
                'latitude' => -5.118500,
                'longitude' => 105.312000,
                'location_address' => 'Jl. AH Nasution No. 45, Yosodadi, Metro Timur',
                'district' => 'Metro Timur',
                'status' => 'in_progress',
                'urgency' => 'high',
                'admin_note' => 'Sudah diverifikasi oleh Dinas PUTR Kota Metro. Tim pemeliharaan jalan dijadwalkan melakukan penambalan besok pagi.',
                'verified_at' => now()->subDays(2),
                'resolved_at' => null,
            ],
            [
                'ticket_code' => 'MTR-202607-002',
                'user_id' => $siti->id ?? null,
                'category_id' => $env->id,
                'reporter_name' => 'Siti Rahmawati',
                'reporter_phone' => '081399887766',
                'title' => 'Tumpukan Sampah Liar dan Bau Menyengat di Lapangan Ganjar Asri',
                'description' => 'Warga membuang sampah sisa rumah tangga dan pasar di sudut lapangan 16C Ganjar Asri. Sampah mulai menumpuk dan menimbulkan bau tidak sedap.',
                'latitude' => -5.132100,
                'longitude' => 105.291500,
                'location_address' => 'Jl. Jendral Sudirman 16C, Ganjar Asri, Metro Barat',
                'district' => 'Metro Barat',
                'status' => 'resolved',
                'urgency' => 'medium',
                'admin_note' => 'Dinas Lingkungan Hidup Kota Metro telah menerjunkan armada truk sampah dan membersihkan lokasi secara menyeluruh pada 21 Juli 2026.',
                'verified_at' => now()->subDays(4),
                'resolved_at' => now()->subDay(),
            ],
            [
                'ticket_code' => 'MTR-202607-003',
                'user_id' => null,
                'category_id' => $fasum->id,
                'reporter_name' => 'Ahmad Fauzi',
                'reporter_phone' => '082155443322',
                'title' => 'Lampu Taman Kota Merdeka Metro Sebagian Besar Padam',
                'description' => 'Sekitar 6 titik tiang lampu penerangan taman di area Taman Merdeka Metro mati total sejak seminggu lalu. Suasana menjadi agak gelap dan riskan keamanan.',
                'latitude' => -5.113900,
                'longitude' => 105.307200,
                'location_address' => 'Taman Merdeka Kota Metro, Imopuro, Metro Pusat',
                'district' => 'Metro Pusat',
                'status' => 'pending',
                'urgency' => 'medium',
                'admin_note' => null,
                'verified_at' => null,
                'resolved_at' => null,
            ],
            [
                'ticket_code' => 'MTR-202607-004',
                'user_id' => $budi->id ?? null,
                'category_id' => $env->id,
                'reporter_name' => 'Budi Santoso',
                'reporter_phone' => '085211223344',
                'title' => 'Luapan Drainase / Genangan Air Saat Hujan Deras di Banjarsari 29',
                'description' => 'Saluran drainase tersumbat sedimen lumpur sehingga air meluap ke badan jalan sepanjang 100 meter saat hujan deras.',
                'latitude' => -5.092000,
                'longitude' => 105.304500,
                'location_address' => 'Jl. Ki Hajar Dewantara 29, Banjarsari, Metro Utara',
                'district' => 'Metro Utara',
                'status' => 'verified',
                'urgency' => 'high',
                'admin_note' => 'Verifikasi valid. Laporan telah diteruskan ke bidang Pengairan & Drainase PUTR Kota Metro.',
                'verified_at' => now()->subHours(12),
                'resolved_at' => null,
            ],
            [
                'ticket_code' => 'MTR-202607-005',
                'user_id' => null,
                'category_id' => $social->id,
                'reporter_name' => 'Rina Wijaya',
                'reporter_phone' => '087811224455',
                'title' => 'Aktivitas Balap Liar Malam Minggu di Sepanjang Jl. Raden Intan',
                'description' => 'Setiap Sabtu malam mulai pukul 23.00 WIB sering terjadi aksi balap liar motor knalpot brong yang sangat mengganggu jam istirahat warga sekitar.',
                'latitude' => -5.148000,
                'longitude' => 105.313500,
                'location_address' => 'Jl. Raden Intan, Margorejo, Metro Selatan',
                'district' => 'Metro Selatan',
                'status' => 'in_progress',
                'urgency' => 'critical',
                'admin_note' => 'Telah dikordinasikan dengan Satpol PP Kota Metro dan Polres Metro untuk peningkatan patroli rutin dan penyekatan jalan.',
                'verified_at' => now()->subDays(1),
                'resolved_at' => null,
            ],
            [
                'ticket_code' => 'MTR-202607-006',
                'user_id' => $siti->id ?? null,
                'category_id' => $econ->id,
                'reporter_name' => 'Siti Rahmawati',
                'reporter_phone' => '081399887766',
                'title' => 'Penataan Lapak PKL Meluber ke Badan Jalan Pasar Cendrawasih',
                'description' => 'Lapak pedagang di sekitar Pasar Cendrawasih meluber sampai menutup separuh badan jalan sehingga memicu kemacetan parah di jam pagi.',
                'latitude' => -5.116200,
                'longitude' => 105.309000,
                'location_address' => 'Pasar Cendrawasih, Metro Pusat',
                'district' => 'Metro Pusat',
                'status' => 'resolved',
                'urgency' => 'medium',
                'admin_note' => 'Dinas Perdagangan dan Satpol PP telah menata kembali jalur PKL serta memberikan sosialisasi batas lapak jualan.',
                'verified_at' => now()->subDays(5),
                'resolved_at' => now()->subDays(2),
            ],
        ];

        foreach ($sampleReports as $repData) {
            $report = Report::updateOrCreate(['ticket_code' => $repData['ticket_code']], $repData);

            // Create initial log
            ReportLog::create([
                'report_id' => $report->id,
                'user_id' => $repData['user_id'] ?? $admin->id,
                'status_from' => null,
                'status_to' => $repData['status'],
                'note' => 'Laporan dibuat dan dicatat dalam sistem Metrologi.',
            ]);
        }
    }
}
