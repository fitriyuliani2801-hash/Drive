@extends('layouts.app')

@section('title', 'METROLOGI NEWS - Portal Berita & Isu Medsos Kota Metro')

@section('content')
<!-- HERO HEADLINES BENTO GRID (GAYA DETIK.COM & KOMPAS.COM) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Section Title Header -->
    <div class="flex items-center justify-between border-b-2 border-crimson-600 pb-3">
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2 font-heading tracking-tight">
            <span class="w-3 h-7 bg-crimson-700 rounded-sm inline-block"></span> HEADLINE BERITA UTAMA
        </h2>
        <span class="text-xs text-slate-500 font-medium">Diperbarui {{ \Carbon\Carbon::now()->format('H:i') }} WIB</span>
    </div>

    @if($featuredArticles->isNotEmpty())
        @php 
            $primaryHeadline = $featuredArticles->first(); 
            $sideHeadlines = $featuredArticles->skip(1);
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Primary Featured Hero News Card (7 Cols - Left) -->
            <div class="lg:col-span-7">
                <a href="{{ route('articles.show', $primaryHeadline->slug) }}" class="group block relative bg-slate-950 rounded-md overflow-hidden shadow-xl border border-slate-200 hover:shadow-2xl transition-all duration-500">
                    <div class="relative h-[440px] w-full overflow-hidden bg-slate-900">
                        @if($primaryHeadline->image_path)
                            <img src="{{ asset('storage/' . $primaryHeadline->image_path) }}" alt="{{ $primaryHeadline->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-80 group-hover:opacity-90">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-900 via-brand-950 to-slate-950 flex items-center justify-center p-8">
                                <i class="ri-newspaper-line text-9xl text-white/20"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>

                        <!-- Top Badges -->
                        <div class="absolute top-5 left-5 flex flex-wrap items-center gap-2 z-10">
                            <span class="px-3.5 py-1 rounded-md font-black text-white text-xs shadow-md uppercase tracking-wider font-heading bg-crimson-700">
                                {{ $primaryHeadline->category->name }}
                            </span>
                            <span class="px-3.5 py-1 rounded-md font-black text-amber-300 text-xs bg-slate-950/80 backdrop-blur-md border border-amber-400/40 flex items-center gap-1.5 font-heading">
                                <i class="ri-star-fill text-amber-400"></i> HEADLINE FOKUS
                            </span>
                        </div>

                        <!-- Content Overlay -->
                        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8 space-y-3 z-10">
                            <div class="flex items-center gap-3 text-xs text-slate-300 font-medium">
                                <span class="text-amber-300 font-bold"><i class="ri-time-line"></i> {{ $primaryHeadline->published_at ? $primaryHeadline->published_at->diffForHumans() : $primaryHeadline->created_at->diffForHumans() }}</span>
                                <span>&bull;</span>
                                <span><i class="ri-user-3-line"></i> {{ $primaryHeadline->source }}</span>
                                <span>&bull;</span>
                                <span class="text-cyan-300 font-bold"><i class="ri-eye-line"></i> {{ number_format($primaryHeadline->views_count) }} dibaca</span>
                            </div>

                            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white group-hover:text-amber-300 transition-colors leading-snug font-heading">
                                {{ $primaryHeadline->title }}
                            </h1>

                            <p class="text-slate-300 text-xs sm:text-sm line-clamp-2 leading-relaxed font-normal">
                                {{ $primaryHeadline->excerpt }}
                            </p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 4 Secondary Side Headlines List (5 Cols - Right) -->
            <div class="lg:col-span-5 space-y-3">
                @foreach($sideHeadlines as $index => $secHeadline)
                    <a href="{{ route('articles.show', $secHeadline->slug) }}" class="bg-white p-4 rounded-md border border-slate-200/90 hover:border-crimson-600 transition-all group flex items-start gap-3.5 shadow-xs hover:shadow-md block">
                        <!-- Number Badge -->
                        <div class="w-8 h-8 rounded-md bg-slate-100 text-slate-700 flex items-center justify-center font-black text-sm shrink-0 font-heading group-hover:bg-crimson-700 group-hover:text-white transition-colors">
                            #{{ $index + 2 }}
                        </div>

                        @if($secHeadline->image_path)
                            <div class="w-20 h-20 rounded-md bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                                <img src="{{ asset('storage/' . $secHeadline->image_path) }}" alt="{{ $secHeadline->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                        @endif

                        <div class="flex-1 space-y-1 min-w-0">
                            <div class="flex items-center gap-2 text-[10px] font-bold">
                                <span class="px-2 py-0.5 rounded text-white uppercase font-heading bg-brand-600">
                                    {{ $secHeadline->category->name }}
                                </span>
                                <span class="text-slate-400"><i class="ri-time-line"></i> {{ $secHeadline->published_at ? $secHeadline->published_at->diffForHumans() : $secHeadline->created_at->diffForHumans() }}</span>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 group-hover:text-crimson-700 transition-colors line-clamp-2 leading-snug font-heading">
                                {{ $secHeadline->title }}
                            </h3>

                            <div class="flex items-center gap-3 text-[11px] text-slate-500 font-medium">
                                <span class="text-brand-700 font-bold"><i class="ri-eye-line"></i> {{ number_format($secHeadline->views_count) }} dibaca</span>
                                <span>&bull;</span>
                                <span class="text-emerald-700 font-bold">👍 {{ $secHeadline->positive_count }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    @endif
</div>


<!-- FEATURED WIDGET: 6-STEP LDA TOPIC MODELING PUBLIC ISU ANALYSIS BANNER -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
    <div class="bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 p-6 sm:p-8 rounded-md border border-slate-800 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-3xl">
            <span class="px-3 py-1 rounded-md bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-bold font-heading uppercase tracking-wider">
                <i class="ri-pulse-line text-teal-400"></i> Sistem Machine Learning NLP (6-Step LDA Pipeline)
            </span>
            <h3 class="text-xl sm:text-2xl font-black text-white font-heading leading-tight">
                Analisis Isu Publik &amp; Pemodelan Topik Berbasis Latent Dirichlet Allocation (LDA)
            </h3>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                Sistem otomatis mengagregasi komentar publik dari Instagram, X, dan Berita Online, memproses 4 tahap pre-processing NLP (Case Folding, Tokenization, Stopword, Stemming), memetakan matriks DTM (TF-IDF), serta menghitung probabilitas 4 klaster topik utama Kota Metro.
            </p>
        </div>

      <div class="flex flex-wrap items-center gap-3 shrink-0">
    <!-- Tambahkan awalan admin. pada route -->
    <a href="{{ route('admin.analysis.index') }}" class="px-5 py-3 rounded-md bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs shadow-lg shadow-teal-600/30 transition-all font-heading flex items-center gap-2">
        <i class="ri-pie-chart-2-line text-lg"></i> Buka Dashboard LDA &rarr;
    </a>
    <a href="{{ route('admin.analysis.vectorization') }}" class="px-4 py-3 rounded-md bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs border border-slate-700 transition-all font-heading flex items-center gap-1.5">
        <i class="ri-matrix-line text-indigo-400"></i> Inspeksi Matriks DTM
    </a>
</div>
    </div>
</div>

<!-- MAIN CONTENT 2-COLUMN SECTION (7 COLS MAIN STREAM + 5 COLS SIDEBAR WIDGETS) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- LEFT COLUMN: CHRONOLOGICAL NEWS STREAM (7 COLS) -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Metro Terkini Header -->
            <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3">
                <h2 class="text-xl font-black text-slate-900 flex items-center gap-2 font-heading tracking-tight">
                    <i class="ri-newspaper-line text-crimson-600"></i> METRO TERKINI
                </h2>
                <a href="{{ route('articles.index') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 font-heading">
                    Lihat Semua Berita &rarr;
                </a>
            </div>

            <!-- Articles Feed List -->
            <div class="space-y-6">
                @foreach($latestArticles as $art)
                    <div class="bg-white rounded-md p-5 border border-slate-200/90 hover:border-brand-500 transition-all flex flex-col sm:flex-row gap-5 shadow-xs hover:shadow-md group">
                        
                        <!-- Thumbnail Image -->
                        @if($art->image_path)
                            <div class="w-full sm:w-48 h-40 sm:h-36 rounded-md overflow-hidden bg-slate-100 shrink-0 border border-slate-200 relative">
                                <img src="{{ asset('storage/' . $art->image_path) }}" alt="{{ $art->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute top-2 left-2">
                                    <span class="px-2.5 py-0.5 rounded-md font-black text-white text-[10px] uppercase font-heading bg-crimson-700 shadow-xs">
                                        {{ $art->category->name }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="w-full sm:w-48 h-40 sm:h-36 rounded-md bg-blue-50 flex items-center justify-center text-brand-400 shrink-0 relative border border-blue-100">
                                <i class="ri-newspaper-line text-4xl"></i>
                                <div class="absolute top-2 left-2">
                                    <span class="px-2.5 py-0.5 rounded-md font-black text-white text-[10px] uppercase font-heading bg-crimson-700">
                                        {{ $art->category->name }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Text Details -->
                        <div class="flex-1 space-y-2 flex flex-col justify-between">
                            <div class="space-y-1.5">
                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium">
                                    <span class="text-brand-700 font-bold"><i class="ri-time-line"></i> {{ $art->published_at ? $art->published_at->diffForHumans() : $art->created_at->diffForHumans() }}</span>
                                    @if($art->district)
                                        <span>&bull;</span>
                                        <span class="font-bold text-slate-700"><i class="ri-map-pin-line text-crimson-600"></i> {{ $art->district }}</span>
                                    @endif
                                </div>

                                <a href="{{ route('articles.show', $art->slug) }}" class="block">
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-crimson-700 transition-colors line-clamp-2 leading-snug font-heading">
                                        {{ $art->title }}
                                    </h3>
                                </a>

                                <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed font-normal">
                                    {{ $art->excerpt }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                                <div class="flex items-center gap-2 text-[11px]">
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-brand-800 font-extrabold" title="Respon Positif">
                                        👍 {{ $art->positive_count }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-extrabold" title="Respon Negatif">
                                        👎 {{ $art->negative_count }}
                                    </span>
                                </div>

                                <a href="{{ route('articles.show', $art->slug) }}" class="text-crimson-700 font-bold hover:underline flex items-center gap-1 font-heading">
                                    Baca Selengkapnya <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

        </div>

        <!-- RIGHT COLUMN: SIDEBAR WIDGETS (5 COLS - DETIK / KOMPAS WIDGETS) -->
        <div class="lg:col-span-5 space-y-8">
            
            <!-- WIDGET 1: BERITA TERPOPULER (MOST READ TOP 5) -->
            @php 
                $popularArticles = \App\Models\Article::with('category')->orderBy('views_count', 'desc')->take(5)->get();
            @endphp

            <div class="bg-white rounded-md p-6 border border-slate-200 shadow-xs space-y-5">
                <div class="flex items-center justify-between border-b-2 border-crimson-600 pb-3">
                    <h3 class="text-base font-black uppercase tracking-wider text-slate-900 font-heading flex items-center gap-2">
                        <i class="ri-fire-fill text-amber-500"></i> BERITA TERPOPULER
                    </h3>
                    <span class="text-[11px] text-slate-400 font-bold">Top Read</span>
                </div>

                <div class="space-y-4">
                    @foreach($popularArticles as $rank => $pop)
                        <a href="{{ route('articles.show', $pop->slug) }}" class="flex items-start gap-3.5 group border-b border-slate-100 pb-3 last:border-0 last:pb-0 block">
                            <!-- Rank Number -->
                            <span class="text-2xl font-black font-heading text-slate-300 group-hover:text-crimson-700 transition-colors shrink-0 w-6 text-center">
                                0{{ $rank + 1 }}
                            </span>
                            <div class="space-y-1 flex-1 min-w-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white uppercase font-heading bg-brand-600 inline-block">
                                    {{ $pop->category->name }}
                                </span>
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-crimson-700 transition-colors line-clamp-2 leading-snug font-heading">
                                    {{ $pop->title }}
                                </h4>
                                <span class="text-[11px] text-slate-400 block font-medium"><i class="ri-eye-line text-brand-600"></i> {{ number_format($pop->views_count) }} kali dibaca</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- WIDGET 2: SENTIMEN & DASHBOARD CHART SUMMARY -->
            <div class="bg-gradient-to-br from-brand-950 to-navy-900 text-white rounded-md p-6 border border-brand-900 shadow-md space-y-4">
                <div class="flex items-center justify-between border-b border-blue-800 pb-3">
                    <h3 class="text-sm font-black uppercase tracking-wider text-white font-heading flex items-center gap-2">
                        <i class="ri-pie-chart-fill text-blue-400"></i> SENTIMEN NETIZEN METRO
                    </h3>
                </div>

                <div class="h-48 relative flex items-center justify-center">
                    <canvas id="sideSentChart"></canvas>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-2 rounded-md bg-blue-900/60 border border-blue-700">
                        <span class="font-black block text-blue-200 font-heading">{{ \App\Models\SocialComment::where('sentiment', 'positif')->count() }}</span>
                        <span class="text-[9px] uppercase font-semibold text-blue-300">Positif 👍</span>
                    </div>
                    <div class="p-2 rounded-md bg-slate-800 border border-slate-700">
                        <span class="font-black block text-slate-200 font-heading">{{ \App\Models\SocialComment::where('sentiment', 'negatif')->count() }}</span>
                        <span class="text-[9px] uppercase font-semibold text-slate-400">Negatif 👎</span>
                    </div>
                    <div class="p-2 rounded-md bg-blue-950 border border-blue-900">
                        <span class="font-black block text-blue-300 font-heading">{{ \App\Models\SocialComment::where('sentiment', 'netral')->count() }}</span>
                        <span class="text-[9px] uppercase font-semibold text-blue-400">Netral 💬</span>
                    </div>
                </div>
            </div>

            <!-- WIDGET 3: KATEGORI SEKTOR PERKOTAAN -->
            <div class="bg-white rounded-md p-6 border border-slate-200 shadow-xs space-y-4">
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 font-heading border-b-2 border-slate-900 pb-3">
                    Kategori Sektor Informasi Perkotaan
                </h3>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('articles.index', ['category' => $cat->slug]) }}" class="flex items-center justify-between p-3 rounded-md bg-slate-50 hover:bg-red-50 hover:text-crimson-700 transition-all font-heading text-xs font-bold border border-slate-100 group">
                            <span class="flex items-center gap-2 text-slate-800 group-hover:text-crimson-700">
                                <i class="{{ $cat->icon }} text-brand-600"></i> {{ $cat->name }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-slate-200 group-hover:bg-crimson-700 group-hover:text-white text-slate-700 text-[10px]">
                                {{ $cat->articles_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctxSent = document.getElementById('sideSentChart').getContext('2d');
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
                    backgroundColor: ['#3b82f6', '#94a3b8', '#cbd5e1'],
                    borderWidth: 2,
                    borderColor: '#0f172a'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { family: 'Plus Jakarta Sans', size: 10 }, color: '#cbd5e1' }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush
