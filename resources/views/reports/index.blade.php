@extends('layouts.app')

@section('title', 'Daftar Pengaduan Warga - Metrologi Kota Metro')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Linimasa Pengaduan Perkotaan</h1>
            <p class="text-slate-600 text-sm mt-1">Daftar pengaduan masyarakat Kota Metro beserta transparansi status penanganannya.</p>
        </div>

        <a href="{{ route('reports.create') }}" class="px-5 py-3 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-bold text-sm shadow-md shadow-teal-600/20 flex items-center justify-center gap-2">
            <i class="ri-add-circle-fill text-lg"></i> Buat Pengaduan Baru (Tanpa Login)
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <form action="{{ route('reports.index') }}" method="GET" class="bg-white border border-slate-200/90 p-4 rounded-2xl mb-8 space-y-4 md:space-y-0 md:flex items-center gap-4 shadow-xs">
        <div class="flex-1 relative">
            <i class="ri-search-line absolute left-3.5 top-3.5 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode tiket, judul laporan, atau alamat..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors">
        </div>

        <div class="w-full md:w-48">
            <select name="category" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full md:w-48">
            <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak / Hoax</option>
            </select>
        </div>

        <div class="w-full md:w-48">
            <select name="district" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white">
                <option value="">Seluruh Kecamatan</option>
                @foreach($districts as $dist)
                    <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('reports.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold flex items-center justify-center gap-1.5 transition-colors">
            <i class="ri-refresh-line"></i> Reset
        </a>
    </form>

    <!-- Reports Grid -->
    @if($reports->isEmpty())
        <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center max-w-md mx-auto my-12 shadow-xs">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-3xl">
                <i class="ri-inbox-archive-line"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Tidak Ada Pengaduan Ditemukan</h3>
            <p class="text-xs text-slate-500 mb-6">Tidak ada laporan yang cocok dengan pencarian atau filter yang Anda pilih.</p>
            <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-xl bg-teal-600 text-white font-bold text-xs">Lihat Semua Pengaduan</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($reports as $report)
                <div class="bg-white border border-slate-200/90 rounded-3xl overflow-hidden hover:border-slate-300 transition-all flex flex-col justify-between shadow-xs hover:shadow-md">
                    <div>
                        <!-- Header Bar -->
                        <div class="p-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-slate-600">{{ $report->ticket_code }}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $report->status_badge_class }}">
                                {{ $report->status_label }}
                            </span>
                        </div>

                        <!-- Image Preview if available -->
                        @if($report->image_path)
                            <div class="h-48 w-full bg-slate-100 overflow-hidden relative">
                                <img src="{{ asset('storage/' . $report->image_path) }}" alt="{{ $report->title }}" class="w-full h-full object-cover">
                                <div class="absolute bottom-2 left-2 px-2.5 py-1 rounded-lg bg-white/90 backdrop-blur-md text-[10px] text-slate-800 font-bold flex items-center gap-1 shadow-xs border border-slate-200">
                                    <i class="{{ $report->category->icon ?? 'ri-map-pin-line' }}" style="color: {{ $report->category->color_code }}"></i>
                                    {{ $report->category->name }}
                                </div>
                            </div>
                        @endif

                        <!-- Body -->
                        <div class="p-5 space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold text-slate-700 bg-slate-100">
                                    <i class="ri-map-pin-line text-teal-600 mr-1"></i>{{ $report->district }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $report->urgency_badge_class }}">
                                    Urgensi: {{ $report->urgency_label }}
                                </span>
                            </div>

                            <a href="{{ route('reports.show', $report->id) }}" class="block">
                                <h3 class="text-base font-bold text-slate-900 hover:text-teal-700 transition-colors line-clamp-2">
                                    {{ $report->title }}
                                </h3>
                            </a>

                            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $report->description }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-[10px]">
                                {{ strtoupper(substr($report->reporter_name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-slate-700">{{ $report->reporter_name }}</span>
                        </div>
                        <a href="{{ route('reports.show', $report->id) }}" class="text-teal-700 font-bold hover:underline flex items-center gap-1">
                            Detail <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
