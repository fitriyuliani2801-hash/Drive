@extends('layouts.admin')

@section('title', 'Verifikasi & Kelola Pengaduan - Admin Metrologi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Kelola & Verifikasi Pengaduan Warga</h1>
            <p class="text-xs text-slate-500 mt-1">Tinjau, validasi status, dan tentukan tingkat urgensi penanganan pengaduan.</p>
        </div>
    </div>

    <!-- Status Tabs Nav -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200 text-xs">
        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2.5 rounded-xl font-bold transition-all whitespace-nowrap {{ !request('status') ? 'bg-teal-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:text-slate-900 border border-slate-200' }}">
            Semua Status
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="px-4 py-2.5 rounded-xl font-bold transition-all whitespace-nowrap {{ request('status') == 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-amber-700 hover:bg-amber-50 border border-amber-200' }}">
            Menunggu Verifikasi
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'verified']) }}" class="px-4 py-2.5 rounded-xl font-bold transition-all whitespace-nowrap {{ request('status') == 'verified' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-blue-700 hover:bg-blue-50 border border-blue-200' }}">
            Terverifikasi
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'in_progress']) }}" class="px-4 py-2.5 rounded-xl font-bold transition-all whitespace-nowrap {{ request('status') == 'in_progress' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-indigo-700 hover:bg-indigo-50 border border-indigo-200' }}">
            Sedang Diproses
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" class="px-4 py-2.5 rounded-xl font-bold transition-all whitespace-nowrap {{ request('status') == 'resolved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-emerald-700 hover:bg-emerald-50 border border-emerald-200' }}">
            Selesai
        </a>
        <a href="{{ route('admin.reports.index', ['status' => 'rejected']) }}" class="px-4 py-2.5 rounded-xl font-bold transition-all whitespace-nowrap {{ request('status') == 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-rose-700 hover:bg-rose-50 border border-rose-200' }}">
            Ditolak / Hoax
        </a>
    </div>

    <!-- Search & Filter Form -->
    <form action="{{ route('admin.reports.index') }}" method="GET" class="bg-white border border-slate-200 p-4 rounded-2xl space-y-4 md:space-y-0 md:flex items-center gap-4 shadow-xs">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div class="flex-1 relative">
            <i class="ri-search-line absolute left-3.5 top-3.5 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tiket, judul, pelapor..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white">
        </div>

        <div class="w-full md:w-48">
            <select name="urgency" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white">
                <option value="">Semua Urgensi</option>
                <option value="low" {{ request('urgency') == 'low' ? 'selected' : '' }}>Rendah</option>
                <option value="medium" {{ request('urgency') == 'medium' ? 'selected' : '' }}>Sedang</option>
                <option value="high" {{ request('urgency') == 'high' ? 'selected' : '' }}>Tinggi</option>
                <option value="critical" {{ request('urgency') == 'critical' ? 'selected' : '' }}>Kritis / Darurat</option>
            </select>
        </div>

        <div class="w-full md:w-48">
            <select name="category" onchange="this.form.submit()" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('admin.reports.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
            Reset
        </a>
    </form>

    <!-- Table -->
    <div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-4">Tiket & Tanggal</th>
                        <th class="px-5 py-4">Pelapor</th>
                        <th class="px-5 py-4">Judul Pengaduan</th>
                        <th class="px-5 py-4">Kategori & Wilayah</th>
                        <th class="px-5 py-4">Urgensi</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-mono font-bold text-slate-900 block">{{ $report->ticket_code }}</span>
                                <span class="text-[10px] text-slate-500">{{ $report->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-800 block">{{ $report->reporter_name }}</span>
                                <span class="text-[10px] text-slate-500">{{ $report->reporter_phone ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-900 max-w-xs truncate">
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="hover:text-teal-700 transition-colors">
                                    {{ $report->title }}
                                </a>
                            </td>
                            <td class="px-5 py-4 space-y-1">
                                <span class="px-2 py-0.5 rounded font-semibold text-white text-[10px] inline-block" style="background-color: {{ $report->category->color_code ?? '#3B82F6' }}">
                                    {{ $report->category->name ?? '-' }}
                                </span>
                                <span class="text-[10px] text-slate-500 block"><i class="ri-map-pin-line text-teal-600"></i> {{ $report->district }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 rounded font-semibold text-[10px] {{ $report->urgency_badge_class }}">
                                    {{ $report->urgency_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full font-bold border text-[10px] {{ $report->status_badge_class }}">
                                    {{ $report->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-600 hover:text-white font-bold text-xs transition-colors border border-teal-200 inline-flex items-center gap-1">
                                    <i class="ri-settings-4-line"></i> Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                                Tidak ada data pengaduan yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
