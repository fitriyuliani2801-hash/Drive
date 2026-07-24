<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ekonomi',
                'slug' => 'ekonomi',
                'icon' => 'ri-money-dollar-circle-line',
                'color_code' => '#10B981', // Emerald
                'description' => 'Berita perdagangan, UMKM, pasar rakyat, investasi daerah, dan pertumbuhan ekonomi Kota Metro.',
            ],
            [
                'name' => 'Hukum',
                'slug' => 'hukum',
                'icon' => 'ri-scales-3-line',
                'color_code' => '#6366F1', // Indigo
                'description' => 'Informasi regulasi daerah, perda, penegakan hukum, serta layanan kesadaran hukum masyarakat.',
            ],
            [
                'name' => 'Politik',
                'slug' => 'politik',
                'icon' => 'ri-government-line',
                'color_code' => '#F59E0B', // Amber
                'description' => 'Kajian kebijakan publik, dinamika pemerintahan kota, pilkada, dan tata kelola transparansi.',
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'icon' => 'ri-football-line',
                'color_code' => '#EF4444', // Red
                'description' => 'Berita kejuaraan daerah, kompetisi olahraga antarkecamatan, prestasi atlet, dan fasilitas olahraga Kota Metro.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
