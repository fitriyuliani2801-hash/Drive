@extends('layouts.app')

@section('title', $report->title . ' - Metrologi Kota Metro')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Navigation Back -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <i class="ri-arrow-left-line"></i> Kembali ke Linimasa Pengaduan
        </a>

        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.reports.show', $report->id) }}" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs flex items-center gap-1.5 transition-colors shadow-md shadow-teal-600/20">
                    <i class="ri-edit-box-line"></i> Kelola dalam Admin Panel
                </a>
            @endif
        @endauth
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Main Info & Timeline -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- Header Card -->
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                    <span class="text-xs font-mono font-bold px-3 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                        Tiket: {{ $report->ticket_code }}
                    </span>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $report->status_badge_class }}">
                            {{ $report->status_label }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $report->urgency_badge_class }}">
                            Urgensi: {{ $report->urgency_label }}
                        </span>
                    </div>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                    {{ $report->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-2">
                    <span class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                        <i class="{{ $report->category->icon ?? 'ri-price-tag-3-line' }}" style="color: {{ $report->category->color_code }}"></i>
                        <span class="font-bold text-slate-800">{{ $report->category->name }}</span>
                    </span>
                    <span class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                        <i class="ri-map-pin-2-line text-teal-600"></i>
                        <span class="text-slate-700">{{ $report->location_address }} ({{ $report->district }})</span>
                    </span>
                    <span class="flex items-center gap-1.5 text-slate-500">
                        <i class="ri-time-line"></i> {{ $report->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </span>
                </div>

                <!-- Description -->
                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Deskripsi Pengaduan</h3>
                    <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                        {{ $report->description }}
                    </p>
                </div>
            </div>

            <!-- Photo Gallery -->
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="ri-image-2-line text-teal-600"></i> Dokumentasi & Foto Bukti
                </h3>

                <div class="grid grid-cols-1 {{ $report->resolution_image_path ? 'md:grid-cols-2' : '' }} gap-6">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Foto Bukti Pengaduan</span>
                        @if($report->image_path)
                            <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-50">
                                <img src="{{ asset('storage/' . $report->image_path) }}" alt="Foto Bukti Pengaduan" class="w-full h-64 object-cover">
                            </div>
                        @else
                            <div class="h-64 rounded-2xl border border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-slate-400">
                                <i class="ri-image-line text-3xl mb-1"></i>
                                <span class="text-xs">Tidak ada foto dilampirkan</span>
                            </div>
                        @endif
                    </div>

                    @if($report->resolution_image_path)
                        <div class="space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block"><i class="ri-checkbox-circle-fill"></i> Foto Hasil Perbaikan (Admin)</span>
                            <div class="rounded-2xl overflow-hidden border border-emerald-300 bg-slate-50 shadow-sm">
                                <img src="{{ asset('storage/' . $report->resolution_image_path) }}" alt="Foto Penyelesaian Admin" class="w-full h-64 object-cover">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline Transparansi Penanganan -->
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="ri-history-line text-teal-600"></i> Riwayat Transparansi Penanganan
                </h3>

                <div class="relative pl-6 border-l-2 border-slate-200 space-y-6">
                    @forelse($report->logs as $log)
                        <div class="relative group">
                            <span class="absolute -left-[31px] top-1.5 w-4 h-4 rounded-full bg-teal-600 border-4 border-white shadow-xs"></span>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-teal-800 uppercase tracking-wider">Status: {{ ucfirst(str_replace('_', ' ', $log->status_to)) }}</span>
                                    <span class="text-slate-500">{{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                </div>
                                @if($log->note)
                                    <p class="text-xs text-slate-700 leading-relaxed mt-1">{{ $log->note }}</p>
                                @endif
                                <div class="text-[10px] text-slate-500 pt-1">
                                    Oleh: <span class="text-slate-700 font-semibold">{{ $log->user->name ?? 'Sistem Metrologi' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500">Belum ada riwayat perubahan status.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar Map & Reporter Info -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Map Card -->
            <div class="bg-white border border-slate-200/90 rounded-3xl p-5 shadow-xs space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <i class="ri-map-pin-2-fill text-teal-600"></i> Lokasi Geospasial
                </h3>
                <div id="detail-map" class="h-64 w-full rounded-2xl border border-slate-200 z-10"></div>
                <div class="text-xs text-slate-600 space-y-1">
                    <p class="font-semibold text-slate-800">{{ $report->location_address }}</p>
                    <p class="font-mono text-[11px] text-teal-700 font-bold">Lat: {{ $report->latitude }}, Lng: {{ $report->longitude }}</p>
                </div>
            </div>

            <!-- Reporter Profile Card -->
            <div class="bg-white border border-slate-200/90 rounded-3xl p-5 shadow-xs space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700">Informasi Pelapor</h3>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center font-extrabold text-lg border border-teal-200">
                        {{ strtoupper(substr($report->reporter_name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ $report->reporter_name }}</h4>
                        <p class="text-xs text-slate-500">Masyarakat Kota Metro</p>
                    </div>
                </div>

                @if($report->admin_note)
                    <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 text-xs text-sky-900 space-y-1">
                        <span class="font-bold text-sky-700 block"><i class="ri-chat-check-line"></i> Catatan Tindak Lanjut Admin:</span>
                        <p class="leading-relaxed">{{ $report->admin_note }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $report->latitude }};
        const lng = {{ $report->longitude }};

        const map = L.map('detail-map', { zoomControl: false }).setView([lat, lng], 15);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            maxZoom: 19
        }).addTo(map);

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        const markerHtml = `
            <div style="background-color: {{ $report->category->color_code ?? '#3B82F6' }};" class="w-8 h-8 rounded-full border-2 border-white shadow-md flex items-center justify-center text-white text-sm font-bold">
                <i class="{{ $report->category->icon ?? 'ri-map-pin-fill' }}"></i>
            </div>
        `;
        const customIcon = L.divIcon({
            html: markerHtml,
            className: 'custom-detail-pin',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });

        L.marker([lat, lng], { icon: customIcon }).addTo(map);
    });
</script>
@endpush
