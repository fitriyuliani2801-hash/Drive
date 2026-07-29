<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Redaksi - METROLOGI NEWS')</title>

    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Remixicon Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    
    <!-- Chart.js -->
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
    </style>
    @stack('styles')
</head>
<body class="min-h-full bg-slate-100 text-slate-800 flex flex-col antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-slate-950 text-white flex flex-col justify-between p-4 z-30 shadow-xl border-r border-slate-800">
            <div class="space-y-6">
                <!-- Admin Logo -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-2 py-2 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-crimson-700 to-brand-600 p-0.5 shadow-md">
                        <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
                            <i class="ri-shield-user-fill text-xl text-crimson-600"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-base font-black tracking-tight text-white flex items-center gap-1 font-heading">
                            METROLOGI<span class="text-crimson-600">NEWS</span>
                        </span>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Dashboard Redaksi</p>
                    </div>
                </a>

                <!-- Nav Items -->
                <nav class="space-y-1 font-heading">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-crimson-700 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i class="ri-dashboard-3-line text-base"></i>
                        <span>Dashboard Redaksi</span>
                    </a>

                    <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.articles.index') || request()->routeIs('admin.articles.edit') ? 'bg-crimson-700 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i class="ri-newspaper-line text-base"></i>
                        <span>Kelola Berita &amp; Postingan</span>
                    </a>

                    <a href="{{ route('admin.articles.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.articles.create') ? 'bg-crimson-700 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i class="ri-add-circle-line text-base"></i>
                        <span>Tulis Berita Baru</span>
                    </a>

                    <div class="pt-3 pb-1">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 px-4">Analisis Topic LDA &amp; NLP</span>
                    </div>

                    <a href="{{ route('admin.lda.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.lda.*') ? 'bg-teal-700 text-white shadow-md' : 'text-teal-400 hover:text-white hover:bg-slate-900' }}">
                        <i class="ri-shield-check-line text-base"></i>
                        <span>Kelola Publikasi Topik LDA</span>
                    </a>

                    <a href="{{ route('admin.analysis.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.analysis.index') ? 'bg-slate-800 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i class="ri-pulse-line text-base text-teal-400"></i>
                        <span>Dashboard LDA Engine</span>
                    </a>

                    <a href="{{ route('admin.analysis.preprocessing') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.analysis.preprocessing') ? 'bg-slate-800 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i class="ri-sound-module-line text-base text-teal-400"></i>
                        <span>Pre-processing NLP (Step 2)</span>
                    </a>

                    <a href="{{ route('admin.analysis.vectorization') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.analysis.vectorization') ? 'bg-slate-800 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i class="ri-matrix-line text-base text-indigo-400"></i>
                        <span>Matriks DTM &amp; TF-IDF (Step 3)</span>
                    </a>

                    <a href="{{ route('admin.analysis.comments') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('admin.analysis.comments') ? 'bg-slate-800 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                        <i class="ri-database-2-line text-base text-amber-400"></i>
                        <span>Data Scraper Komentar (Step 1)</span>
                    </a>
                </nav>
            </div>

            <div class="space-y-3 pt-4 border-t border-slate-800 font-heading">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-brand-600 text-slate-300 hover:text-white text-xs font-bold transition-colors border border-slate-800">
                    <i class="ri-earth-line text-crimson-600"></i> Lihat Portal Publik
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-950/60 hover:bg-rose-900 text-rose-300 text-xs font-bold transition-colors border border-rose-900/50">
                        <i class="ri-logout-box-r-line"></i> Keluar Redaksi
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Admin Content Container -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-100">
            <!-- Admin Top Header -->
            <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shadow-xs">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider font-heading flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-crimson-600 animate-ping"></span>
                    RED AKSI METROLOGI NEWS &bull; PANEL EDITORIAL &amp; ANALISIS SENTIMEN
                </h2>

                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-600 font-bold font-heading">Redaktur Utama</span>
                    <div class="w-8 h-8 rounded-full bg-crimson-700 text-white flex items-center justify-center font-black text-xs font-heading">
                        {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Page Content Scroll Area -->
            <main class="flex-1 overflow-y-auto p-6 sm:p-8">
                @yield('content')
            </main>
        </div>

    </div>

    @stack('scripts')
</body>
</html>
