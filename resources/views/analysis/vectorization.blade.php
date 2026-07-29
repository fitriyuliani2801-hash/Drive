@extends('layouts.admin')

@section('title', 'Langkah 3: Inspeksi Matriks DTM & TF-IDF Vectorization - Admin Redaksi')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <!-- Title Header -->
    <div class="bg-white border border-slate-200/90 rounded-md p-6 sm:p-8 shadow-xs space-y-2">
        <div class="flex items-center justify-between">

            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-md border border-indigo-200 uppercase tracking-wider font-heading">
                <i class="ri-matrix-line"></i> Langkah 3: Vectorization & DTM Matrix

            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200 uppercase tracking-wider font-heading">
                <i class="ri-matrix-line"></i> Langkah 3: Vectorization &amp; DTM Matrix

            </span>
            <a href="{{ route('admin.analysis.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 font-heading">
                &larr; Kembali ke Dashboard LDA
            </a>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 font-heading">
            Inspeksi Matriks Dokumen (Document-Term Matrix &amp; TF-IDF)
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
            Menampilkan pembentukan <strong>Kamus Kata Unik (Vocabulary Index)</strong> dan transformasi dokumen teks komentar mentah menjadi <strong>Matriks Frekuensi Kata (Document-Term Matrix / DTM)</strong> dan <strong>Matriks Pembobotan TF-IDF</strong> sebelum dimasukkan ke dalam estimasi LDA.
        </p>
    </div>

    <!-- Overview Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white border border-slate-200/90 rounded-md p-5 shadow-xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 font-heading">Total Sampel Dokumen (D)</span>
            <p class="text-3xl font-black text-slate-900 font-heading">{{ number_format($totalDocs) }} Dokumen</p>
            <p class="text-[11px] text-slate-500">Teks komentar publik terproses</p>
        </div>

        <div class="bg-white border border-indigo-200 rounded-md p-5 shadow-xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 font-heading">Ukuran Kamus Kata (V)</span>
            <p class="text-3xl font-black text-indigo-700 font-heading">{{ number_format(count($vocabulary)) }} Kata Unik</p>
            <p class="text-[11px] text-indigo-800 font-semibold">Vocabulary Terms Index</p>
        </div>

        <div class="bg-white border border-teal-200 rounded-md p-5 shadow-xs space-y-1">
            <span class="text-xs font-bold uppercase tracking-wider text-teal-700 font-heading">Metode Pembobotan</span>
            <p class="text-2xl font-black text-teal-700 font-heading">TF-IDF &amp; Bag-of-Words</p>
            <p class="text-[11px] text-teal-800 font-semibold">Term Frequency-Inverse Document Freq</p>
        </div>
    </div>

    <!-- Vocabulary Index Dictionary Table -->
    <div class="bg-white border border-slate-200/90 rounded-md overflow-hidden shadow-xs space-y-4 p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 font-heading">
                    <i class="ri-book-open-line text-indigo-600"></i> 1. Kamus Kata Unik (Vocabulary Terms Index)
                </h3>
                <p class="text-xs text-slate-500">Daftar kata unik terdistribusi hasil tokenization &amp; stemming.</p>
            </div>
            <span class="text-xs text-indigo-700 font-bold bg-indigo-50 px-3 py-1 rounded-md border border-indigo-200">
                {{ count($vocabulary) }} Kata Terdaftar
            </span>
        </div>

        <div class="flex flex-wrap gap-2 max-h-56 overflow-y-auto p-4 bg-slate-50 rounded-md border border-slate-200 custom-scrollbar">
            @foreach(array_slice($vocabulary, 0, 60, true) as $word => $info)
                <span class="px-3 py-1.5 rounded-md bg-white border border-slate-200 text-slate-800 text-xs font-medium shadow-2xs flex items-center gap-1.5">
                    <strong class="text-indigo-700 font-mono">#{{ $info['id'] }}</strong> {{ $word }}
                    <span class="text-[10px] text-slate-400 font-mono font-bold">({{ $info['total_freq'] }}x / {{ $info['doc_count'] }} doc)</span>
                </span>
            @endforeach
        </div>
    </div>

    <!-- Document-Term Matrix (DTM) & TF-IDF Weights Table -->
    <div class="bg-white border border-slate-200/90 rounded-md overflow-hidden shadow-xs">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 font-heading">
                    <i class="ri-table-line text-teal-600"></i> 2. Matriks Pembobotan TF-IDF per Dokumen Komentar
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Nilai numerik bobot $TF \times IDF$ untuk tiap kata dalam sampel dokumen komentar.</p>
            </div>
            <span class="text-xs text-slate-500 font-medium">Menampilkan 20 Sampel Dokumen Terakhir</span>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-xs text-slate-700 min-w-[900px]">
                @php
                    $sampleWords = array_keys(array_slice($vocabulary, 0, 10, true));
                @endphp
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4 w-12 text-center">ID Doc</th>
                        <th class="px-4 py-4 max-w-xs">Sampel Teks Dokumen</th>
                        @foreach($sampleWords as $sWord)
                            <th class="px-3 py-4 text-center font-mono text-indigo-700 bg-indigo-50/50 border-x border-slate-200">
                                {{ $sWord }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($comments as $comm)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-center text-slate-900">
                                D-{{ $comm->id }}
                            </td>
                            <td class="px-4 py-3 max-w-xs leading-snug">
                                <span class="text-[10px] text-teal-700 font-bold block mb-0.5">[{{ $comm->platform }}]</span>
                                <span class="text-slate-800 line-clamp-2">"{{ $comm->raw_text }}"</span>
                            </td>
                            @foreach($sampleWords as $sWord)
                                @php
                                    $val = $tfidfMatrix[$comm->id][$sWord] ?? 0;
                                @endphp
                                <td class="px-3 py-3 text-center font-mono border-x border-slate-100 {{ $val > 0 ? 'bg-teal-50/70 text-teal-900 font-bold' : 'text-slate-300' }}">
                                    {{ $val > 0 ? number_format($val, 3) : '0.000' }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($sampleWords) + 2 }}" class="px-4 py-8 text-center text-slate-500">
                                Belum ada data dokumen untuk ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
