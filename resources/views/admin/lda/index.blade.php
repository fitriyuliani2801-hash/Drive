@extends('layouts.admin')

@section('title', 'Peninjauan Redaksi & Publikasi Analisis LDA - Admin Redaksi')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <!-- Title & Action Header -->
    <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-xs font-bold text-teal-700 bg-teal-50 px-3 py-1 rounded-full border border-teal-200 uppercase tracking-wider font-heading">
                    <i class="ri-shield-check-line"></i> Ruang Kerja Peninjauan &amp; Publikasi Redaksi
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-2 font-heading">
                    Peninjauan &amp; Publikasi Analisis Topic LDA oleh Admin
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                    Setiap eksekusi pemodelan topik LDA akan menghasilkan <strong>Draf Redaksi (Unpublished)</strong>. Admin wajib meninjau kata kunci, menyunting nama label topik, dan secara manual menekan tombol <strong>Terbitkan Ke Publik</strong> sebelum hasil analisis tampil resmi di portal publik.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 self-start md:self-center">
                <form action="{{ route('admin.lda.run-analysis') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-2xl bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs shadow-md transition-all font-heading flex items-center gap-1.5">
                        <i class="ri-refresh-line text-base"></i> Jalankan Analisis Draf Baru
                    </button>
                </form>

                <form action="{{ route('admin.lda.publish-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENERBITKAN SELURUH topik analisis LDA ke publik?')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-2xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs shadow-md transition-all font-heading flex items-center gap-1.5">
                        <i class="ri-send-plane-fill text-base"></i> Terbitkan Seluruh Topik
                    </button>
                </form>
            </div>
        </div>

        <!-- Metrics Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider font-heading">Total Komentar Terkumpul</span>
                <p class="text-2xl font-black text-slate-900 font-heading">{{ number_format($totalComments) }} Komentar</p>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 space-y-1">
                <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider font-heading">Draf Peninjauan Admin</span>
                <p class="text-2xl font-black text-amber-700 font-heading">{{ $draftCount }} Topik Draf</p>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-1">
                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider font-heading">Telah Diterbitkan ke Publik</span>
                <p class="text-2xl font-black text-emerald-700 font-heading">{{ $publishedCount }} Topik Resmi</p>
            </div>
        </div>
    </div>

    <!-- INPUT DIRECT SOCIAL MEDIA LINK URL ANALYSIS FORM (DRAFT MODE) -->
    <div class="bg-white border-2 border-teal-500/40 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <span class="text-[10px] font-bold text-teal-700 uppercase tracking-widest bg-teal-50 px-3 py-1 rounded-full border border-teal-200 font-heading">
                <i class="ri-link text-teal-600"></i> Input Link Media Sosial Langsung
            </span>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2 font-heading">
                Analisis Pemodelan Topik (LDA) dari URL Media Sosial
            </h3>
            <p class="text-xs text-slate-500 mt-1">
                Masukkan URL postingan media sosial (Instagram, X / Twitter, Facebook, TikTok, atau Berita Online) beserta teks komentar publiknya. Hasil analisis akan diproses sebagai <strong>DRAF REDAKSI (Belum Terbit)</strong> dan <strong>TIDAK akan diterbitkan ke publik secara otomatis</strong>.
            </p>
        </div>

        <form action="{{ route('admin.lda.analyze-url') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-6 space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase font-heading">URL Post Media Sosial <span class="text-rose-500">*</span></label>
                    <div class="relative flex items-center">
                        <i class="ri-link-m text-slate-400 absolute left-3.5"></i>
                        <input type="url" name="post_url" placeholder="https://www.instagram.com/p/DFX123... atau https://x.com/..." required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors font-mono">
                    </div>
                </div>

                <div class="md:col-span-3 space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase font-heading">Platform Media Sosial <span class="text-rose-500">*</span></label>
                    <select name="platform" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors font-heading">
                        <option value="Instagram">Instagram</option>
                        <option value="X (Twitter)">X (Twitter)</option>
                        <option value="Berita Online Lampung">Berita Online Lampung</option>
                        <option value="Facebook">Facebook</option>
                        <option value="TikTok">TikTok</option>
                    </select>
                </div>

                <div class="md:col-span-3 space-y-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase font-heading">Nama Akun Sumber <span class="text-rose-500">*</span></label>
                    <input type="text" name="source_account" placeholder="@pemkotmetro atau @radar_lampung" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors font-medium">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 uppercase font-heading">Data Komentar Publik (Teks Mentah per Baris) <span class="text-rose-500">*</span></label>
                <textarea name="comments_text" rows="5" placeholder="Pastekan komentar publik netizen dari post media sosial di atas (satu komentar per baris)...&#10;Contoh: Tolong lubang jalan AH Nasution depan 21A segera ditambal bahaya malam hari!&#10;Pasar kreatif kuliner kulon progo sangat bagus untuk UMKM Metro!" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors leading-relaxed font-sans"></textarea>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <span class="text-[11px] text-amber-700 font-bold bg-amber-50 px-3 py-1 rounded-full border border-amber-200 flex items-center gap-1 font-heading">
                    <i class="ri-shield-flash-line text-amber-600"></i> Mode Draf Redaksi: Hasil analisis tidak langsung terbit ke publik.
                </span>

                <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs shadow-md transition-all font-heading flex items-center gap-2">
                    <i class="ri-pulse-line text-base"></i> Jalankan Analisis LDA (Simpan Draf)
                </button>
            </div>
        </form>
    </div>

    <!-- LDA Topics Editorial Review Grid -->
    <div class="space-y-4">
        <h3 class="text-lg font-black text-slate-900 font-heading">Daftar Klaster Topik &amp; Kontrol Publikasi Admin</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($topics as $topic)
                <div class="bg-white border-2 rounded-3xl p-6 shadow-xs space-y-5 flex flex-col justify-between transition-all {{ $topic->is_published ? 'border-emerald-300 bg-emerald-50/10' : 'border-amber-300 bg-amber-50/10' }}">
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full font-black text-white text-xs uppercase font-heading shadow-2xs" style="background-color: {{ $topic->category->color_code ?? '#0d9488' }}">
                                    {{ $topic->category->name ?? 'Umum' }}
                                </span>
                                <span class="text-xs font-bold text-slate-500 font-heading">Topik #{{ $topic->topic_number }}</span>
                            </div>

                            <!-- Publication Status Badge -->
                            @if($topic->is_published)
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-extrabold flex items-center gap-1 font-heading">
                                    <i class="ri-checkbox-circle-fill text-emerald-600"></i> PUBLISHED (Terbit)
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-xs font-extrabold flex items-center gap-1 font-heading">
                                    <i class="ri-draft-line text-amber-600"></i> DRAFT REDAKSI
                                </span>
                            @endif
                        </div>

                        <!-- Topic Title / Label -->
                        <div>
                            <h4 class="text-lg font-black text-slate-900 font-heading leading-snug">{{ $topic->label }}</h4>
                            <p class="text-xs text-slate-500 mt-1 font-semibold">
                                <i class="ri-chat-1-line text-teal-600"></i> {{ $topic->comments_count }} Komentar Netizen Terklaster | Coherence Score: <strong class="font-mono text-slate-800">{{ $topic->coherence_score }}</strong>
                            </p>
                        </div>

                        <!-- Top Keywords Grid -->
                        <div class="pt-3 border-t border-slate-200/80 space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600 block font-heading">Kata Kunci Utama &amp; Pembobotan Numerik:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(array_slice($topic->keywords ?? [], 0, 10) as $kw)
                                    <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-xs font-medium border border-slate-200">
                                        {{ $kw['word'] }} <span class="text-teal-700 font-mono font-bold">({{ $kw['weight'] }})</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Admin Actions Bar -->
                    <div class="pt-4 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-2 font-heading">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.lda.topics.edit', $topic->id) }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 hover:border-teal-600 hover:text-teal-700 text-slate-700 font-bold text-xs transition-all shadow-xs flex items-center gap-1">
                                <i class="ri-edit-line text-teal-600"></i> Sunting
                            </a>

                            <form action="{{ route('admin.lda.topics.delete', $topic->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENGHAPUS topik ini?')">
                                @csrf
                                <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 border border-rose-200 hover:bg-rose-600 hover:text-white text-rose-700 font-bold text-xs transition-all flex items-center gap-1">
                                    <i class="ri-delete-bin-line"></i> Hapus Topik
                                </button>
                            </form>
                        </div>

                        @if($topic->is_published)
                            <form action="{{ route('admin.lda.topics.unpublish', $topic->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-colors flex items-center gap-1">
                                    <i class="ri-draft-line text-slate-500"></i> Kembalikan ke Draft
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.lda.topics.publish', $topic->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md transition-all flex items-center gap-1">
                                    <i class="ri-send-plane-fill"></i> Terbitkan ke Publik
                                </button>
                            </form>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
