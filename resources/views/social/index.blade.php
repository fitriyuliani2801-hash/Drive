@extends('layouts.app')

@section('title', 'Analisis Link Medsos, Deteksi Hoaks & Sentimen Komentar - Metrologi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Hero Banner with URL Input Form -->
    <div class="bg-gradient-to-br from-white via-teal-50/50 to-slate-50 border border-slate-200/90 rounded-3xl p-6 sm:p-10 shadow-xs space-y-6">
        <div class="max-w-3xl space-y-2">
            <span class="text-xs font-bold text-teal-700 bg-teal-100/80 px-3 py-1 rounded-full border border-teal-200 uppercase tracking-wider">
                <i class="ri-link text-amber-500"></i> Social Media Analyzer & Fact-Checker Engine
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                Masukkan Link URL Instagram / Facebook / TikTok
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Tempelkan link URL postingan berita dari sosial media. Sistem akan menyaring keaslian berita (<strong>Berita Asli / Fakta vs Hoaks / Disinformasi</strong>) serta mengekstrak dan memisahkan <strong>Komentar Positif</strong> dan <strong>Komentar Negatif</strong>.
            </p>
        </div>

        <!-- URL Submission Form -->
        <form action="{{ route('social.analyze') }}" method="POST" class="max-w-4xl space-y-3">
            @csrf
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <i class="ri-link absolute left-4 top-4 text-slate-400 text-lg"></i>
                    <input type="url" name="url" value="{{ old('url') }}" required placeholder="Tempelkan link URL: https://www.instagram.com/p/... atau TikTok / Facebook" class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl text-sm text-slate-900 shadow-sm focus:outline-none focus:border-teal-600 transition-colors">
                </div>

                <button type="submit" class="w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-sm shadow-md shadow-teal-600/20 transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                    <i class="ri-search-eye-line text-lg"></i> Analisis Berita & Komentar
                </button>
            </div>
            
            <div class="flex items-center gap-4 text-[11px] text-slate-500 font-medium pt-1">
                <span>Contoh platform didukung:</span>
                <span class="flex items-center gap-1 text-pink-600 font-semibold"><i class="ri-instagram-line"></i> Instagram</span>
                <span class="flex items-center gap-1 text-blue-600 font-semibold"><i class="ri-facebook-box-line"></i> Facebook</span>
                <span class="flex items-center gap-1 text-slate-900 font-semibold"><i class="ri-tiktok-line"></i> TikTok</span>
            </div>
        </form>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white border border-slate-200/90 p-5 rounded-2xl shadow-xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Link Medsos Di-Analisis</span>
            <p class="text-3xl font-black text-slate-900">{{ number_format($totalAnalyzed) }}</p>
            <p class="text-[11px] text-slate-500">Postingan Instagram, FB & TikTok</p>
        </div>

        <div class="bg-white border border-emerald-200 p-5 rounded-2xl shadow-xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Terverifikasi Berita Asli (Fakta)</span>
            <p class="text-3xl font-black text-emerald-700">{{ number_format($totalFacts) }}</p>
            <p class="text-[11px] text-emerald-800 font-semibold">Konten valid dari sumber resmi</p>
        </div>

        <div class="bg-white border border-rose-200 p-5 rounded-2xl shadow-xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Terindikasi Hoaks / Disinformasi</span>
            <p class="text-3xl font-black text-rose-700">{{ number_format($totalHoaxes) }}</p>
            <p class="text-[11px] text-rose-800 font-semibold">Bohong & klaim tidak valid</p>
        </div>
    </div>

    <!-- Filter & Search Bar for Catalog -->
    <form action="{{ route('social.index') }}" method="GET" class="bg-white border border-slate-200/90 p-4 rounded-2xl flex flex-col md:flex-row items-center gap-4 shadow-xs">
        <div class="flex-1 relative w-full">
            <i class="ri-search-line absolute left-3.5 top-3.5 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita, URL, atau akun pembuat..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600">
        </div>

        <div class="w-full md:w-48">
            <select name="verdict" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-600">
                <option value="">Semua Status Keaslian</option>
                <option value="asli" {{ request('verdict') == 'asli' ? 'selected' : '' }}>Berita Asli (Fakta)</option>
                <option value="hoaks" {{ request('verdict') == 'hoaks' ? 'selected' : '' }}>Hoaks / Disinformasi</option>
            </select>
        </div>

        <div class="w-full md:w-48">
            <select name="platform" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-600">
                <option value="">Semua Platform</option>
                <option value="Instagram" {{ request('platform') == 'Instagram' ? 'selected' : '' }}>Instagram</option>
                <option value="Facebook" {{ request('platform') == 'Facebook' ? 'selected' : '' }}>Facebook</option>
                <option value="TikTok" {{ request('platform') == 'TikTok' ? 'selected' : '' }}>TikTok</option>
            </select>
        </div>

        <a href="{{ route('social.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">Reset</a>
    </form>

    <!-- Catalog Cards Grid -->
    @if($analyses->isEmpty())
        <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center max-w-md mx-auto shadow-xs">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-3xl">
                <i class="ri-link-unlink-m"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Analisis Link Ditemukan</h3>
            <p class="text-xs text-slate-500 mb-6">Masukkan link URL Instagram, Facebook, atau TikTok di atas untuk memulai analisis.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($analyses as $item)
                <div class="bg-white border border-slate-200/90 rounded-3xl overflow-hidden hover:border-slate-300 transition-all flex flex-col justify-between shadow-xs hover:shadow-md">
                    <div>
                        <!-- Header Verdict Bar -->
                        <div class="p-4 border-b border-slate-100 flex items-center justify-between {{ $item->verdict == 'asli' ? 'bg-emerald-50/70' : 'bg-rose-50/70' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $item->verdict == 'asli' ? 'bg-emerald-600' : 'bg-rose-600' }}"></span>
                                <span class="font-extrabold text-xs uppercase tracking-wider {{ $item->verdict == 'asli' ? 'text-emerald-800' : 'text-rose-800' }}">
                                    {{ $item->verdict == 'asli' ? 'Berita Asli (Fakta)' : 'Hoaks / Disinformasi' }}
                                </span>
                            </div>

                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-white text-slate-700 border border-slate-200">
                                Skor: {{ $item->verdict_score }}%
                            </span>
                        </div>

                        <!-- Content Body -->
                        <div class="p-5 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-800 text-[11px] border border-slate-200">
                                    {{ $item->platform }}
                                </span>
                                <span class="text-slate-500 text-[11px] truncate max-w-[140px]"><i class="ri-user-3-line"></i> {{ $item->author_name }}</span>
                            </div>

                            <a href="{{ route('social.show', $item->id) }}" class="block">
                                <h3 class="text-base font-bold text-slate-900 hover:text-teal-700 transition-colors line-clamp-2 leading-snug">
                                    {{ $item->post_title }}
                                </h3>
                            </a>

                            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $item->post_content }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer Comments Sentiments Breakdown -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1 text-emerald-700 font-bold" title="Komentar Positif">
                                <i class="ri-thumb-up-line"></i> {{ $item->positive_count }}
                            </span>
                            <span class="flex items-center gap-1 text-rose-700 font-bold" title="Komentar Negatif">
                                <i class="ri-thumb-down-line"></i> {{ $item->negative_count }}
                            </span>
                        </div>

                        <a href="{{ route('social.show', $item->id) }}" class="text-teal-700 font-bold hover:underline flex items-center gap-1">
                            Detail & Komentar <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $analyses->links() }}
        </div>
    @endif

</div>
@endsection
