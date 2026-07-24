@extends('layouts.app')

@section('title', 'Buat Pengaduan Warga - Metrologi Kota Metro')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors mb-3">
            <i class="ri-arrow-left-line"></i> Kembali ke Beranda
        </a>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-xs font-bold mb-2">
            <i class="ri-user-unfollow-line text-teal-600"></i> Terbuka untuk Umum - Tanpa Perlu Registrasi / Login
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900">Formulir Pengaduan Warga Kota Metro</h1>
        <p class="text-slate-600 text-sm mt-1">Kirimkan aspirasi atau temuan isu perkotaan di lingkungan sekitar Anda secara cepat, akurat, dan transparan.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
            <p class="font-bold mb-1 flex items-center gap-1"><i class="ri-error-warning-line text-rose-600"></i> Mohon perbaiki beberapa kesalahan berikut:</p>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Card 1: Identitas Pelapor & Kategori -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs border border-teal-200">1</span>
                Informasi Pelapor & Kategori Isu
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nama Pelapor <span class="text-rose-500">*</span></label>
                    <input type="text" name="reporter_name" value="{{ old('reporter_name', Auth::user()->name ?? 'Warga Metro') }}" required placeholder="Masukkan nama Anda (atau Anonim)..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nomor Telepon / WA (Opsional)</label>
                    <input type="text" name="reporter_phone" value="{{ old('reporter_phone', Auth::user()->phone ?? '') }}" placeholder="Contoh: 081234567890" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-3">Pilih Kategori Isu <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($categories as $category)
                        <label class="relative flex items-center p-3.5 rounded-2xl border border-slate-200 bg-slate-50 hover:border-teal-500/50 hover:bg-white cursor-pointer group transition-all">
                            <input type="radio" name="category_id" value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'checked' : ($loop->first ? 'checked' : '') }} class="text-teal-600 focus:ring-0 mr-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg" style="background-color: {{ $category->color_code }}15; color: {{ $category->color_code }}">
                                    <i class="{{ $category->icon }}"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-800 group-hover:text-teal-700">{{ $category->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card 2: Detail Pengaduan -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs border border-teal-200">2</span>
                Detail Permasalahan
            </h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Judul Pengaduan <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Tumpukan Sampah di Sudut Lapangan 16C Ganjar Asri" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Deskripsi Lengkap <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="4" required placeholder="Jelaskan kondisi permasalahan secara rinci (perkiraan ukuran/luas, dampak terhadap warga, dll)..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors leading-relaxed">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Card 3: Geospasial & Lokasi Presisi -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs border border-teal-200">3</span>
                Penentuan Lokasi Geospasial Kota Metro
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Kecamatan <span class="text-rose-500">*</span></label>
                    <select name="district" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors">
                        @foreach($districts as $dist)
                            <option value="{{ $dist }}" {{ old('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Alamat / Patokan Lokasi <span class="text-rose-500">*</span></label>
                    <input type="text" name="location_address" id="location_address" value="{{ old('location_address') }}" required placeholder="Contoh: Jl. AH Nasution No. 45, Yosodadi" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors">
                </div>
            </div>

            <!-- Interactive Map Coordinate Picker -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Pilih Titik Koordinat Presisi pada Peta (Klik / Geser Pin)</label>
                <div id="picker-map" class="h-80 w-full rounded-2xl border border-slate-200 z-10 mb-3 shadow-xs"></div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[11px] text-slate-500 font-medium">Latitude:</span>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude', '-5.113900') }}" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-mono text-teal-700 font-bold">
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-medium">Longitude:</span>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude', '105.307200') }}" readonly class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-mono text-teal-700 font-bold">
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Upload Foto Bukti -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                <span class="w-8 h-8 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-xs border border-teal-200">4</span>
                Unggah Foto Bukti Kejadian
            </h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Foto Bukti (Maks. 5MB, format JPEG/PNG/WEBP)</label>
                <div class="border-2 border-dashed border-slate-200 hover:border-teal-500 bg-slate-50/60 rounded-2xl p-8 text-center cursor-pointer transition-colors relative">
                    <input type="file" name="image" id="image-upload" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-20" onchange="previewImage(event)">
                    <div id="upload-placeholder" class="space-y-2">
                        <div class="w-12 h-12 rounded-full bg-white text-teal-600 shadow-md flex items-center justify-center mx-auto text-2xl border border-slate-200">
                            <i class="ri-upload-cloud-2-line"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-800">Klik atau seret foto ke area ini</p>
                        <p class="text-xs text-slate-500">Pastikan foto jelas untuk mempercepat verifikasi admin</p>
                    </div>
                    <div id="image-preview-container" class="hidden mt-2">
                        <img id="image-preview" src="#" alt="Preview Foto Bukti" class="max-h-64 mx-auto rounded-xl border border-slate-200 shadow-md">
                        <p class="text-xs text-teal-700 font-semibold mt-2"><i class="ri-checkbox-circle-fill"></i> Foto siap diunggah</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-4">
            <a href="{{ route('home') }}" class="px-6 py-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors">
                Batal
            </a>
            <button type="submit" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold shadow-lg shadow-teal-600/20 hover:-translate-y-0.5 transition-all text-sm flex items-center gap-2">
                <i class="ri-send-plane-fill text-lg"></i>
                Kirim Pengaduan Sekarang
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const defaultLat = parseFloat(document.getElementById('latitude').value) || -5.113900;
        const defaultLng = parseFloat(document.getElementById('longitude').value) || 105.307200;

        const pickerMap = L.map('picker-map').setView([defaultLat, defaultLng], 14);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            maxZoom: 19
        }).addTo(pickerMap);

        const markerHtml = `
            <div class="w-8 h-8 rounded-full bg-teal-600 border-2 border-white shadow-lg flex items-center justify-center text-white text-sm font-bold">
                <i class="ri-map-pin-user-fill"></i>
            </div>
        `;
        const customIcon = L.divIcon({
            html: markerHtml,
            className: 'custom-picker-pin',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });

        const marker = L.marker([defaultLat, defaultLng], {
            draggable: true,
            icon: customIcon
        }).addTo(pickerMap);

        function updateCoords(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
        }

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            updateCoords(position.lat, position.lng);
        });

        pickerMap.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            marker.setLatLng([lat, lng]);
            updateCoords(lat, lng);
        });
    });

    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('upload-placeholder').classList.add('hidden');
                const container = document.getElementById('image-preview-container');
                container.classList.remove('hidden');
                document.getElementById('image-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
