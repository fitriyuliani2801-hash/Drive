@extends('layouts.app')

@section('title', 'Hasil Analisis ' . $analysis->post_title . ' - Metrologi')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Navigation Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('social.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar Analisis Medsos
        </a>
        
        <a href="{{ $analysis->url }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors flex items-center gap-1">
            <i class="ri-external-link-line"></i> Buka Link Asli di {{ $analysis->platform }}
        </a>
    </div>

    <!-- 1. Verdict & Fact Check Header Card -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <!-- Verdict Badge -->
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shadow-md {{ $analysis->verdict == 'asli' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}">
                    <i class="{{ $analysis->verdict == 'asli' ? 'ri-checkbox-circle-fill' : 'ri-close-circle-fill' }}"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Status Hasil Penyaringan Keaslian:</span>
                    <h2 class="text-xl sm:text-2xl font-black {{ $analysis->verdict == 'asli' ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $analysis->verdict == 'asli' ? 'BERITA ASLI / TERVERIFIKASI FAKTA' : 'TERINDIKASI HOAKS / DISINFORMASI' }}
                    </h2>
                </div>
            </div>

            <!-- Score Badge -->
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-right self-start md:self-auto">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Tingkat Kepercayaan (Confidence Score)</span>
                <span class="text-2xl font-black font-mono {{ $analysis->verdict == 'asli' ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $analysis->verdict_score }}%
                </span>
            </div>
        </div>

        <!-- Fact Check Reasoning -->
        <div class="p-4 rounded-2xl {{ $analysis->verdict == 'asli' ? 'bg-emerald-50/70 border border-emerald-200 text-emerald-950' : 'bg-rose-50/70 border border-rose-200 text-rose-950' }} space-y-1 text-xs">
            <span class="font-bold block uppercase tracking-wider text-[11px]"><i class="ri-shield-keyhole-line"></i> Penjelasan Verifikasi Sistem AI:</span>
            <p class="leading-relaxed">{{ $analysis->verdict_reasoning }}</p>
        </div>

    </div>

    <!-- 2. Post Content & Details Card -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 text-xs border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full font-bold bg-slate-100 text-slate-800 text-[11px] border border-slate-200">
                    {{ $analysis->platform }}
                </span>
                <span class="font-bold text-slate-900"><i class="ri-user-3-line text-teal-600"></i> {{ $analysis->author_name }}</span>
            </div>
            <span class="text-slate-500"><i class="ri-time-line"></i> Di-analisis {{ $analysis->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
        </div>

        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-tight">
            {{ $analysis->post_title }}
        </h1>

        <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line font-normal bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
            "{{ $analysis->post_content }}"
        </div>
    </div>

    <!-- 3. Public Sentiment Breakdown & Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Doughnut Chart -->
        <div class="lg:col-span-5 bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xs space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-pie-chart-2-line text-teal-600"></i> Distribusi Sentimen Publik Komentar
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="sentimentChart"></canvas>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="lg:col-span-7 bg-white border border-slate-200/90 rounded-3xl p-6 shadow-xs space-y-6 flex flex-col justify-between">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-chat-smile-2-line text-emerald-600"></i> Ringkasan Klasifikasi Sentimen Komentar
            </h3>

            <div class="grid grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider block text-emerald-700"><i class="ri-thumb-up-fill"></i> Komentar Positif</span>
                    <span class="text-3xl font-black block">{{ number_format($analysis->positive_count) }}</span>
                    <span class="text-[10px] text-emerald-700 font-semibold">Respon bagus & apresiasi</span>
                </div>

                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider block text-rose-700"><i class="ri-thumb-down-fill"></i> Komentar Negatif</span>
                    <span class="text-3xl font-black block">{{ number_format($analysis->negative_count) }}</span>
                    <span class="text-[10px] text-rose-700 font-semibold">Kritik, keluhan & sanggahan</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-slate-900 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider block text-slate-500"><i class="ri-chat-neutral-fill"></i> Komentar Netral</span>
                    <span class="text-3xl font-black block">{{ number_format($analysis->neutral_count) }}</span>
                    <span class="text-[10px] text-slate-500 font-semibold">Pertanyaan & fakta umum</span>
                </div>
            </div>

            @php
                $totalC = max(1, $analysis->positive_count + $analysis->negative_count + $analysis->neutral_count);
                $posPercent = round(($analysis->positive_count / $totalC) * 100);
            @endphp
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center justify-between text-xs font-bold">
                    <span class="text-slate-700">Rasio Dukungan Publik Positif</span>
                    <span class="text-emerald-700 font-mono text-sm">{{ $posPercent }}% Positif</span>
                </div>
                <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden flex border border-slate-200">
                    <div class="h-full bg-emerald-500" style="width: {{ $posPercent }}%;"></div>
                    <div class="h-full bg-rose-500" style="width: {{ round(($analysis->negative_count / $totalC) * 100) }}%;"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. Public Comments List & Filter Tabs -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Daftar Komentar Publik & Pemisahan Sentimen</h3>
                <p class="text-xs text-slate-500 mt-0.5">Filter komentar berdasarkan kategori sentimen positif atau negatif.</p>
            </div>

            <!-- Sentiment Filter Tabs -->
            <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-200 text-xs font-bold">
                <a href="{{ route('social.show', $analysis->id) }}" class="px-3 py-1.5 rounded-lg transition-all {{ !request('sentiment') ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua ({{ $analysis->comments->count() }})
                </a>
                <a href="{{ route('social.show', [$analysis->id, 'sentiment' => 'positif']) }}" class="px-3 py-1.5 rounded-lg transition-all {{ request('sentiment') == 'positif' ? 'bg-emerald-600 text-white shadow-xs' : 'text-emerald-700 hover:bg-emerald-50' }}">
                    <i class="ri-thumb-up-line"></i> Positif ({{ $analysis->positive_count }})
                </a>
                <a href="{{ route('social.show', [$analysis->id, 'sentiment' => 'negatif']) }}" class="px-3 py-1.5 rounded-lg transition-all {{ request('sentiment') == 'negatif' ? 'bg-rose-600 text-white shadow-xs' : 'text-rose-700 hover:bg-rose-50' }}">
                    <i class="ri-thumb-down-line"></i> Negatif ({{ $analysis->negative_count }})
                </a>
            </div>
        </div>

        <!-- Comments Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5">Penulis Komentar</th>
                        <th class="px-4 py-3.5">Isi Teks Komentar Publik</th>
                        <th class="px-4 py-3.5 text-center">Klasifikasi Sentimen</th>
                        <th class="px-4 py-3.5 text-right">Skor Sentimen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($comments as $c)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-slate-900">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[10px] border border-slate-200">
                                        {{ strtoupper(substr($c->author_name, 1, 1)) }}
                                    </div>
                                    <span>{{ $c->author_name }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-4 text-slate-800 leading-relaxed font-normal">
                                "{{ $c->raw_comment }}"
                            </td>

                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                @if($c->sentiment === 'positif')
                                    <span class="px-3 py-1 rounded-full font-extrabold text-[10px] uppercase bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                        <i class="ri-thumb-up-fill"></i> Positif
                                    </span>
                                @elseif($c->sentiment === 'negatif')
                                    <span class="px-3 py-1 rounded-full font-extrabold text-[10px] uppercase bg-rose-100 text-rose-800 border border-rose-200 inline-flex items-center gap-1">
                                        <i class="ri-thumb-down-fill"></i> Negatif
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full font-extrabold text-[10px] uppercase bg-slate-100 text-slate-700 border border-slate-200 inline-flex items-center gap-1">
                                        <i class="ri-chat-neutral-line"></i> Netral
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 text-right font-mono font-bold text-slate-700">
                                {{ number_format($c->sentiment_score * 100, 1) }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                                Tidak ada komentar yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $comments->links() }}
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const posCount = {{ $analysis->positive_count }};
        const negCount = {{ $analysis->negative_count }};
        const neuCount = {{ $analysis->neutral_count }};

        new Chart(document.getElementById('sentimentChart'), {
            type: 'doughnut',
            data: {
                labels: ['Komentar Positif', 'Komentar Negatif', 'Komentar Netral'],
                datasets: [{
                    data: [posCount, negCount, neuCount],
                    backgroundColor: ['#10B981', '#EF4444', '#94A3B8'],
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
