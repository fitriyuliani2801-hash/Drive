@extends('layouts.admin')

@section('title', 'Kelola Berita & Artikel - Redaksi Metrologi')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-red-50 text-crimson-700 border border-red-200 font-heading">
                EDITORIAL MANAGEMENT
            </span>
            <h1 class="text-2xl font-black text-slate-900 mt-1 font-heading">Kelola Berita &amp; Artikel Publikasi</h1>
            <p class="text-xs text-slate-500 font-medium">Daftar seluruh berita liputan khusus, isu media sosial, serta verifikasi screenshot komentar netizen.</p>
        </div>

        <a href="{{ route('admin.articles.create') }}" class="px-5 py-3 rounded-xl bg-crimson-700 hover:bg-crimson-800 text-white font-extrabold text-xs shadow-md transition-all flex items-center gap-1.5 self-start font-heading">
            <i class="ri-add-circle-line text-sm"></i> Tulis Berita Baru
        </a>
    </div>

    <!-- Import Link Box -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 font-heading flex items-center gap-1.5">
                <i class="ri-links-line text-crimson-700"></i> Import Berita Otomatis dari Link Medsos
            </h3>
            <p class="text-[11px] text-slate-500">Masukkan link rujukan Instagram, TikTok, atau Facebook untuk meng-import berita beserta data komentar rujukan secara otomatis.</p>
        </div>
        <form action="{{ route('admin.articles.import-link') }}" method="POST" class="space-y-3">
            @csrf
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="url" name="url" required placeholder="Contoh: https://www.instagram.com/p/... atau Link Facebook / TikTok" class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white font-medium">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-crimson-700 text-white font-extrabold text-xs transition-colors font-heading flex items-center justify-center gap-1.5 shadow-xs shrink-0">
                    <i class="ri-download-cloud-line"></i> Import &amp; Terbitkan
                </button>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1 font-heading">Salin &amp; Tempel Teks Komentar Medsos Rujukan (Opsional - Satu Komentar per Baris)</label>
                <textarea name="comments_text" rows="3" placeholder="Tempelkan (copy-paste) komentar asli dari postingan media sosial di sini (satu baris per komentar) jika Anda ingin meng-import komentar aslinya..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors leading-relaxed font-normal"></textarea>
            </div>
        </form>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('admin.articles.index') }}" method="GET" class="bg-white p-4 rounded-3xl border border-slate-200 flex flex-col sm:flex-row items-center gap-3 shadow-xs">
        <div class="flex-1 relative w-full">
            <i class="ri-search-line absolute left-3.5 top-3 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white font-medium">
        </div>

        <div class="w-full sm:w-48">
            <select name="category" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-crimson-600 font-heading">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('admin.articles.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold font-heading border border-slate-200">
            Reset
        </a>
    </form>

    <!-- Articles Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900 text-white font-heading uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Berita &amp; Headline</th>
                        <th class="px-4 py-4">Kategori</th>
                        <th class="px-4 py-4">Kecamatan</th>
                        <th class="px-4 py-4">Pembaca</th>
                        <th class="px-4 py-4">Sentimen Netizen</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($articles as $art)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 max-w-xs">
                                <div class="space-y-1">
                                    <a href="{{ route('articles.show', $art->slug) }}" target="_blank" class="font-bold text-slate-900 hover:text-crimson-700 line-clamp-2 leading-snug font-heading text-sm">
                                        {{ $art->title }}
                                    </a>
                                    <span class="text-[10px] text-slate-400 block font-normal">{{ $art->created_at ? $art->created_at->format('d M Y H:i') : '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-1 rounded-full font-black text-[10px] uppercase font-heading bg-crimson-700 text-white">
                                    {{ $art->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-4 font-semibold text-slate-700">
                                {{ $art->district ?: 'Seluruh Metro' }}
                            </td>
                            <td class="px-4 py-4 font-bold text-brand-700 font-heading">
                                <i class="ri-eye-line"></i> {{ number_format($art->views_count) }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-brand-800">👍 {{ $art->positive_count }}</span>
                                    <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-700">👎 {{ $art->negative_count }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                @if($art->is_featured)
                                    <span class="px-2.5 py-1 rounded-full font-black text-[10px] bg-amber-100 text-amber-900 border border-amber-300 font-heading">
                                        ⭐ HEADLINE
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] bg-slate-100 text-slate-600 font-semibold">
                                        Standar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.articles.edit', $art->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors font-heading">
                                        <i class="ri-edit-line"></i> Sunting
                                    </a>
                                    <form action="{{ route('admin.articles.destroy', $art->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors font-heading">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-xs">
                                Belum ada artikel berita yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
