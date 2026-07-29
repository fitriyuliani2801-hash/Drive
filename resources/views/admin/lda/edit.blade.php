@extends('layouts.admin')

@section('title', 'Sunting Draf Topik LDA - Admin Redaksi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.lda.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-slate-900 mb-2 font-heading">
            <i class="ri-arrow-left-line"></i> Kembali ke Peninjauan LDA
        </a>
        <h1 class="text-2xl font-black text-slate-900 font-heading">Sunting Draf Topik LDA #{{ $topic->topic_number }}</h1>
        <p class="text-xs text-slate-500 mt-1">Ubah nama label deskriptif topik, kategori isu perkotaan, dan penyesuaian bobot kata kunci sebelum diterbitkan ke portal publik.</p>
    </div>

    <form action="{{ route('admin.lda.topics.update', $topic->id) }}" method="POST" class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Nama Label Topik Deskriptif <span class="text-rose-500">*</span></label>
                <input type="text" name="label" value="{{ old('label', $topic->label) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors font-medium">
                <p class="text-[11px] text-slate-400 mt-1">Contoh: Topik 1: Isu Perbaikan Jalan Berlubang &amp; Infrastruktur Perkotaan</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Kategori Sektor Perkotaan <span class="text-rose-500">*</span></label>
                <select name="category_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors font-heading">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $topic->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Kata Kunci Utama &amp; Pembobotan Numerik (Format: `kata:bobot` per baris)</label>
            <p class="text-[11px] text-slate-500 mb-2">Masukkan pasangan kata kunci dan nilai bobotnya (antara 0.1 hingga 1.0) satu kata per baris.</p>
            
            @php
                $kwLines = [];
                foreach ($topic->keywords ?? [] as $kw) {
                    $kwLines[] = ($kw['word'] ?? '') . ':' . ($kw['weight'] ?? 0.85);
                }
                $kwText = implode("\n", $kwLines);
            @endphp

            <textarea name="keywords_text" rows="8" required placeholder="jalan:0.95&#10;berlubang:0.88&#10;aspal:0.75" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors leading-relaxed">{{ old('keywords_text', $kwText) }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100 font-heading">
            <a href="{{ route('admin.lda.index') }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition-colors">
                Batal
            </a>

            <button type="submit" class="px-6 py-3 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs shadow-md transition-all">
                Simpan Perubahan Draf
            </button>
        </div>
    </form>
</div>
@endsection
