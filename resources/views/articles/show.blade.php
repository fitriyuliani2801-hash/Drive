@extends('layouts.app')

@section('title', $article->title . ' - METROLOGI NEWS')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Back Navigation Link -->
    <div>
        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-xs font-extrabold text-crimson-700 hover:text-crimson-800 transition-colors font-heading bg-white px-4 py-2 rounded-full border border-slate-200 shadow-xs">
            <i class="ri-arrow-left-line"></i> Kembali ke Metro Terkini
        </a>
    </div>

    <!-- 2-COLUMN GRID WRAPPER (ARTICLE LEFT, COMMENTS RIGHT) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: MAIN ARTICLE (7 COLS) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- MAIN ARTICLE READING CARD (GAYA DETIK & KOMPAS) -->
            <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-6 relative overflow-hidden">
        
        <!-- Badges & Category Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 relative z-10">
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1 rounded-full text-xs font-black text-white uppercase tracking-wider font-heading shadow-xs bg-crimson-700">
                    {{ $article->category->name }}
                </span>

                @if($article->is_featured)
                    <span class="px-3.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1.5 font-heading">
                        <i class="ri-star-fill text-amber-500"></i> HEADLINE UTAMA
                    </span>
                @endif

                @if($article->verdict === 'asli')
                    <span class="px-3.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-900 border border-emerald-300 flex items-center gap-1.5 font-heading">
                        <i class="ri-checkbox-circle-fill text-emerald-600"></i> BERITA ASLI (FAKTA)
                    </span>
                @elseif($article->verdict === 'hoaks')
                    <span class="px-3.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-900 border border-rose-300 flex items-center gap-1.5 font-heading">
                        <i class="ri-close-circle-fill text-rose-600"></i> BERITA HOAKS (DISINFORMASI)
                    </span>
                @endif
            </div>

            <span class="text-xs text-brand-700 font-bold ml-auto flex items-center gap-1.5 font-heading">
                <i class="ri-eye-line text-brand-600"></i> {{ number_format($article->views_count) }} Kali Dibaca
            </span>
        </div>

        <!-- Editorial Headline Title -->
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-tight font-heading tracking-tight">
            {{ $article->title }}
        </h1>

        <!-- Author, Editor & Date Meta Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-slate-100 text-xs text-slate-600 font-medium">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-red-50 border border-red-200 text-crimson-700 flex items-center justify-center font-black text-base shadow-xs">
                    <i class="ri-user-3-line"></i>
                </div>
                <div>
                    <span class="font-bold text-slate-900 block text-sm font-heading">Penulis: {{ $article->source }}</span>
                    <span class="text-[11px] text-slate-500">Tim Redaksi METROLOGI NEWS</span>
                </div>
            </div>

            <div class="text-right space-y-0.5">
                <span class="text-slate-900 block font-bold font-heading">
                    <i class="ri-calendar-event-line text-crimson-600"></i> {{ $article->published_at ? $article->published_at->translatedFormat('l, d F Y') : $article->created_at->translatedFormat('l, d F Y') }} | {{ $article->published_at ? $article->published_at->format('H:i') : $article->created_at->format('H:i') }} WIB
                </span>
                @if($article->district)
                    <span class="text-xs text-slate-500 block font-semibold"><i class="ri-map-pin-line text-crimson-600"></i> Lokasi: {{ $article->district }}, Kota Metro</span>
                @endif
            </div>
        </div>

        <!-- SOCIAL SHARE BUTTONS BAR -->
        <div class="flex items-center justify-between bg-slate-50 p-3 rounded-2xl border border-slate-200 text-xs">
            <span class="font-bold text-slate-700 font-heading flex items-center gap-1.5">
                <i class="ri-share-forward-fill text-crimson-600 text-base"></i> BAGIKAN BERITA:
            </span>
            <div class="flex items-center gap-2">
                <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' ' . url()->current()) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-all flex items-center gap-1">
                    <i class="ri-whatsapp-line"></i> WhatsApp
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-all flex items-center gap-1">
                    <i class="ri-facebook-fill"></i> Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold transition-all flex items-center gap-1">
                    <i class="ri-twitter-x-line"></i> X
                </a>
            </div>
        </div>

        <!-- Excerpt Highlight Callout Box -->
        @if($article->excerpt)
            <div class="p-5 rounded-2xl bg-red-50/60 border border-red-200 text-slate-900 text-sm sm:text-base leading-relaxed space-y-1.5 shadow-xs">
                <span class="font-black block uppercase tracking-widest text-[11px] text-crimson-700 font-heading"><i class="ri-information-fill"></i> RINGKASAN UTAMA BERITA:</span>
                <p class="font-medium text-slate-800">{{ $article->excerpt }}</p>
            </div>
        @endif

        <!-- Article Featured Image + Caption (FOTO 1: UTAMA / ATAS) -->
        @if($article->image_path)
            <div class="space-y-2">
                <div class="rounded-3xl overflow-hidden border border-slate-200 bg-slate-50 max-h-[500px] shadow-sm">
                    <img src="{{ str_starts_with($article->image_path, 'http') ? $article->image_path : asset('storage/' . $article->image_path) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                </div>
                <p class="text-[11px] text-slate-500 italic text-center font-medium">Foto Utama: Dokumentasi Postingan Media Sosial Kota Metro.</p>
            </div>
        @endif

        <!-- Source Video / Medsos Link Callout Button (If provided) -->
        @if($article->source_url)
            <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <i class="ri-links-line text-brand-600 text-xl"></i>
                    <div>
                        <span class="font-bold text-slate-900 block font-heading">Rujukan Postingan / Video Medsos Asli</span>
                        <span class="text-slate-500 font-mono text-[11px] truncate max-w-md block">{{ $article->source_url }}</span>
                    </div>
                </div>
                <a href="{{ $article->source_url }}" target="_blank" class="px-4 py-2 rounded-xl bg-white border border-blue-200 hover:bg-brand-600 hover:text-white text-brand-700 font-bold transition-all text-xs flex items-center justify-center gap-1 shadow-xs font-heading">
                    <i class="ri-external-link-line"></i> Buka Link Asli
                </a>
            </div>
        @endif

        <!-- VERDICT & TRUTH ANALYSIS CARD -->
        @if($article->verdict)
            @if($article->verdict === 'asli')
                <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-950 space-y-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider font-heading text-emerald-800">Hasil Verifikasi Keaslian Berita</h4>
                            <span class="text-base font-extrabold text-emerald-900 font-heading">BERITA ASLI (FAKTA) &bull; Skor Kepercayaan: {{ number_format($article->verdict_score) }}%</span>
                        </div>
                    </div>
                    @if($article->verdict_reasoning)
                        <p class="text-xs sm:text-sm text-emerald-800 leading-relaxed pl-13 font-medium">
                            <strong class="text-emerald-950 font-bold">Catatan Analis AI:</strong> {{ $article->verdict_reasoning }}
                        </p>
                    @endif
                </div>
            @elseif($article->verdict === 'hoaks')
                <div class="p-6 rounded-3xl bg-rose-50 border border-rose-200 text-rose-950 space-y-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center font-bold text-lg shadow-sm animate-pulse">
                            <i class="ri-close-circle-line"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider font-heading text-rose-800">Hasil Verifikasi Keaslian Berita</h4>
                            <span class="text-base font-extrabold text-rose-900 font-heading">BERITA HOAKS (DISINFORMASI) &bull; Skor Kebenaran: {{ number_format($article->verdict_score) }}%</span>
                        </div>
                    </div>
                    @if($article->verdict_reasoning)
                        <p class="text-xs sm:text-sm text-rose-800 leading-relaxed pl-13 font-medium">
                            <strong class="text-rose-950 font-bold">Catatan Analis AI:</strong> {{ $article->verdict_reasoning }}
                        </p>
                    @endif
                </div>
            @endif
        @endif

        <!-- Article Body Content with 3 Photos (Foto Pertengahan & Foto Akhiran) -->
        @php
            $paras = array_values(array_filter(explode("\n", $article->content), fn($p) => trim($p) !== ''));
            $totalParas = count($paras);
            $midIndex = max(1, (int) ceil($totalParas / 2));
        @endphp

        <div class="pt-6 border-t border-slate-100 text-slate-800 text-base sm:text-lg leading-relaxed space-y-6 font-normal">
            @if($totalParas > 0)
                @foreach($paras as $idx => $para)
                    <p class="leading-relaxed">{{ $para }}</p>

                    <!-- FOTO 2: PERTENGAHAN ARTIKEL -->
                    @if($article->middle_image_path && ($idx + 1) === $midIndex)
                        <div class="my-8 space-y-2">
                            <div class="rounded-3xl overflow-hidden border border-slate-200 bg-slate-50 max-h-[450px] shadow-sm">
                                <img src="{{ str_starts_with($article->middle_image_path, 'http') ? $article->middle_image_path : asset('storage/' . $article->middle_image_path) }}" alt="{{ $article->title }} - Pertengahan" class="w-full h-full object-cover">
                            </div>
                            <p class="text-[11px] text-slate-500 italic text-center font-medium">Foto Pertengahan: Dokumentasi Lapangan Redaksi METROLOGI NEWS Kota Metro.</p>
                        </div>
                    @endif
                @endforeach
            @else
                <p class="whitespace-pre-line leading-relaxed">{{ $article->content }}</p>
                
                @if($article->middle_image_path)
                    <div class="my-8 space-y-2">
                        <div class="rounded-3xl overflow-hidden border border-slate-200 bg-slate-50 max-h-[450px] shadow-sm">
                            <img src="{{ str_starts_with($article->middle_image_path, 'http') ? $article->middle_image_path : asset('storage/' . $article->middle_image_path) }}" alt="{{ $article->title }} - Pertengahan" class="w-full h-full object-cover">
                        </div>
                        <p class="text-[11px] text-slate-500 italic text-center font-medium">Foto Pertengahan: Dokumentasi Lapangan Redaksi METROLOGI NEWS Kota Metro.</p>
                    </div>
                @endif
            @endif

            <!-- FOTO 3: AKHIRAN ARTIKEL -->
            @if($article->end_image_path)
                <div class="mt-8 space-y-2">
                    <div class="rounded-3xl overflow-hidden border border-slate-200 bg-slate-50 max-h-[450px] shadow-sm">
                        <img src="{{ str_starts_with($article->end_image_path, 'http') ? $article->end_image_path : asset('storage/' . $article->end_image_path) }}" alt="{{ $article->title }} - Akhiran" class="w-full h-full object-cover">
                    </div>
                    <p class="text-[11px] text-slate-500 italic text-center font-medium">Foto Akhiran: Dokumentasi Penutup Liputan Redaksi METROLOGI NEWS Kota Metro.</p>
                </div>
            @endif
        </div>

    </div>
    </div> <!-- END OF LEFT COLUMN -->

    <!-- RIGHT COLUMN: SENTIMENT & COMMENTS SECTION (5 COLS) -->
    <div class="lg:col-span-5 space-y-6">
        <!-- PUBLIC SENTIMENT & COMMENTS SECTION -->
        <div id="comments-section" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-8">
        
        <!-- Section Header -->
        <div class="flex flex-col gap-4 border-b border-slate-200 pb-5">
            <div>
                <h3 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2 font-heading leading-tight">
                    <i class="ri-chat-smile-2-line text-crimson-600"></i> Tanggapan &amp; Komentar Netizen
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-1">Komentar publik &amp; reaksi netizen (Instagram, X, Facebook, YouTube) yang telah dikurasi via Ingestion &amp; Filtering Algorithm.</p>
            </div>

            <!-- Sentiment Filter Tabs -->
            <div class="flex flex-wrap items-center gap-1 bg-slate-100 p-1.5 rounded-2xl border border-slate-200 text-xs font-bold font-heading self-start">
                <a href="{{ route('articles.show', $article->slug) }}#comments-section" class="px-3 py-1.5 rounded-xl transition-all {{ !request('sentiment') ? 'bg-crimson-700 text-white shadow-xs font-extrabold' : 'text-slate-600 hover:text-crimson-700' }}">
                    Semua ({{ $article->positive_count + $article->negative_count + $article->neutral_count }})
                </a>
                <a href="{{ route('articles.show', [$article->slug, 'sentiment' => 'positif']) }}#comments-section" class="px-3 py-1.5 rounded-xl transition-all {{ request('sentiment') == 'positif' ? 'bg-emerald-600 text-white shadow-xs' : 'text-emerald-700 hover:bg-emerald-50' }}">
                    <i class="ri-thumb-up-line"></i> Positif ({{ $article->positive_count }})
                </a>
                <a href="{{ route('articles.show', [$article->slug, 'sentiment' => 'negatif']) }}#comments-section" class="px-3 py-1.5 rounded-xl transition-all {{ request('sentiment') == 'negatif' ? 'bg-rose-600 text-white shadow-xs' : 'text-rose-700 hover:bg-rose-50' }}">
                    <i class="ri-thumb-down-line"></i> Negatif ({{ $article->negative_count }})
                </a>
            </div>
        </div>

        <!-- Sentiment Summary Metric Cards (Vertical Stack for Neatness in Column layout) -->
        <div class="flex flex-col gap-3">
            <!-- Positif -->
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-emerald-950 gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600/10 text-emerald-600 flex items-center justify-center shrink-0">
                        <i class="ri-thumb-up-fill text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-black uppercase tracking-wider block text-emerald-800 font-heading">Respon Positif</span>
                        <span class="text-[11px] text-emerald-600 font-medium truncate block">Dukungan &amp; Apresiasi Netizen</span>
                    </div>
                </div>
                <span class="text-base sm:text-lg font-black font-heading text-emerald-700 shrink-0 whitespace-nowrap">{{ number_format($article->positive_count) }} Komentar</span>
            </div>

            <!-- Negatif -->
            <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-between text-rose-950 gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-rose-600/10 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="ri-thumb-down-fill text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-black uppercase tracking-wider block text-rose-800 font-heading">Respon Negatif</span>
                        <span class="text-[11px] text-rose-600/70 font-medium truncate block">Kritikan &amp; Keluhan Netizen</span>
                    </div>
                </div>
                <span class="text-base sm:text-lg font-black font-heading text-rose-700 shrink-0 whitespace-nowrap">{{ number_format($article->negative_count) }} Komentar</span>
            </div>

            <!-- Netral -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between text-slate-900 gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center shrink-0">
                        <i class="ri-chat-neutral-fill text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-black uppercase tracking-wider block text-slate-600 font-heading">Komentar Netral</span>
                        <span class="text-[11px] text-slate-500 font-medium truncate block">Pernyataan Umum &amp; Pertanyaan</span>
                    </div>
                </div>
                <span class="text-base sm:text-lg font-black font-heading text-slate-800 shrink-0 whitespace-nowrap">{{ number_format($article->neutral_count) }} Komentar</span>
            </div>
        </div>

        <!-- CAROUSEL / SLIDE SHOW BUKTI SCREENSHOT KOMENTAR MEDSOS (INTERACTIVE SLIDER) -->
        @php 
            $screenshotsList = $article->comment_images ?? [];
            if (empty($screenshotsList) && $article->comment_image_path) {
                $screenshotsList = [$article->comment_image_path];
            }
        @endphp

        @if(!empty($screenshotsList))
            <div class="bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-800 text-white space-y-6 shadow-xl">
                
                <!-- Carousel Header Indicator -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-crimson-700 text-white flex items-center justify-center font-bold shadow-md">
                            <i class="ri-slideshow-3-line text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-base font-black text-white uppercase tracking-wider font-heading flex items-center gap-2">
                                📷 CAROUSEL SCREENSHOT KOMENTAR MEDSOS NETIZEN
                            </h4>
                            <p class="text-[11px] text-slate-400">Geser atau klik panah untuk melihat bukti tangkapan layar komentar medsos asli ({{ count($screenshotsList) }} Foto).</p>
                        </div>
                    </div>

                    <!-- Slide Counter Badge -->
                    <span id="slideCounterBadge" class="text-xs font-black text-amber-300 font-heading bg-slate-800 border border-slate-700 px-4 py-1.5 rounded-full self-start sm:self-auto shadow-xs">
                        Foto 1 dari {{ count($screenshotsList) }}
                    </span>
                </div>

                <!-- Main Active Slide Display Stage with Controls -->
                <div class="relative bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 shadow-2xl flex items-center justify-center min-h-[380px] sm:min-h-[460px] max-h-[550px] group">
                    
                    <!-- Main Active Image -->
                    <img id="mainCarouselImg" src="{{ asset('storage/' . $screenshotsList[0]) }}" alt="Screenshot Active Slide" class="w-full h-full object-contain cursor-pointer transition-all duration-300" onclick="openScreenshotModal(this.src)">

                    <!-- Overlay Zoom Hint -->
                    <button onclick="openScreenshotModal(document.getElementById('mainCarouselImg').src)" class="absolute bottom-4 right-4 z-10 px-3.5 py-1.5 rounded-xl bg-black/70 hover:bg-black text-white text-xs font-bold font-heading backdrop-blur-md border border-white/20 flex items-center gap-1.5 transition-all shadow-md">
                        <i class="ri-zoom-in-line text-amber-400"></i> Perbesar Layar Penuh
                    </button>

                    <!-- Previous Arrow Button -->
                    <button onclick="prevSlide()" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/60 hover:bg-crimson-700 text-white flex items-center justify-center font-black transition-all border border-white/20 shadow-lg group-hover:scale-105">
                        <i class="ri-arrow-left-s-line text-2xl"></i>
                    </button>

                    <!-- Next Arrow Button -->
                    <button onclick="nextSlide()" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/60 hover:bg-crimson-700 text-white flex items-center justify-center font-black transition-all border border-white/20 shadow-lg group-hover:scale-105">
                        <i class="ri-arrow-right-s-line text-2xl"></i>
                    </button>
                </div>

                <!-- Thumbnails Strip Carousel Ribbon Below Main Stage -->
                <div class="space-y-2">
                    <span class="text-[11px] font-bold text-slate-400 font-heading block">Pilih Slide Foto:</span>
                    <div class="flex items-center gap-3 overflow-x-auto custom-scrollbar pb-2">
                        @foreach($screenshotsList as $idx => $imgPath)
                            <div id="thumb-{{ $idx }}" onclick="goToSlide({{ $idx }})" class="thumb-card w-24 h-20 rounded-xl overflow-hidden border-2 cursor-pointer shrink-0 transition-all bg-slate-900 shadow-xs {{ $idx === 0 ? 'border-crimson-600 scale-105 opacity-100' : 'border-slate-800 opacity-60 hover:opacity-100 hover:border-slate-600' }}">
                                <img src="{{ asset('storage/' . $imgPath) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        @endif

        <!-- FORM KIRIM KOMENTAR PENGUNJUNG PUBLIK -->
        <div class="bg-slate-50 border border-slate-200 p-6 rounded-2xl space-y-4">
            <h4 class="text-base font-bold text-slate-900 flex items-center gap-2 font-heading">
                <i class="ri-edit-box-line text-crimson-600"></i> Tulis Tanggapan / Komentar Anda
            </h4>
            <p class="text-xs text-slate-500">Sistem AI akan secara otomatis menganalisis sentimen dari komentar yang Anda kirimkan.</p>

            <form action="{{ route('articles.comment', $article->slug) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama / Username Anda <span class="text-rose-500">*</span></label>
                        <input type="text" name="author_name" value="{{ old('author_name') }}" required placeholder="Contoh: @warga_metro / Budi" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-crimson-600 font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Isi Komentar / Tanggapan <span class="text-rose-500">*</span></label>
                    <textarea name="raw_comment" rows="3" required placeholder="Tuliskan pendapat atau masukan Anda mengenai isu berita ini..." class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-crimson-600 leading-relaxed font-medium">{{ old('raw_comment') }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-crimson-700 hover:bg-crimson-800 text-white font-extrabold text-xs shadow-md shadow-crimson-700/20 transition-all font-heading flex items-center gap-1.5">
                        <i class="ri-send-plane-fill"></i> Kirim Komentar
                    </button>
                </div>
            </form>
        </div>

        <!-- LIST KOMENTAR MEDIA SOSIAL (FEED STYLE) -->
        <div class="space-y-4 pt-2">
            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700 font-heading mb-4 flex items-center gap-2">
                <i class="ri-team-line text-crimson-600"></i> Tanggapan Netizen Media Sosial (Ter-Kurasi):
            </h4>

            @forelse($comments as $c)
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 hover:border-slate-300 transition-all shadow-2xs">
                    <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                        
                        <!-- User Profile Avatar & Username -->
                        <div class="flex items-center gap-3">
                            @if($c->author_avatar)
                                <img src="{{ $c->author_avatar }}" alt="{{ $c->author_name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-xs">
                            @else
                                <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-800 flex items-center justify-center font-black text-xs font-heading shadow-xs">
                                    {{ strtoupper(substr($c->author_name, 1, 1) ?: substr($c->author_name, 0, 1)) }}
                                </div>
                            @endif

                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-heading font-bold text-slate-900 text-sm">{{ $c->author_name }}</span>
                                    
                                    <!-- Platform Badge -->
                                    @if($c->platform === 'Instagram')
                                        <span class="px-2 py-0.5 rounded-md font-extrabold text-[10px] bg-gradient-to-r from-purple-600 via-pink-600 to-amber-500 text-white flex items-center gap-1 shadow-xs">
                                            <i class="ri-instagram-line"></i> Instagram
                                        </span>
                                    @elseif($c->platform === 'X')
                                        <span class="px-2 py-0.5 rounded-md font-extrabold text-[10px] bg-slate-950 text-white flex items-center gap-1 shadow-xs">
                                            <i class="ri-twitter-x-line"></i> X (Twitter)
                                        </span>
                                    @elseif($c->platform === 'Facebook')
                                        <span class="px-2 py-0.5 rounded-md font-extrabold text-[10px] bg-blue-600 text-white flex items-center gap-1 shadow-xs">
                                            <i class="ri-facebook-fill"></i> Facebook
                                        </span>
                                    @elseif($c->platform === 'YouTube')
                                        <span class="px-2 py-0.5 rounded-md font-extrabold text-[10px] bg-red-600 text-white flex items-center gap-1 shadow-xs">
                                            <i class="ri-youtube-fill"></i> YouTube
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md font-extrabold text-[10px] bg-crimson-700 text-white flex items-center gap-1 shadow-xs">
                                            <i class="ri-global-line"></i> Web Portal
                                        </span>
                                    @endif
                                </div>

                                <span class="text-[11px] text-slate-400 font-normal">
                                    <i class="ri-time-line text-slate-400"></i> {{ $c->posted_at ? $c->posted_at->diffForHumans() : ($c->created_at ? $c->created_at->diffForHumans() : 'Baru saja') }}
                                </span>
                            </div>
                        </div>

                        <!-- Sentiment AI Badge -->
                        @if($c->sentiment === 'positif')
                            <span class="px-3 py-1 rounded-full font-black text-[10px] uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1 font-heading">
                                <i class="ri-thumb-up-fill text-emerald-600"></i> Sentimen Positif
                            </span>
                        @elseif($c->sentiment === 'negatif')
                            <span class="px-3 py-1 rounded-full font-black text-[10px] uppercase bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1 font-heading">
                                <i class="ri-thumb-down-fill text-rose-600"></i> Sentimen Negatif
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full font-black text-[10px] uppercase bg-slate-100 text-slate-700 border border-slate-200 flex items-center gap-1 font-heading">
                                <i class="ri-chat-neutral-fill text-slate-500"></i> Sentimen Netral
                            </span>
                        @endif
                    </div>

                    <!-- Comment Text -->
                    <p class="text-xs sm:text-sm text-slate-800 leading-relaxed font-normal pl-12">
                        "{{ $c->raw_comment }}"
                    </p>
                </div>
            @empty
                <div class="p-10 text-center text-slate-500 text-xs bg-slate-50 rounded-3xl border border-slate-200 space-y-2">
                    <div class="w-12 h-12 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center mx-auto text-2xl">
                        <i class="ri-chat-smile-2-line"></i>
                    </div>
                    <p class="font-bold text-slate-700 text-sm font-heading">Belum ada tanggapan media sosial untuk berita ini.</p>
                    <p class="text-slate-400 text-xs">Jadilah yang pertama memberikan tanggapan melalui form di atas.</p>
                </div>
            @endforelse

            @if($comments->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ $comments->links() }}
                </div>
            @endif
        </div>

    </div>
    </div> <!-- END OF RIGHT COLUMN -->

</div> <!-- END OF 2-COLUMN GRID WRAPPER -->

    <!-- Related Articles Section -->
    @if($relatedArticles->isNotEmpty())
        <div class="pt-8 space-y-6">
            <h3 class="text-2xl font-black text-slate-900 flex items-center gap-2 font-heading tracking-tight border-b-2 border-slate-900 pb-3">
                <i class="ri-newspaper-line text-crimson-600"></i> BACA JUGA BERITA TERKAIT
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedArticles as $rel)
                    <a href="{{ route('articles.show', $rel->slug) }}" class="bg-white rounded-3xl p-5 border border-slate-200 hover:border-crimson-600 transition-all block space-y-3 shadow-xs hover:shadow-md group">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black text-white uppercase font-heading bg-crimson-700">
                            {{ $rel->category->name }}
                        </span>
                        <h4 class="font-bold text-slate-900 text-base line-clamp-2 group-hover:text-crimson-700 transition-colors leading-snug font-heading">{{ $rel->title }}</h4>
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">{{ $rel->excerpt }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Screenshot Modal Viewer -->
<div id="screenshotModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden items-center justify-center p-4" onclick="closeScreenshotModal()">
    <div class="relative max-w-4xl w-full max-h-[90vh] bg-slate-950 rounded-3xl p-2 overflow-hidden shadow-2xl flex flex-col items-center justify-center" onclick="event.stopPropagation()">
        <button onclick="closeScreenshotModal()" class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center font-bold transition-all">
            <i class="ri-close-line text-2xl"></i>
        </button>
        <img id="modalImage" src="" alt="Screenshot Full View" class="max-h-[85vh] w-auto max-w-full object-contain rounded-2xl">
    </div>
</div>

@push('scripts')
<script>
    @if(!empty($screenshotsList))
        const slides = {!! json_encode(array_map(fn($path) => asset('storage/' . $path), $screenshotsList)) !!};
        let currentSlideIdx = 0;

        function updateSlideUI() {
            const img = document.getElementById('mainCarouselImg');
            img.src = slides[currentSlideIdx];

            const badge = document.getElementById('slideCounterBadge');
            badge.innerText = `Foto ${currentSlideIdx + 1} dari ${slides.length}`;

            // Highlight thumbnail
            document.querySelectorAll('.thumb-card').forEach((thumb, idx) => {
                if (idx === currentSlideIdx) {
                    thumb.className = 'thumb-card w-24 h-20 rounded-xl overflow-hidden border-2 cursor-pointer shrink-0 transition-all bg-slate-900 shadow-xs border-crimson-600 scale-105 opacity-100';
                } else {
                    thumb.className = 'thumb-card w-24 h-20 rounded-xl overflow-hidden border-2 cursor-pointer shrink-0 transition-all bg-slate-900 shadow-xs border-slate-800 opacity-60 hover:opacity-100 hover:border-slate-600';
                }
            });
        }

        function nextSlide() {
            currentSlideIdx = (currentSlideIdx + 1) % slides.length;
            updateSlideUI();
        }

        function prevSlide() {
            currentSlideIdx = (currentSlideIdx - 1 + slides.length) % slides.length;
            updateSlideUI();
        }

        function goToSlide(idx) {
            currentSlideIdx = idx;
            updateSlideUI();
        }
    @endif

    function openScreenshotModal(src) {
        document.getElementById('modalImage').src = src;
        const modal = document.getElementById('screenshotModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeScreenshotModal() {
        const modal = document.getElementById('screenshotModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endpush
@endsection
