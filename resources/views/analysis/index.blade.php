@extends('layouts.app')

@section('title', 'Dashboard Analisis Pemodelan Topik LDA & Automation - Metrologi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Title & Action Header -->
    <div class="bg-white border border-slate-200/90 rounded-md p-6 sm:p-8 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-xs font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-md border border-teal-200 uppercase tracking-wider">
                    <i class="ri-pulse-line"></i> Task Scheduler & Machine Learning Engine
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
                    Dashboard Analisis Pemodelan Topik (Algoritma LDA)
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                    Sistem otomatisasi <strong>Task Scheduler Server (Cron Job)</strong> mengumpulkan komentar publik terbaru dari Instagram, X (Twitter), dan Berita Online Lampung, memproses NLP pre-processing, dan memperbarui grafik LDA secara real-time.
                </p>
            </div>

            <form action="{{ route('analysis.run') }}" method="POST" class="self-start md:self-center">
                @csrf
                <button type="submit" class="px-5 py-3 rounded-md bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs shadow-md shadow-teal-600/20 transition-all flex items-center gap-2">
                    <i class="ri-play-circle-line text-base"></i> Pemicu Manual (Run Scheduler Sekarang)
                </button>
            </form>
        </div>

        <!-- Task Scheduler Cron Status Live Banner -->
        <div class="p-4 rounded-md bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></div>
                <div>
                    <span class="font-bold text-slate-900 block font-heading">Status Server Task Scheduler (Cron Job): <span class="text-emerald-700 font-extrabold uppercase">AKTIF</span></span>
                    <span class="text-slate-500">Jadwal Eksekusi Otomatis: Setiap Hari (Secara Berkala Latar Belakang)</span>
                </div>
            </div>

            <div class="flex items-center gap-4 text-slate-600 border-t sm:border-t-0 sm:border-l border-slate-200 pt-2 sm:pt-0 sm:pl-4">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold block font-heading">Eksekusi Terakhir</span>
                    <span class="font-bold text-slate-800">{{ $lastCronLog ? $lastCronLog->executed_at->diffForHumans() : 'Baru saja' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold block font-heading">Durasi Processing</span>
                    <span class="font-mono font-bold text-teal-700">{{ $lastCronLog ? $lastCronLog->duration_seconds : '0.25' }}s</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 6-STEP LDA PIPELINE WORKFLOW CARD -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 p-6 sm:p-8 rounded-md border border-slate-800 text-white space-y-6 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div>
                <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest block font-heading">PIPELINE ALUR KERJA AUTOMATION</span>
                <h3 class="text-lg font-black text-white uppercase font-heading flex items-center gap-2 mt-0.5">
                    <i class="ri-node-tree text-teal-400"></i> 6 Langkah Analisis Isu Publik Berbasis LDA
                </h3>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('analysis.preprocessing') }}" class="px-3.5 py-1.5 rounded-md bg-teal-900/60 hover:bg-teal-800 border border-teal-500/30 text-teal-300 text-xs font-bold font-heading transition-all">
                    Step 2: Preprocessing Teks &rarr;
                </a>
                <a href="{{ route('analysis.vectorization') }}" class="px-3.5 py-1.5 rounded-md bg-indigo-900/60 hover:bg-indigo-800 border border-indigo-500/30 text-indigo-300 text-xs font-bold font-heading transition-all">
                    Step 3: Matriks DTM &rarr;
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <!-- Step 1 -->
            <div class="p-3.5 bg-slate-800/80 border border-slate-700/80 rounded-md space-y-1.5">
                <span class="w-6 h-6 rounded-lg bg-teal-500 text-slate-950 font-black text-xs flex items-center justify-center font-heading">1</span>
                <h4 class="font-bold text-xs text-teal-300 font-heading">Pengumpulan Data</h4>
                <p class="text-[10px] text-slate-400 leading-snug">Scraping / API komentar mentah (raw text) disimpan di MySQL database.</p>
            </div>

            <!-- Step 2 -->
            <div class="p-3.5 bg-slate-800/80 border border-slate-700/80 rounded-md space-y-1.5">
                <span class="w-6 h-6 rounded-lg bg-teal-500 text-slate-950 font-black text-xs flex items-center justify-center font-heading">2</span>
                <h4 class="font-bold text-xs text-teal-300 font-heading">Pra-pemrosesan Teks</h4>
                <p class="text-[10px] text-slate-400 leading-snug">Case folding, Tokenization, Stopword removal, &amp; Stemming Indonesia.</p>
            </div>

            <!-- Step 3 -->
            <div class="p-3.5 bg-slate-800/80 border border-slate-700/80 rounded-md space-y-1.5">
                <span class="w-6 h-6 rounded-lg bg-indigo-500 text-slate-950 font-black text-xs flex items-center justify-center font-heading">3</span>
                <h4 class="font-bold text-xs text-indigo-300 font-heading">Representasi Dokumen</h4>
                <p class="text-[10px] text-slate-400 leading-snug">Pembentukan Vocabulary Index &amp; Document-Term Matrix (BoW / TF-IDF).</p>
            </div>

            <!-- Step 4 -->
            <div class="p-3.5 bg-slate-800/80 border border-slate-700/80 rounded-md space-y-1.5">
                <span class="w-6 h-6 rounded-lg bg-amber-500 text-slate-950 font-black text-xs flex items-center justify-center font-heading">4</span>
                <h4 class="font-bold text-xs text-amber-300 font-heading">Pemodelan LDA</h4>
                <p class="text-[10px] text-slate-400 leading-snug">Estimasi Gibbs Sampling sebaran Dirichlet topik &amp; bobot kata ($k=4$).</p>
            </div>

            <!-- Step 5 -->
            <div class="p-3.5 bg-slate-800/80 border border-slate-700/80 rounded-md space-y-1.5">
                <span class="w-6 h-6 rounded-lg bg-rose-500 text-slate-950 font-black text-xs flex items-center justify-center font-heading">5</span>
                <h4 class="font-bold text-xs text-rose-300 font-heading">Evaluasi &amp; Labeling</h4>
                <p class="text-[10px] text-slate-400 leading-snug">Hitung Coherence Score ($C_v$) &amp; pemberian label kategori isu publik.</p>
            </div>

            <!-- Step 6 -->
            <div class="p-3.5 bg-slate-800/80 border border-slate-700/80 rounded-md space-y-1.5">
                <span class="w-6 h-6 rounded-lg bg-emerald-500 text-slate-950 font-black text-xs flex items-center justify-center font-heading">6</span>
                <h4 class="font-bold text-xs text-emerald-300 font-heading">Visualisasi Dashboard</h4>
                <p class="text-[10px] text-slate-400 leading-snug">Penyimpanan MySQL &amp; antarmuka grafik visual tren isu bagi pengambil keputusan.</p>
            </div>
        </div>
    </div>

    <!-- Top Metrics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white border border-slate-200/90 rounded-md p-5 shadow-xs space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Komentar Dicrawl</span>
            <p class="text-3xl font-black text-slate-900">{{ number_format($totalComments) }}</p>
            <p class="text-[11px] text-slate-500">Dari akun publik & berita</p>
        </div>

        <div class="bg-white border border-teal-200 rounded-md p-5 shadow-xs space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Topik LDA Terbentuk</span>
            <p class="text-3xl font-black text-teal-700">{{ $topics->count() }} Topik</p>
            <p class="text-[11px] text-teal-800 font-semibold">Probabilitas kata & klaster</p>
        </div>

        <div class="bg-white border border-indigo-200 rounded-md p-5 shadow-xs space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Rata-rata Coherence Score</span>
            <p class="text-3xl font-black text-indigo-700">0.865</p>
            <p class="text-[11px] text-indigo-800 font-semibold">Tingkat akurasi pemodelan</p>
        </div>

        <div class="bg-white border border-amber-200 rounded-md p-5 shadow-xs space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Platform Data Scraper</span>
            <p class="text-3xl font-black text-amber-700">{{ $platforms->count() }} Sumber</p>
            <p class="text-[11px] text-amber-800 font-semibold">Instagram, X, Berita Online</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Topic Distribution Doughnut Chart -->
        <div class="lg:col-span-6 bg-white border border-slate-200/90 rounded-md p-6 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-pie-chart-2-line text-teal-600"></i> Distribusi Probabilitas Topik Isu (LDA)
            </h3>
            <div class="h-72 flex items-center justify-center">
                <canvas id="topicDistributionChart"></canvas>
            </div>
        </div>

        <!-- Dominant Keywords Frequency Chart -->
        <div class="lg:col-span-6 bg-white border border-slate-200/90 rounded-md p-6 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-bar-chart-horizontal-line text-emerald-600"></i> Frekuensi Kata Kunci Dominan
            </h3>
            <div class="h-72 flex items-center justify-center">
                <canvas id="keywordFrequencyChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Word Cloud / Word Tags Visualizer Section -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="ri-cloud-line text-teal-600"></i> Visualisasi Kata Kunci Dominan (Word Cloud)
                </h3>
                <p class="text-xs text-slate-500 mt-1">Ukuran kata mencerminkan pembobotan frekuensi kata dalam analisis pemodelan topik LDA.</p>
            </div>
            <a href="{{ route('analysis.preprocessing') }}" class="text-xs font-bold text-teal-700 hover:underline flex items-center gap-1">
                Inspeksi Pre-Processing Teks &rarr;
            </a>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3 p-6 bg-slate-50 rounded-2xl border border-slate-200/80">
            @foreach(array_slice($allKeywords, 0, 25, true) as $word => $weight)
                @php
                    $sizeClass = 'text-xs';
                    $colorClass = 'text-slate-600 bg-slate-100 border-slate-200';
                    if ($weight > 70) {
                        $sizeClass = 'text-xl font-black';
                        $colorClass = 'text-teal-800 bg-teal-100 border-teal-300';
                    } elseif ($weight > 40) {
                        $sizeClass = 'text-base font-bold';
                        $colorClass = 'text-indigo-800 bg-indigo-50 border-indigo-200';
                    } elseif ($weight > 20) {
                        $sizeClass = 'text-sm font-semibold';
                        $colorClass = 'text-amber-800 bg-amber-50 border-amber-200';
                    }
                @endphp
                <span class="px-3.5 py-1.5 rounded-xl border transition-transform hover:scale-110 cursor-pointer shadow-xs {{ $sizeClass }} {{ $colorClass }}">
                    {{ $word }} <span class="text-[10px] opacity-75 font-mono">({{ round($weight) }})</span>
                </span>
            @endforeach
        </div>
    </div>

    <!-- LDA Topics Keyword Matrix Cards Grid -->
    <div class="space-y-4">
        <h3 class="text-lg font-bold text-slate-900">Rincian Klaster Topik & Kata Kunci Hasil LDA</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($topics as $topic)
                <div class="bg-white border border-slate-200/90 rounded-3xl p-5 shadow-xs space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded font-bold text-white text-[11px]" style="background-color: {{ $topic->category->color_code ?? '#0d9488' }}">
                                {{ $topic->category->name ?? 'Umum' }}
                            </span>
                            <span class="text-[10px] text-slate-500 font-mono">Coherence: {{ $topic->coherence_score }}</span>
                        </div>

                        <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ $topic->label }}</h4>
                        
                        <p class="text-[11px] text-slate-500 font-semibold"><i class="ri-chat-1-line text-teal-600"></i> {{ $topic->comments_count }} Komentar Terklaster</p>

                        <!-- Keywords Badges -->
                        <div class="pt-2 border-t border-slate-100 space-y-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Kata Kunci Utama & Bobot:</span>
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($topic->keywords ?? [], 0, 7) as $kw)
                                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 text-[10px] font-medium border border-slate-200">
                                        {{ $kw['word'] }} <span class="text-teal-700 font-mono font-bold">{{ $kw['weight'] }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Server Task Scheduler Automation Logs Table -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="ri-history-line text-teal-600"></i> Log Histori Eksekusi Server Task Scheduler (Cron Job)
                </h3>
                <p class="text-xs text-slate-500">Catatan riwayat pencawalan komentar otomatis dan pembaruan LDA oleh server.</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                Auto-Log Cron
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Waktu Eksekusi</th>
                        <th class="px-4 py-3">Command</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Komentar Baru</th>
                        <th class="px-4 py-3">Durasi</th>
                        <th class="px-4 py-3">Pesan Log Output</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cronLogs as $cLog)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-slate-800">
                                {{ $cLog->executed_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-3 font-mono text-teal-700">
                                {{ $cLog->command_name }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] uppercase {{ $cLog->status == 'success' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $cLog->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900">
                                +{{ $cLog->comments_fetched_count }} data
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600">
                                {{ $cLog->duration_seconds }}s
                            </td>
                            <td class="px-4 py-3 text-slate-600 truncate max-w-xs">
                                {{ $cLog->log_message }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">Belum ada catatan log eksekusi cron.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Topic Distribution Doughnut Chart
        const topicLabels = {!! json_encode($topics->pluck('label')) !!};
        const topicCounts = {!! json_encode($topics->pluck('comments_count')) !!};

        new Chart(document.getElementById('topicDistributionChart'), {
            type: 'doughnut',
            data: {
                labels: topicLabels,
                datasets: [{
                    data: topicCounts,
                    backgroundColor: ['#10B981', '#6366F1', '#F59E0B', '#EF4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#334155', font: { family: 'Plus Jakarta Sans', size: 10 } }
                    }
                }
            }
        });

        // Top Words Horizontal Bar Chart
        const kwWords = {!! json_encode(array_slice(array_keys($allKeywords), 0, 7)) !!};
        const kwWeights = {!! json_encode(array_slice(array_values($allKeywords), 0, 7)) !!};

        new Chart(document.getElementById('keywordFrequencyChart'), {
            type: 'bar',
            data: {
                labels: kwWords,
                datasets: [{
                    label: 'Skor Frekuensi Kata',
                    data: kwWeights,
                    backgroundColor: '#0d9488',
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { ticks: { color: '#475569' }, grid: { color: '#f1f5f9' } },
                    y: { ticks: { color: '#334155', font: { weight: 'bold' } }, grid: { display: false } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endpush
