@extends('layouts.admin')

@section('title', 'Dashboard Analisis & Tren - Metrologi Kota Metro')

@section('content')
<div class="space-y-8">
    
    <!-- Title -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Dashboard Analisis & Tren Perkotaan</h1>
        <p class="text-xs text-slate-500 mt-1">Visualisasi data statistik agregat isu perkotaan di Kota Metro untuk pemangku kebijakan.</p>
    </div>

    <!-- Top Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Category Doughnut Chart -->
        <div class="lg:col-span-6 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-pie-chart-2-line text-teal-600"></i> Distribusi Isu per Kategori
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- District Bar Chart -->
        <div class="lg:col-span-6 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-bar-chart-2-line text-teal-600"></i> Sebaran Isu per Kecamatan di Kota Metro
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="districtChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Bottom Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Urgency Chart -->
        <div class="lg:col-span-5 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-alarm-warning-line text-rose-600"></i> Tingkat Urgensi Masuk
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="urgencyChart"></canvas>
            </div>
        </div>

        <!-- Status Summary Cards & Rates -->
        <div class="lg:col-span-7 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-6">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-checkbox-circle-line text-emerald-600"></i> Efektivitas & Status Penanganan
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach($statusCounts as $label => $count)
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">{{ $label }}</span>
                        <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $count }}</span>
                    </div>
                @endforeach
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center justify-between text-xs font-bold">
                    <span class="text-slate-700">Indikator Kinerja Pelayanan Publik (Rasio Selesai)</span>
                    @php
                        $total = array_sum($statusCounts);
                        $resolved = $statusCounts['Selesai'] ?? 0;
                        $rate = $total > 0 ? round(($resolved / $total) * 100) : 0;
                    @endphp
                    <span class="text-teal-700 font-mono text-sm">{{ $rate }}%</span>
                </div>
                <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden border border-slate-200">
                    <div class="h-full bg-gradient-to-r from-teal-600 to-emerald-500 rounded-full transition-all duration-1000" style="width: {{ $rate }}%;"></div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const catLabels = {!! json_encode($categoriesStats->pluck('name')) !!};
        const catData = {!! json_encode($categoriesStats->pluck('reports_count')) !!};
        const catColors = {!! json_encode($categoriesStats->pluck('color_code')) !!};

        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: catColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: '#334155', font: { family: 'Plus Jakarta Sans', size: 11 } }
                    }
                }
            }
        });

        const distLabels = {!! json_encode(array_keys($districtStats)) !!};
        const distData = {!! json_encode(array_values($districtStats)) !!};

        new Chart(document.getElementById('districtChart'), {
            type: 'bar',
            data: {
                labels: distLabels,
                datasets: [{
                    label: 'Jumlah Isu Laporan',
                    data: distData,
                    backgroundColor: '#0d9488',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { ticks: { color: '#475569' }, grid: { display: false } },
                    y: { ticks: { color: '#475569', precision: 0 }, grid: { color: '#f1f5f9' } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        const urgLabels = {!! json_encode(array_keys($urgencyCounts)) !!};
        const urgData = {!! json_encode(array_values($urgencyCounts)) !!};

        new Chart(document.getElementById('urgencyChart'), {
            type: 'pie',
            data: {
                labels: urgLabels,
                datasets: [{
                    data: urgData,
                    backgroundColor: ['#94a3b8', '#0284c7', '#f97316', '#e11d48'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#334155', font: { family: 'Plus Jakarta Sans', size: 11 } }
                    }
                }
            }
        });
    });
</script>
@endpush
