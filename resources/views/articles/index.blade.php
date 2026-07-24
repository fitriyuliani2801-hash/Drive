@extends('layouts.app')

@section('title', 'Kategori Publikasi & Berita Terbaru - METROLOGI NEWS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Page Header Banner (National Media Style) -->
    <div class="bg-gradient-to-r from-slate-900 via-brand-950 to-slate-900 p-8 sm:p-10 rounded-3xl border border-slate-800 text-white relative overflow-hidden space-y-3 shadow-md">
        <div class="relative z-10">
            <span class="px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-crimson-700 text-white border border-red-500/30 font-heading">
                METRO TERKINI &amp; ARSIP BERITA
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white mt-3 font-heading">
                Kategori Publikasi &amp; Berita Terbaru
            </h1>
            <p class="text-slate-300 text-sm sm:text-base max-w-2xl mt-1">Jelajahi berita liputan khusus, isu terkini media sosial, serta hasil analisis sentimen komentar publik di Kota Metro.</p>
        </div>
    </div>

    <!-- Search & Filters Bar -->
    <form action="{{ route('articles.index') }}" method="GET" class="bg-white p-5 rounded-3xl border border-slate-200 space-y-4 md:space-y-0 md:flex items-center gap-4 shadow-xs">
        <div class="flex-1 relative">
            <i class="ri-search-2-line absolute left-4 top-3.5 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita, kata kunci isu perkotaan..." class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white font-medium">
        </div>

        <div class="w-full md:w-56">
            <select name="category" onchange="this.form.submit()" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-700 focus:outline-none focus:border-crimson-600 font-heading">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full md:w-56">
            <select name="district" onchange="this.form.submit()" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-700 focus:outline-none focus:border-crimson-600 font-heading">
                <option value="">Seluruh Kecamatan</option>
                @foreach($districts as $dist)
                    <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('articles.index') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center gap-2 transition-all font-heading border border-slate-200">
            <i class="ri-refresh-line"></i> Reset Filter
        </a>
    </form>

    <!-- Articles Grid -->
    <div class="space-y-6">
        <h2 class="text-xl font-black text-slate-900 flex items-center gap-2 font-heading border-b-2 border-slate-900 pb-3">
            <i class="ri-newspaper-line text-crimson-600"></i> Daftar Postingan Berita
        </h2>

        @if($articles->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center max-w-md mx-auto my-8 border border-slate-200 shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-3xl">
                    <i class="ri-newspaper-line"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1 font-heading">Belum Ada Artikel Berita</h3>
                <p class="text-xs text-slate-500 mb-5">Tidak ada berita yang sesuai dengan filter pencarian Anda.</p>
                <a href="{{ route('articles.index') }}" class="px-5 py-2.5 rounded-xl bg-crimson-700 text-white font-bold text-xs font-heading">Lihat Semua Berita</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $article)
                    <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 hover:border-crimson-600 transition-all flex flex-col justify-between shadow-xs hover:shadow-md group">
                        <div>
                            @if($article->image_path)
                                <div class="h-48 w-full bg-slate-100 overflow-hidden relative">
                                    <img src="{{ asset('storage/' . $article->image_path) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                            @endif

                            <div class="p-6 space-y-3">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="px-3 py-1 rounded-full font-black text-white text-[10px] uppercase font-heading bg-crimson-700">
                                        {{ $article->category->name }}
                                    </span>
                                    <span class="text-brand-700 text-[11px] font-bold"><i class="ri-eye-line"></i> {{ number_format($article->views_count) }} dibaca</span>
                                </div>

                                <a href="{{ route('articles.show', $article->slug) }}" class="block">
                                    <h3 class="text-base font-bold text-slate-900 group-hover:text-crimson-700 transition-colors line-clamp-2 leading-snug font-heading">
                                        {{ $article->title }}
                                    </h3>
                                </a>

                                <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed font-normal">
                                    {{ $article->excerpt }}
                                </p>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs font-medium">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-md bg-blue-100 text-brand-800 font-extrabold text-[10px]">
                                    👍 {{ $article->positive_count }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-md bg-slate-200 text-slate-700 font-extrabold text-[10px]">
                                    👎 {{ $article->negative_count }}
                                </span>
                            </div>
                            <a href="{{ route('articles.show', $article->slug) }}" class="text-crimson-700 font-bold hover:underline flex items-center gap-1 font-heading group-hover:translate-x-0.5 transition-transform">
                                Baca Selengkapnya <i class="ri-arrow-right-line"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
