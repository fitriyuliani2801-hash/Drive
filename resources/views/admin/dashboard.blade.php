@extends('layouts.admin')

@section('title', 'Dashboard Redaksi & Analytics - METROLOGI NEWS')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Title & Quick Welcome -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-crimson-60 text-crimson-700 bg-red-50 border border-red-200 font-heading">
                OFFICIAL EDITORIAL DASHBOARD
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 font-heading">
                Dashboard Redaksi METROLOGI NEWS
            </h1>
            <p class="text-xs text-slate-500 font-medium">Ringkasan publikasi berita, statistik pembaca, serta hasil analisis sentimen tanggapan netizen Kota Metro.</p>
        </div>
        
        <a href="{{ route('admin.articles.create') }}" class="px-5 py-3 rounded-xl bg-crimson-700 hover:bg-crimson-800 text-white font-extrabold text-xs shadow-md transition-all flex items-center gap-1.5 self-start font-heading">
            <i class="ri-add-circle-line text-sm"></i> Tulis Berita Baru
        </a>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Berita -->
        <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-bold uppercase tracking-wider font-heading">Berita Diterbitkan</span>
                <div class="w-10 h-10 rounded-2xl bg-red-50 text-crimson-700 flex items-center justify-center font-bold">
                    <i class="ri-newspaper-line text-xl"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-900 font-heading">{{ number_format($totalArticles) }}</p>
            <p class="text-[11px] text-slate-500 font-semibold">Artikel &amp; liputan terbit</p>
        </div>

        <!-- Total Views -->
        <div class="bg-white border border-blue-200 rounded-3xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-brand-700">
                <span class="text-xs font-bold uppercase tracking-wider font-heading">Total Pembaca</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-brand-700 flex items-center justify-center font-bold">
                    <i class="ri-eye-line text-xl"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-brand-700 font-heading">{{ number_format($totalViews) }}</p>
            <p class="text-[11px] text-brand-800 font-semibold">Total akumulasi pembaca</p>
        </div>

        <!-- Total Komentar Netizen -->
        <div class="bg-white border border-emerald-200 rounded-3xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-emerald-700">
                <span class="text-xs font-bold uppercase tracking-wider font-heading">Komentar Medsos</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                    <i class="ri-chat-smile-2-line text-xl"></i>
                </div>
            </div>
            @php $totalCommentsCount = \App\Models\SocialComment::count(); @endphp
            <p class="text-3xl font-black text-emerald-700 font-heading">{{ number_format($totalCommentsCount) }}</p>
            <p class="text-[11px] text-emerald-800 font-semibold">Ter-Ingest &amp; AI Filtered</p>
        </div>

        <!-- Categories -->
        <div class="bg-white border border-purple-200 rounded-3xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-purple-700">
                <span class="text-xs font-bold uppercase tracking-wider font-heading">Sektor Perkotaan</span>
                <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                    <i class="ri-price-tag-3-line text-xl"></i>
                </div>
            </div>
            <p class="text-3xl font-black text-purple-700 font-heading">{{ number_format($categoriesCount) }}</p>
            <p class="text-[11px] text-purple-800 font-semibold">Kategori berita aktif</p>
        </div>

    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <div class="lg:col-span-6 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 font-heading">
                <i class="ri-pie-chart-line text-crimson-600"></i> Distribusi Sentimen Tanggapan Netizen
            </h3>
            <div class="h-60 relative flex items-center justify-center">
                <canvas id="adminSentChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-6 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 font-heading">
                <i class="ri-bar-chart-line text-crimson-600"></i> Publikasi Berita per Sektor Perkotaan
            </h3>
            <div class="h-60 relative">
                <canvas id="adminCatChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Recent & Popular Articles Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Articles -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 font-heading">
                    <i class="ri-newspaper-line text-crimson-600"></i> Berita Terbaru Terbit
                </h3>
                <a href="{{ route('admin.articles.index') }}" class="text-xs text-crimson-700 font-bold hover:underline font-heading">Lihat Semua</a>
            </div>

            <div class="space-y-3">
                @foreach($recentArticles as $art)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition-all text-xs">
                        <div class="space-y-0.5 flex-1 min-w-0 pr-4">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold text-white uppercase font-heading bg-crimson-700">
                                {{ $art->category->name }}
                            </span>
                            <h4 class="font-bold text-slate-900 truncate font-heading">{{ $art->title }}</h4>
                            <span class="text-[10px] text-slate-400 font-medium">{{ $art->created_at ? $art->created_at->diffForHumans() : 'Baru saja' }}</span>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <a href="{{ route('admin.articles.edit', $art->id) }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-crimson-700 text-xs font-bold shadow-2xs">
                                <i class="ri-edit-line"></i>
                            </a>
                            <a href="{{ route('articles.show', $art->slug) }}" target="_blank" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-brand-600 text-xs font-bold shadow-2xs">
                                <i class="ri-external-link-line"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Popular Articles (Most Read) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 font-heading">
                    <i class="ri-fire-line text-amber-500"></i> Berita Terpopuler (Top Read)
                </h3>
                <span class="text-[11px] text-slate-400 font-semibold font-heading">Paling Banyak Dibaca</span>
            </div>

            <div class="space-y-3">
                @foreach($popularArticles as $pop)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition-all text-xs">
                        <div class="space-y-0.5 flex-1 min-w-0 pr-4">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold text-white uppercase font-heading bg-brand-600">
                                {{ $pop->category->name }}
                            </span>
                            <h4 class="font-bold text-slate-900 truncate font-heading">{{ $pop->title }}</h4>
                            <span class="text-[10px] text-brand-700 font-bold"><i class="ri-eye-line"></i> {{ number_format($pop->views_count) }} kali dibaca</span>
                        </div>
                        <a href="{{ route('articles.show', $pop->slug) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-brand-600 text-xs font-bold shadow-2xs font-heading">
                            Buka
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Admin Sentiment Doughnut Chart
        const ctxSent = document.getElementById('adminSentChart').getContext('2d');
        new Chart(ctxSent, {
            type: 'doughnut',
            data: {
                labels: ['Positif 👍', 'Negatif 👎', 'Netral 💬'],
                datasets: [{
                    data: [
                        {{ \App\Models\SocialComment::where('sentiment', 'positif')->count() }},
                        {{ \App\Models\SocialComment::where('sentiment', 'negatif')->count() }},
                        {{ \App\Models\SocialComment::where('sentiment', 'netral')->count() }}
                    ],
                    backgroundColor: ['#2563eb', '#dc2626', '#94a3b8'],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' } } }
                },
                cutout: '70%'
            }
        });

        // Admin Category Bar Chart
        const ctxCat = document.getElementById('adminCatChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'bar',
            data: {
                labels: {!! json_encode($categoriesStats->pluck('name')) !!},
                datasets: [{
                    label: 'Jumlah Berita',
                    data: {!! json_encode($categoriesStats->pluck('articles_count')) !!},
                    backgroundColor: ['#b91c1c', '#1d4ed8', '#0284c7', '#0d9488', '#7c3aed'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } },
                    x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' } } }
                }
            }
        });
    });
</script>
@endpush
