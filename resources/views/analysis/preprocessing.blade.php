@extends('layouts.admin')

@section('title', 'Inspeksi Pre-Processing Teks NLP - Admin Redaksi')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <!-- Title Header -->

    <div class="bg-white border border-slate-200/90 rounded-md p-6 sm:p-8 shadow-xs space-y-2">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-md border border-teal-200 uppercase tracking-wider">

    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-full border border-teal-200 uppercase tracking-wider font-heading">

                <i class="ri-sound-module-line"></i> NLP Pre-Processing Pipeline
            </span>
            <a href="{{ route('admin.analysis.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 font-heading">
                &larr; Kembali ke Dashboard LDA
            </a>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 font-heading">
            Transparansi Tahapan Pre-Processing Teks Bahasa Indonesia
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
            Menampilkan hasil transformasi dari data mentah komentar publik melalui 4 tahapan pre-processing utama sebelum diumpankan ke algoritma pemodelan topik LDA.
        </p>
    </div>

    <!-- 4 Stages Diagram Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-200/90 p-5 rounded-md space-y-2 shadow-xs">
            <div class="w-8 h-8 rounded-md bg-slate-100 text-slate-700 font-black text-sm flex items-center justify-center border border-slate-200">1</div>
            <h4 class="font-bold text-slate-900 text-sm">Raw Text Collection</h4>
            <p class="text-xs text-slate-500">Teks opini mentah hasil crawler dari Instagram, X, dan Berita Online.</p>
        </div>

        <div class="bg-white border border-teal-200 p-5 rounded-md space-y-2 shadow-xs">
            <div class="w-8 h-8 rounded-md bg-teal-100 text-teal-700 font-black text-sm flex items-center justify-center border border-teal-200">2</div>
            <h4 class="font-bold text-teal-900 text-sm">Cleaning & Case Folding</h4>
            <p class="text-xs text-teal-700">Pembersihan URL, hashtag, mention, emoji, angka, dan penyeragaman huruf kecil.</p>
        </div>

        <div class="bg-white border border-indigo-200 p-5 rounded-md space-y-2 shadow-xs">
            <div class="w-8 h-8 rounded-md bg-indigo-100 text-indigo-700 font-black text-sm flex items-center justify-center border border-indigo-200">3</div>
            <h4 class="font-bold text-indigo-900 text-sm">Stopword Removal</h4>
            <p class="text-xs text-indigo-700">Menghilangkan kata umum tanpa makna khusus (seperti <em>yang, di, dari, ini</em>).</p>
        </div>

        <div class="bg-white border border-emerald-200 p-5 rounded-md space-y-2 shadow-xs">
            <div class="w-8 h-8 rounded-md bg-emerald-100 text-emerald-700 font-black text-sm flex items-center justify-center border border-emerald-200">4</div>
            <h4 class="font-bold text-emerald-900 text-sm">Indonesian Stemming</h4>
            <p class="text-xs text-emerald-700">Mengubah kata berimbuhan ke dalam bentuk dasar bahasa Indonesia.</p>
        </div>
    </div>

    <!-- Search Form -->
    <form action="{{ route('admin.analysis.preprocessing') }}" method="GET" class="bg-white border border-slate-200/90 p-4 rounded-2xl flex items-center gap-4 shadow-xs">

        <div class="flex-1 relative">
            <i class="ri-search-line absolute left-3.5 top-3.5 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari teks komentar..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md text-sm text-slate-900 focus:outline-none focus:border-teal-600">
        </div>
        <button type="submit" class="px-4 py-2.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">Cari Teks</button>
    </form>

    <!-- Detailed Inspection Table -->
    <div class="bg-white border border-slate-200/90 rounded-md overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Inspeksi Tahapan Transformasi Teks</h3>
            <span class="text-xs text-slate-500">Total: {{ $comments->total() }} Data Komentar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4">Tahap 1: Raw Text</th>
                        <th class="px-4 py-4">Tahap 2: Cleaned & Lowercase</th>
                        <th class="px-4 py-4">Tahap 3: Stopword Tokens</th>
                        <th class="px-4 py-4">Tahap 4: Stemmed Tokens</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($comments as $comm)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- 1. Raw -->
                            <td class="px-4 py-4 max-w-xs leading-relaxed text-slate-900 font-medium">
                                <span class="text-[10px] text-teal-700 font-bold block mb-1">[{{ $comm->platform }}] {{ $comm->source_account }}</span>
                                "{{ $comm->raw_text }}"
                            </td>

                            <!-- 2. Cleaned -->
                            <td class="px-4 py-4 max-w-xs text-slate-600 font-mono text-[11px] leading-relaxed">
                                {{ $comm->cleaned_text }}
                            </td>

                            <!-- 3. Tokens -->
                            <td class="px-4 py-4 max-w-xs">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($comm->tokens ?? [] as $tok)
                                        <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200 font-mono text-[10px]">
                                            {{ $tok }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- 4. Stemmed -->
                            <td class="px-4 py-4 max-w-xs">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($comm->stemmed_tokens ?? [] as $stem)
                                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono font-bold text-[10px]">
                                            {{ $stem }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-slate-500">
                                Tidak ada data komentar ditemukan.
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
