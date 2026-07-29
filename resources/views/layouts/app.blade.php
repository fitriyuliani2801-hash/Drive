<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'METROLOGI NEWS - Portal Berita & Isu Medsos Kota Metro')</title>
    <meta name="description" content="Portal berita liputan khusus, isu terkini media sosial, dan analisis sentimen netizen Kota Metro, Lampung.">
    
    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Remixicon Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#172554',
                        },
                        crimson: {
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'Outfit', sans-serif; }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #0f172a;
            border-radius: 9999px;
        }

        .menu-scrollbar::-webkit-scrollbar {
            height: 8px;
            width: 0px;
        }
        .menu-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .menu-scrollbar::-webkit-scrollbar-thumb {
            background: #0f172a;
            border-radius: 9999px;
        }

        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: 200%;
            animation: marquee 28s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-slate-100/80 text-slate-800 antialiased selection:bg-brand-600 selection:text-white custom-scrollbar">

    <!-- TOP NATIONAL UTILITY HEADER BAR (GAYA DETIK / KOMPAS) -->
    <div class="bg-slate-900 text-slate-200 text-xs font-semibold py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-4 text-[11px]">
                <span class="flex items-center gap-1.5 text-blue-300 font-bold">
                    <i class="ri-calendar-check-fill text-blue-400"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} | {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                </span>
                <span class="hidden md:inline-block text-slate-700">&bull;</span>
                <span class="hidden md:flex items-center gap-1 text-slate-300">
                    <i class="ri-map-pin-2-fill text-rose-500"></i> Kota Metro, Lampung
                </span>
                <span class="hidden md:inline-block text-slate-700">&bull;</span>
                <span class="hidden md:flex items-center gap-1 text-amber-400">
                    <i class="ri-sun-cloudy-line"></i> Cerah 31°C
                </span>
            </div>

            <div class="flex items-center gap-4 text-[11px]">
                <span class="text-slate-400">Portal Media Berita Nasional Kota Metro</span>
                <div class="flex items-center gap-2.5 text-slate-300">
                    <a href="#" class="hover:text-blue-400 transition-colors"><i class="ri-facebook-box-fill text-base"></i></a>
                    <a href="#" class="hover:text-blue-400 transition-colors"><i class="ri-instagram-fill text-base"></i></a>
                    <a href="#" class="hover:text-blue-400 transition-colors"><i class="ri-tiktok-fill text-base"></i></a>
                    <a href="#" class="hover:text-blue-400 transition-colors"><i class="ri-youtube-fill text-base"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- BREAKING NEWS MARQUEE TICKER (RED & WHITE TV STYLE) -->
    <div class="bg-gradient-to-r from-crimson-700 via-rose-700 to-crimson-800 text-white text-xs font-semibold shadow-xs border-b border-red-800 overflow-hidden">
        <div class="max-w-7xl mx-auto flex items-stretch">
            <div class="relative shrink-0 flex items-center pl-4 sm:pl-6 lg:pl-8 pr-6 py-2.5 bg-[#b91c1c] z-20">
                <!-- Efek miring pada ujung blok merah tanpa garis putih -->
                <div class="absolute -right-2 top-0 h-full w-6 bg-[#b91c1c] skew-x-12"></div>
                
                <span class="relative z-10 flex items-center gap-2 uppercase tracking-widest font-black text-[11px] text-white font-heading mr-2">
                    <span class="relative flex h-2.5 w-2.5">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-400"></span>
                    </span>
                    BREAKING NEWS
                </span>
            </div>
            <div class="overflow-hidden relative w-full flex items-center py-2.5 pl-6 pr-4 sm:pr-6 lg:pr-8">
                <div class="animate-marquee whitespace-nowrap flex gap-8 items-center text-white font-medium text-xs">
                    <span>⚡ Pemkot Metro Rilis Inovasi Layanan Publik &amp; Penguatan UMKM Sentra Kuliner 2026</span>
                    <span>⚡ Pembukaan Porkot Metro Dibuka Meriah di Stadion Tejosari</span>
                    <span>⚡ Bagian Hukum Setda Kota Metro Gelar Bantuan Hukum Gratis Warga Kurang Mampu</span>
                    <span>⚡ Pemkot Metro Rilis Inovasi Layanan Publik &amp; Penguatan UMKM Sentra Kuliner 2026</span>
                    <span>⚡ Pembukaan Porkot Metro Dibuka Meriah di Stadion Tejosari</span>
                    <span>⚡ Bagian Hukum Setda Kota Metro Gelar Bantuan Hukum Gratis Warga Kurang Mampu</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN BRANDING & HEADER (DETIK / KOMPAS STYLE) -->
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                
                <!-- Logo & Subtitle -->
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                    <div class="w-14 h-14 shrink-0 group-hover:scale-105 transition-transform duration-300">
                        <img src="{{ asset('images/Logo-Kota-Metro.png') }}" alt="Logo Kota Metro" class="w-full h-full object-contain scale-[1.6]">
                    </div>
                    <div>
                        <span class="text-3xl sm:text-4xl font-black tracking-tight text-slate-900 flex items-center gap-2.5 font-heading">
                            METROLOGI<span class="text-transparent bg-clip-text bg-gradient-to-r from-crimson-700 to-brand-600">NEWS</span>
                        </span>
                        <p class="text-xs text-slate-500 font-semibold tracking-wide">Portal Media Berita Utama &amp; Sentimen Netizen Kota Metro</p>
                    </div>
                </a>

                <!-- Header Search Bar -->
                <form action="{{ route('articles.index') }}" method="GET" class="w-full md:w-auto flex-1 max-w-lg">
                    <div class="relative flex items-center shadow-xs rounded-md border border-slate-300 bg-slate-50 focus-within:bg-white focus-within:border-brand-600 focus-within:shadow-md transition-all p-1">
                        <button type="submit" class="pl-3 pr-2 text-slate-400 hover:text-crimson-700 transition-colors focus:outline-none" title="Cari Berita">
                            <i class="ri-search-2-line text-lg"></i>
                        </button>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berita terkini, kata kunci, isu perkotaan..." class="w-full pr-3 py-2 bg-transparent text-xs text-slate-900 focus:outline-none font-medium">
                    </div>
                </form>

            </div>
        </div>
    </header>

    <!-- TRENDING HASHTAG BAR (DETIK / TRIBUN STYLE) -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800 overflow-x-auto overflow-y-hidden menu-scrollbar">
        <div class="max-w-7xl mx-auto flex items-center gap-4 shrink-0 font-medium">
            <span class="text-amber-400 font-bold uppercase text-[11px] tracking-wider flex items-center gap-1 font-heading shrink-0">
                <i class="ri-fire-fill"></i> HOT TOPICS:
            </span>
            <div class="flex items-center gap-3 overflow-x-auto overflow-y-hidden font-heading shrink-0 menu-scrollbar">
                <a href="{{ route('articles.index', ['search' => 'UMKM']) }}" class="hover:text-white text-slate-300 transition-all text-[11px]">#KulinerMetro</a>
                <a href="{{ route('articles.index', ['category' => 'olahraga']) }}" class="hover:text-white text-slate-300 transition-all text-[11px]">#PorkotMetro2026</a>
                <a href="{{ route('articles.index', ['category' => 'hukum']) }}" class="hover:text-white text-slate-300 transition-all text-[11px]">#BantuanHukum</a>
                <a href="{{ route('articles.index', ['district' => 'Metro Pusat']) }}" class="hover:text-white text-slate-300 transition-all text-[11px]">#MetroPusat</a>
                <a href="{{ route('articles.index', ['category' => 'politik']) }}" class="hover:text-white text-slate-300 transition-all text-[11px]">#APBDMetro2026</a>
            </div>
        </div>
    </div>

    <!-- STICKY NATIONAL EDITORIAL NAV BAR -->
    <nav class="sticky top-0 z-40 glass-nav shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 overflow-x-auto custom-scrollbar">
                
                <div class="flex items-center gap-2 text-xs font-bold font-heading py-2 shrink-0">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 {{ request()->routeIs('home') ? 'bg-crimson-700 text-white shadow-md font-extrabold' : 'text-slate-700 hover:bg-red-50 hover:text-crimson-700' }}">
                        <i class="ri-fire-fill text-amber-300"></i> Beranda &amp; Headline
                    </a>
                    <a href="{{ route('articles.index') }}" class="px-4 py-2 rounded-xl transition-all flex items-center gap-1.5 {{ request()->routeIs('articles.index') || request()->routeIs('articles.show') ? 'bg-crimson-700 text-white shadow-md font-extrabold' : 'text-slate-700 hover:bg-red-50 hover:text-crimson-700' }}">
                        <i class="ri-newspaper-line text-crimson-600"></i> Metro Terkini
                    </a>
                    <span class="text-slate-300 font-normal">|</span>
                    <a href="{{ route('articles.index', ['category' => 'ekonomi']) }}" class="px-3.5 py-2 rounded-xl text-slate-700 hover:text-crimson-700 hover:bg-red-50 transition-all flex items-center gap-1.5">
                        <i class="ri-price-tag-3-line text-brand-600"></i> Ekonomi &amp; UMKM
                    </a>
                    <a href="{{ route('articles.index', ['category' => 'hukum']) }}" class="px-3.5 py-2 rounded-xl text-slate-700 hover:text-crimson-700 hover:bg-red-50 transition-all flex items-center gap-1.5">
                        <i class="ri-scales-3-line text-brand-600"></i> Hukum &amp; Perda
                    </a>
                    <a href="{{ route('articles.index', ['category' => 'politik']) }}" class="px-3.5 py-2 rounded-xl text-slate-700 hover:text-crimson-700 hover:bg-red-50 transition-all flex items-center gap-1.5">
                        <i class="ri-government-line text-brand-600"></i> Politik &amp; APBD
                    </a>
                    <a href="{{ route('articles.index', ['category' => 'olahraga']) }}" class="px-3.5 py-2 rounded-xl text-slate-700 hover:text-crimson-700 hover:bg-red-50 transition-all flex items-center gap-1.5">
                        <i class="ri-trophy-line text-brand-600"></i> Olahraga &amp; Porkot
                    </a>
                </div>
            </div>
        </div>
    </nav>



    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="bg-emerald-600 text-white px-4 py-3 text-center text-sm font-semibold flex items-center justify-center gap-2 shadow-md">
            <i class="ri-checkbox-circle-fill text-emerald-200 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-600 text-white px-4 py-3 text-center text-sm font-semibold flex items-center justify-center gap-2 shadow-md">
            <i class="ri-error-warning-fill text-rose-200 text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Content Body -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- FOOTER NATIONAL MEDIA PORTAL -->
    <footer class="bg-slate-950 text-white border-t border-slate-800 pt-16 pb-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 shrink-0">
                            <img src="{{ asset('images/Logo-Kota-Metro.png') }}" alt="Logo Kota Metro" class="w-full h-full object-contain scale-[1.6]">
                        </div>
                        <span class="text-3xl font-black tracking-tight text-white font-heading">METROLOGI<span class="text-crimson-600">NEWS</span></span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-md">
                        Portal Media Berita Utama &amp; Sentimen Netizen Kota Metro, Lampung. Menyajikan berita liputan khusus, isu terkini media sosial, serta pengolahan sentimen tanggapan netizen secara terbuka dan terpercaya.
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-300 font-heading mb-4 border-l-3 border-crimson-600 pl-3">Navigasi Redaksi</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors flex items-center gap-2"><i class="ri-arrow-right-s-line text-crimson-600"></i> Beranda &amp; Headline Utama</a></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-white transition-colors flex items-center gap-2"><i class="ri-arrow-right-s-line text-crimson-600"></i> Metro Terkini</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-300 font-heading mb-4 border-l-3 border-crimson-600 pl-3">Kategori Berita</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('articles.index', ['category' => 'ekonomi']) }}" class="hover:text-white transition-colors flex items-center gap-2"><i class="ri-price-tag-3-line text-crimson-600"></i> Ekonomi &amp; UMKM</a></li>
                        <li><a href="{{ route('articles.index', ['category' => 'hukum']) }}" class="hover:text-white transition-colors flex items-center gap-2"><i class="ri-scales-3-line text-crimson-600"></i> Hukum &amp; Perda</a></li>
                        <li><a href="{{ route('articles.index', ['category' => 'politik']) }}" class="hover:text-white transition-colors flex items-center gap-2"><i class="ri-government-line text-crimson-600"></i> Politik &amp; APBD</a></li>
                        <li><a href="{{ route('articles.index', ['category' => 'olahraga']) }}" class="hover:text-white transition-colors flex items-center gap-2"><i class="ri-trophy-line text-crimson-600"></i> Olahraga &amp; Porkot</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-900 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} METROLOGI NEWS Kota Metro. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-md bg-slate-900 text-slate-300 border border-slate-800 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        Portal Berita Terverifikasi Dewan Pers &amp; Sentimen AI
                    </span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
