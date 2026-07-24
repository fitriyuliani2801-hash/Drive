@extends('layouts.app')

@section('title', 'Data Komentar Scraper - Metrologi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Title Header -->
    <div class="bg-white border border-slate-200/90 rounded-md p-6 sm:p-8 shadow-xs space-y-2">
        <span class="text-xs font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-md border border-teal-200 uppercase tracking-wider">
            <i class="ri-database-2-line"></i> Data Scraper & Aggregator
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2">
            Data Komentar Mentah Hasil Scraper Sosial Media
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
            Kumpulan komentar masyarakat Kota Metro yang terkumpul secara otomatis dari akun publik Instagram, X (Twitter), dan Berita Online Lampung.
        </p>
    </div>

    <!-- Filter Form -->
    <form action="{{ route('analysis.comments') }}" method="GET" class="bg-white border border-slate-200/90 p-4 rounded-md flex flex-col md:flex-row items-center gap-4 shadow-xs">
        <div class="flex-1 relative w-full">
            <i class="ri-search-line absolute left-3.5 top-3.5 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari penulis, akun, atau isi komentar..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md text-sm text-slate-900 focus:outline-none focus:border-teal-600">
        </div>

        <div class="w-full md:w-56">
            <select name="platform" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-md text-xs font-bold text-slate-700 focus:outline-none focus:border-teal-600">
                <option value="">Semua Platform</option>
                @foreach($platforms as $plat)
                    <option value="{{ $plat }}" {{ request('platform') == $plat ? 'selected' : '' }}>{{ $plat }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('analysis.comments') }}" class="px-4 py-2.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">Reset</a>
    </form>

    <!-- Table -->
    <div class="bg-white border border-slate-200/90 rounded-md overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Tabel Komentar Dicrawl</h3>
            <span class="text-xs text-slate-500">Total: {{ $comments->total() }} Komentar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-4">Platform & Akun Scraped</th>
                        <th class="px-5 py-4">Penulis</th>
                        <th class="px-5 py-4">Teks Komentar Mentah (Raw)</th>
                        <th class="px-5 py-4">Kategori & Topik LDA</th>
                        <th class="px-5 py-4">Waktu Dicrawl</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($comments as $comm)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md font-bold bg-slate-100 text-slate-800 text-[10px] block w-fit mb-1 border border-slate-200">
                                    {{ $comm->platform }}
                                </span>
                                <span class="text-[10px] font-mono text-teal-700 font-bold">{{ $comm->source_account }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-bold text-slate-900">
                                {{ $comm->author_name }}
                            </td>
                            <td class="px-5 py-4 text-slate-800 max-w-md leading-relaxed font-normal">
                                "{{ $comm->raw_text }}"
                            </td>
                            <td class="px-5 py-4 space-y-1">
                                <span class="px-2 py-0.5 rounded font-bold text-white text-[10px] inline-block" style="background-color: {{ $comm->category->color_code ?? '#3B82F6' }}">
                                    {{ $comm->category->name ?? '-' }}
                                </span>
                                <p class="text-[10px] text-slate-500">{{ $comm->ldaTopic->label ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-500 whitespace-nowrap">
                                {{ $comm->scraped_at ? $comm->scraped_at->diffForHumans() : $comm->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                Belum ada komentar mentah dicrawl.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $comments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
