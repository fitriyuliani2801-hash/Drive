@extends('layouts.admin')

@section('title', 'Verifikasi Laporan ' . $report->ticket_code . ' - Admin Metrologi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar Pengaduan
        </a>

        <a href="{{ route('reports.show', $report->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors flex items-center gap-1">
            <i class="ri-external-link-line"></i> Pratinjau Tampilan Publik
        </a>
    </div>

    <!-- Title & Ticket -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded bg-slate-100 text-teal-800 border border-slate-200">
                    {{ $report->ticket_code }}
                </span>
                <span class="text-xs text-slate-500">Dibuat {{ $report->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ $report->title }}</h1>
            <p class="text-xs text-slate-600 mt-1"><i class="ri-map-pin-line text-teal-600"></i> {{ $report->location_address }} ({{ $report->district }})</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-full text-xs font-bold border {{ $report->status_badge_class }}">
                {{ $report->status_label }}
            </span>
            <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $report->urgency_badge_class }}">
                Urgensi: {{ $report->urgency_label }}
            </span>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Moderation Form -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Moderation Form Card -->
            <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-5">
                @csrf
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i class="ri-shield-check-line text-teal-600"></i> Panel Moderasi Admin & Verifikasi
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Ubah Status Pengaduan</label>
                        <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white">
                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="verified" {{ $report->status == 'verified' ? 'selected' : '' }}>Terverifikasi (Valid)</option>
                            <option value="in_progress" {{ $report->status == 'in_progress' ? 'selected' : '' }}>Sedang Diproses Dinas</option>
                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Selesai Ditangani</option>
                            <option value="rejected" {{ $report->status == 'rejected' ? 'selected' : '' }}>Ditolak / Hoax</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tingkat Urgensi</label>
                        <select name="urgency" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white">
                            <option value="low" {{ $report->urgency == 'low' ? 'selected' : '' }}>Rendah</option>
                            <option value="medium" {{ $report->urgency == 'medium' ? 'selected' : '' }}>Sedang</option>
                            <option value="high" {{ $report->urgency == 'high' ? 'selected' : '' }}>Tinggi</option>
                            <option value="critical" {{ $report->urgency == 'critical' ? 'selected' : '' }}>Kritis / Darurat</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Kategori Isu</label>
                    <select name="category_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $report->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Catatan Tindak Lanjut Admin / Instansi</label>
                    <textarea name="admin_note" rows="3" placeholder="Tambahkan catatan verifikasi, instansi yang ditugaskan, atau penjelasan penanganan..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white leading-relaxed">{{ old('admin_note', $report->admin_note) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Unggah Foto Bukti Penyelesaian (Opsional)</label>
                    <input type="file" name="resolution_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-teal-700 hover:file:bg-slate-200">
                    @if($report->resolution_image_path)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-[11px] text-emerald-700 font-semibold"><i class="ri-checkbox-circle-fill"></i> Foto penyelesaian sudah diunggah.</span>
                        </div>
                    @endif
                </div>

                <div class="pt-3 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-xs shadow-md shadow-teal-600/20 transition-all flex items-center gap-1.5">
                        <i class="ri-save-3-line text-sm"></i> Simpan Pembaruan Moderasi
                    </button>
                </div>
            </form>

            <!-- Original Report Content -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Rincian Pengaduan Pelapor</h3>
                <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">{{ $report->description }}</p>

                @if($report->image_path)
                    <div class="pt-2">
                        <span class="text-xs font-bold text-slate-500 block mb-2">Foto Bukti Pelapor:</span>
                        <img src="{{ asset('storage/' . $report->image_path) }}" alt="Foto Pelapor" class="rounded-xl max-h-64 object-cover border border-slate-200">
                    </div>
                @endif
            </div>

        </div>

        <!-- Right: Map & History Log -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Map -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Titik Koordinat Peta</h3>
                <div id="admin-map" class="h-56 w-full rounded-xl border border-slate-200 z-10"></div>
                <div class="text-[11px] text-slate-600 font-mono">
                    Lat: {{ $report->latitude }}, Lng: {{ $report->longitude }}
                </div>
            </div>

            <!-- History Logs -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-xs space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Log Riwayat Perubahan Status</h3>
                <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar pr-1">
                    @foreach($report->logs as $log)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1 text-xs">
                            <div class="flex items-center justify-between text-teal-800 font-bold">
                                <span>Status: {{ ucfirst(str_replace('_', ' ', $log->status_to)) }}</span>
                                <span class="text-[10px] text-slate-500 font-normal">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($log->note)
                                <p class="text-slate-700 text-[11px]">{{ $log->note }}</p>
                            @endif
                            <p class="text-[10px] text-slate-500">Oleh: {{ $log->user->name ?? 'Admin' }}</p>
                        </div>
                    @endforeach
                </div>
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

        const map = L.map('admin-map', { zoomControl: false }).setView([lat, lng], 15);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19
        }).addTo(map);

        const markerHtml = `
            <div style="background-color: {{ $report->category->color_code ?? '#3B82F6' }};" class="w-7 h-7 rounded-full border-2 border-white shadow-md flex items-center justify-center text-white text-xs font-bold">
                <i class="ri-map-pin-fill"></i>
            </div>
        `;
        const customIcon = L.divIcon({
            html: markerHtml,
            className: 'custom-admin-pin',
            iconSize: [28, 28],
            iconAnchor: [14, 28]
        });

        L.marker([lat, lng], { icon: customIcon }).addTo(map);
    });
</script>
@endpush
