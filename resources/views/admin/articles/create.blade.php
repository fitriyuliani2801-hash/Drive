@extends('layouts.admin')

@section('title', 'Terbitkan Artikel Berita Baru - Redaksi Metrologi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-slate-900 mb-2 font-heading">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar Berita
        </a>
        <h1 class="text-2xl font-black text-slate-900 font-heading">Terbitkan Artikel Berita Baru</h1>
        <p class="text-xs text-slate-500 mt-1">Tulis dan terbitkan berita liputan khusus Kota Metro lengkap dengan bukti tangkapan layar / screenshot komentar netizen medsos (hingga 10 foto).</p>
    </div>

    <!-- Form Tulis Artikel Berita -->
    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Judul Artikel Berita <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Masukkan judul artikel berita..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Kategori Isu <span class="text-rose-500">*</span></label>
                <select name="category_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors font-heading">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Ringkasan Singkat (Excerpt) <span class="text-rose-500">*</span></label>
            <textarea name="excerpt" rows="2" required placeholder="Tuliskan ringkasan 2-3 kalimat mengenai isi berita..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors leading-relaxed font-medium">{{ old('excerpt') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Isi Berita / Artikel Lengkap <span class="text-rose-500">*</span></label>
            <textarea name="content" rows="8" required placeholder="Tuliskan berita atau artikel lengkap..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors leading-relaxed font-medium">{{ old('content') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Sumber / Penulis</label>
                <input type="text" name="source" value="{{ old('source', 'Redaksi METROLOGI NEWS') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Link Rujukan Medsos (Opsional)</label>
                <input type="url" name="source_url" value="{{ old('source_url') }}" placeholder="https://instagram.com/p/... atau TikTok / FB" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Kecamatan Kota Metro</label>
                <select name="district" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white font-heading">
                    <option value="">Seluruh Kota Metro</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist }}">{{ $dist }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- FOTO BERITA (3 POSISI: UTAMA, PERTENGAHAN, AKHIRAN) & SCREENSHOT KOMENTAR UPLOADER -->
        <div class="space-y-6 bg-slate-50 p-6 rounded-3xl border border-slate-200">
            
            <div>
                <h3 class="text-xs font-black uppercase text-slate-900 font-heading flex items-center gap-2">
                    <i class="ri-image-line text-crimson-600"></i> Upload Foto Artikel Berita (3 Posisi Foto)
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Anda dapat memilih foto untuk diletakkan di awal/sampul, pertengahan paragraf, dan bagian akhir artikel.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Foto 1: Utama / Atas -->
                <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-slate-800 font-heading">1. Foto Utama / Cover (Atas)</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-crimson-700 border border-slate-200 rounded-xl bg-slate-50">
                    <p class="text-[10px] text-slate-400">Tampil di awal berita di bawah ringkasan.</p>
                </div>

                <!-- Foto 2: Pertengahan -->
                <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-slate-800 font-heading">2. Foto Pertengahan Artikel</label>
                    <input type="file" name="middle_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-brand-700 border border-slate-200 rounded-xl bg-slate-50">
                    <p class="text-[10px] text-slate-400">Disisipkan di pertengahan artikel.</p>
                </div>

                <!-- Foto 3: Akhiran -->
                <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-slate-800 font-heading">3. Foto Akhiran Artikel</label>
                    <input type="file" name="end_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-800 border border-slate-200 rounded-xl bg-slate-50">
                    <p class="text-[10px] text-slate-400">Tampil di bagian penutup artikel.</p>
                </div>
            </div>

            <!-- Dynamic Screenshot Komentar Uploader (Hingga 10 Foto) -->
            <div class="pt-4 border-t border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-900 font-heading">📷 Tangkapan Layar (Screenshot) Komentar Netizen Medsos</label>
                        <p class="text-[11px] text-slate-500">Unggah hingga 10 foto screenshot komentar. Klik tombol **+ Tambah Foto** untuk menambah file berikutnya.</p>
                    </div>
                    <span id="photoCounter" class="text-xs font-bold text-crimson-700 font-heading bg-red-50 border border-red-200 px-3 py-1 rounded-full">
                        0 / 10 Foto Diberikan
                    </span>
                </div>

                <!-- Input Container Fields -->
                <div id="commentImagesContainer" class="space-y-3">
                    <div class="image-input-row flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-2xl shadow-2xs">
                        <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-black text-xs shrink-0 font-heading">#1</span>
                        <input type="file" name="comment_images[]" accept="image/*" onchange="previewImage(this)" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 border border-slate-200 rounded-xl">
                        <div class="preview-box hidden shrink-0">
                            <img src="" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
                        </div>
                    </div>
                </div>

                <!-- + Add More Photo Button -->
                <div class="pt-2">
                    <button type="button" id="addPhotoButton" onclick="addPhotoInput()" class="px-5 py-2.5 rounded-2xl bg-white border border-slate-300 hover:border-crimson-600 hover:text-crimson-700 text-slate-700 font-bold text-xs transition-all shadow-xs flex items-center gap-2 font-heading">
                        <i class="ri-add-line text-lg text-crimson-600"></i> + Tambah Foto Screenshot Komentar Lagi
                    </button>
                </div>
            </div>

        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5 font-heading">Teks Komentar Netizen Medsos (Opsional - Satu Komentar per Baris)</label>
            <textarea name="comments_text" rows="3" placeholder="Tempelkan (copy-paste) teks komentar netizen dari media sosial (satu baris per komentar). Sistem AI akan otomatis menganalisis sentimennya..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors leading-relaxed font-normal">{{ old('comments_text') }}</textarea>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100 font-heading">
            <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" class="rounded bg-slate-50 border-slate-200 text-crimson-600 focus:ring-0">
                <span>Tampilkan sebagai Headline Utama (Featured)</span>
            </label>

            <button type="submit" class="px-6 py-3 rounded-2xl bg-crimson-700 hover:bg-crimson-800 text-white font-extrabold text-xs shadow-md transition-all">
                Terbitkan Berita
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let photoCount = 1;
    const maxPhotos = 10;

    function updateCounter() {
        const rows = document.querySelectorAll('.image-input-row');
        document.getElementById('photoCounter').innerText = `${rows.length} / ${maxPhotos} Foto Diberikan`;
        
        const addBtn = document.getElementById('addPhotoButton');
        if (rows.length >= maxPhotos) {
            addBtn.classList.add('hidden');
        } else {
            addBtn.classList.remove('hidden');
        }
    }

    function addPhotoInput() {
        const container = document.getElementById('commentImagesContainer');
        const rows = container.querySelectorAll('.image-input-row');
        
        if (rows.length >= maxPhotos) {
            alert('Maksimal foto screenshot komentar yang dapat diunggah adalah 10 foto.');
            return;
        }

        const nextNum = rows.length + 1;
        const newRow = document.createElement('div');
        newRow.className = 'image-input-row flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-2xl shadow-2xs transition-all';
        newRow.innerHTML = `
            <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-black text-xs shrink-0 font-heading">#${nextNum}</span>
            <input type="file" name="comment_images[]" accept="image/*" onchange="previewImage(this)" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 border border-slate-200 rounded-xl">
            <div class="preview-box hidden shrink-0">
                <img src="" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
            </div>
            <button type="button" onclick="removePhotoInput(this)" class="p-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-bold shrink-0 transition-colors">
                <i class="ri-delete-bin-line"></i>
            </button>
        `;

        container.appendChild(newRow);
        updateCounter();
    }

    function removePhotoInput(btn) {
        const row = btn.closest('.image-input-row');
        row.remove();
        
        // Renumber remaining inputs
        const rows = document.querySelectorAll('.image-input-row');
        rows.forEach((r, idx) => {
            r.querySelector('span').innerText = `#${idx + 1}`;
        });

        updateCounter();
    }

    function previewImage(input) {
        const row = input.closest('.image-input-row');
        const previewBox = row.querySelector('.preview-box');
        const img = previewBox.querySelector('img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                previewBox.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);

            // Automatically offer next slot if not reached limit
            const rows = document.querySelectorAll('.image-input-row');
            if (rows.length < maxPhotos && row === rows[rows.length - 1]) {
                addPhotoInput();
            }
        } else {
            previewBox.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', updateCounter);
</script>
@endpush
@endsection
