@extends('layouts.app')

@section('title', 'Peta Isu Interaktif Kota Metro - Metrologi')

@section('content')
<div class="h-[calc(100vh-80px)] flex flex-col md:flex-row overflow-hidden relative">
    
    <!-- Left Filter Sidebar -->
    <div class="w-full md:w-96 bg-white border-r border-slate-200 p-5 flex flex-col justify-between overflow-y-auto custom-scrollbar z-20 shadow-sm">
        <div class="space-y-6">
            <div>
                <span class="text-xs font-bold text-teal-700 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-200 uppercase tracking-wider">Pemetaan Geospasial</span>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-2 flex items-center gap-2">
                    <i class="ri-map-2-line text-teal-600"></i> Peta Isu Kota Metro
                </h1>
                <p class="text-xs text-slate-500 mt-1">Saring titik lokasi berdasarkan status penanganan, kecamatan, atau kategori isu.</p>
            </div>

            <!-- Search Form -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Cari Kata Kunci</label>
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-3 text-slate-400"></i>
                    <input type="text" id="map-search" placeholder="Cari judul, tiket, atau lokasi..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors">
                </div>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Status Penanganan</label>
                <select id="filter-status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:bg-white">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Verifikasi</option>
                    <option value="verified">Terverifikasi</option>
                    <option value="in_progress">Sedang Diproses</option>
                    <option value="resolved">Selesai</option>
                    <option value="rejected">Ditolak / Hoax</option>
                </select>
            </div>

            <!-- Filter District -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Kecamatan di Kota Metro</label>
                <select id="filter-district" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:bg-white">
                    <option value="">Seluruh Kecamatan</option>
                    @foreach($districts as $district)
                        <option value="{{ $district }}">{{ $district }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Category -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Kategori Isu</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <input type="radio" name="category-filter" value="" checked class="text-teal-600 focus:ring-0">
                        <span class="font-medium">Semua Kategori</span>
                    </label>
                    @foreach($categories as $cat)
                        <label class="flex items-center justify-between text-sm text-slate-700 cursor-pointer p-2 rounded-lg hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="category-filter" value="{{ $cat->slug }}" class="text-teal-600 focus:ring-0">
                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color_code }}"></span>
                                <span class="font-medium">{{ $cat->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Reset Filter Button -->
            <button id="reset-filters" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors flex items-center justify-center gap-2">
                <i class="ri-refresh-line"></i> Reset Filter Peta
            </button>
        </div>

        <div class="pt-6 border-t border-slate-200 text-xs text-slate-500 space-y-1">
            <p><i class="ri-information-line text-teal-600"></i> Klik penanda di peta untuk melihat detail pengaduan & foto bukti.</p>
        </div>
    </div>

    <!-- Right Map Container -->
    <div class="flex-1 h-full relative">
        <div id="full-map" class="h-full w-full z-10"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('full-map').setView([-5.1139, 105.3072], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            maxZoom: 19
        }).addTo(map);

        let geoJsonLayer = null;

        function loadMapPoints() {
            const status = document.getElementById('filter-status').value;
            const district = document.getElementById('filter-district').value;
            const selectedCategoryRadio = document.querySelector('input[name="category-filter"]:checked');
            const category = selectedCategoryRadio ? selectedCategoryRadio.value : '';

            let url = "{{ route('api.reports.geojson') }}?";
            const params = new URLSearchParams();
            if (status) params.append('status', status);
            if (district) params.append('district', district);
            if (category) params.append('category', category);

            fetch(url + params.toString())
                .then(res => res.json())
                .then(data => {
                    if (geoJsonLayer) {
                        map.removeLayer(geoJsonLayer);
                    }

                    geoJsonLayer = L.geoJSON(data, {
                        pointToLayer: function (feature, latlng) {
                            const color = feature.properties.color || '#3B82F6';
                            const markerHtml = `
                                <div style="background-color: ${color};" class="w-8 h-8 rounded-full border-2 border-white shadow-md flex items-center justify-center text-white text-xs font-bold transform hover:scale-125 transition-transform cursor-pointer">
                                    <i class="${feature.properties.icon || 'ri-map-pin-fill'}"></i>
                                </div>
                            `;
                            const customIcon = L.divIcon({
                                html: markerHtml,
                                className: 'custom-map-pin',
                                iconSize: [32, 32],
                                iconAnchor: [16, 32]
                            });
                            return L.marker(latlng, { icon: customIcon });
                        },
                        onEachFeature: function (feature, layer) {
                            const props = feature.properties;
                            const imageTag = props.image ? `<img src="${props.image}" class="w-full h-32 object-cover rounded-lg mb-2 border border-slate-200">` : '';
                            
                            const popupContent = `
                                <div class="p-2 max-w-xs text-slate-900 font-sans">
                                    ${imageTag}
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded bg-slate-100 border text-slate-700">${props.ticket_code}</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded text-white" style="background-color: ${props.color}">${props.category}</span>
                                    </div>
                                    <h4 class="font-bold text-sm text-slate-900 leading-snug">${props.title}</h4>
                                    <p class="text-xs text-slate-600 mt-1"><i class="ri-map-pin-2-line text-teal-600"></i> ${props.location} (${props.district})</p>
                                    <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-200">
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded ${props.status_badge}">${props.status_label}</span>
                                        <a href="${props.url}" class="px-2.5 py-1 text-xs font-bold rounded bg-teal-600 hover:bg-teal-700 text-white transition-colors">Detail &rarr;</a>
                                    </div>
                                </div>
                            `;
                            layer.bindPopup(popupContent);
                        }
                    }).addTo(map);
                });
        }

        document.getElementById('filter-status').addEventListener('change', loadMapPoints);
        document.getElementById('filter-district').addEventListener('change', loadMapPoints);
        document.querySelectorAll('input[name="category-filter"]').forEach(radio => {
            radio.addEventListener('change', loadMapPoints);
        });

        document.getElementById('reset-filters').addEventListener('click', function() {
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-district').value = '';
            document.querySelector('input[name="category-filter"][value=""]').checked = true;
            document.getElementById('map-search').value = '';
            loadMapPoints();
        });

        loadMapPoints();
    });
</script>
@endpush
