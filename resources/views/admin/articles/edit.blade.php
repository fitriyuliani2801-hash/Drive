@extends('layouts.admin')

@section('title', 'Sunting Artikel - Redaksi Metrologi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-slate-900 mb-2 font-heading">
            <i class="ri-arrow-left-line"></i> Kembali ke Daftar Berita
        </a>
        <h1 class="text-2xl font-black text-slate-900 font-heading">Sunting Artikel Berita</h1>
    </div>

    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Judul Artikel Berita <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Kategori Isu <span class="text-rose-500">*</span></label>
                <select name="category_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors font-heading">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $article->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Ringkasan Singkat (Excerpt) <span class="text-rose-500">*</span></label>
            <textarea name="excerpt" rows="2" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors leading-relaxed font-medium">{{ old('excerpt', $article->excerpt) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Isi Berita / Artikel Lengkap <span class="text-rose-500">*</span></label>
            <textarea name="content" rows="8" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors leading-relaxed font-medium">{{ old('content', $article->content) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Sumber / Penulis</label>
                <input type="text" name="source" value="{{ old('source', $article->source) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Link Rujukan Medsos (Opsional)</label>
                <input type="url" name="source_url" value="{{ old('source_url', $article->source_url) }}" placeholder="https://instagram.com/p/... atau TikTok / FB" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors font-medium">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2 font-heading">Kecamatan Kota Metro</label>
                <select name="district" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white font-heading">
                    <option value="">Seluruh Kota Metro</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist }}" {{ $article->district == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- FOTO BERITA (3 POSISI: UTAMA, PERTENGAHAN, AKHIRAN) & SCREENSHOT KOMENTAR UPLOADER -->
        <div class="space-y-6 bg-slate-50 p-6 rounded-3xl border border-slate-200">
            
            <div>
                <h3 class="text-xs font-black uppercase text-slate-900 font-heading flex items-center gap-2">
                    <i class="ri-image-line text-crimson-600"></i> Kelola Foto Artikel Berita (3 Posisi Foto)
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Anda dapat memperbarui foto untuk diletakkan di awal/sampul, pertengahan paragraf, dan bagian akhir artikel.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Foto 1: Utama / Atas -->
                <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-slate-800 font-heading">1. Foto Utama / Cover (Atas)</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-crimson-700 border border-slate-200 rounded-xl bg-slate-50">
                    @if($article->image_path)
                        <div class="mt-2 flex items-center justify-between p-2 bg-slate-50 border border-slate-200 rounded-xl text-[11px]">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('storage/' . $article->image_path) }}" class="w-9 h-9 object-cover rounded-lg border border-slate-200">
                                <span class="text-slate-600 font-medium">Terpasang</span>
                            </div>
                            <label class="flex items-center gap-1 font-bold text-rose-600 hover:text-rose-700 cursor-pointer bg-rose-50 hover:bg-rose-100 px-2 py-1 rounded-lg transition-colors">
                                <input type="checkbox" name="delete_main_image" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-0">
                                <span>Hapus</span>
                            </label>
                        </div>
                    @endif
                </div>

                <!-- Foto 2: Pertengahan -->
                <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-slate-800 font-heading">2. Foto Pertengahan Artikel</label>
                    <input type="file" name="middle_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-brand-700 border border-slate-200 rounded-xl bg-slate-50">
                    @if($article->middle_image_path)
                        <div class="mt-2 flex items-center justify-between p-2 bg-slate-50 border border-slate-200 rounded-xl text-[11px]">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('storage/' . $article->middle_image_path) }}" class="w-9 h-9 object-cover rounded-lg border border-slate-200">
                                <span class="text-slate-600 font-medium">Terpasang</span>
                            </div>
                            <label class="flex items-center gap-1 font-bold text-rose-600 hover:text-rose-700 cursor-pointer bg-rose-50 hover:bg-rose-100 px-2 py-1 rounded-lg transition-colors">
                                <input type="checkbox" name="delete_middle_image" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-0">
                                <span>Hapus</span>
                            </label>
                        </div>
                    @endif
                </div>

                <!-- Foto 3: Akhiran -->
                <div class="p-4 bg-white rounded-2xl border border-slate-200 space-y-2">
                    <label class="block text-xs font-bold text-slate-800 font-heading">3. Foto Akhiran Artikel</label>
                    <input type="file" name="end_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-800 border border-slate-200 rounded-xl bg-slate-50">
                    @if($article->end_image_path)
                        <div class="mt-2 flex items-center justify-between p-2 bg-slate-50 border border-slate-200 rounded-xl text-[11px]">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('storage/' . $article->end_image_path) }}" class="w-9 h-9 object-cover rounded-lg border border-slate-200">
                                <span class="text-slate-600 font-medium">Terpasang</span>
                            </div>
                            <label class="flex items-center gap-1 font-bold text-rose-600 hover:text-rose-700 cursor-pointer bg-rose-50 hover:bg-rose-100 px-2 py-1 rounded-lg transition-colors">
                                <input type="checkbox" name="delete_end_image" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-0">
                                <span>Hapus</span>
                            </label>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Existing Comment Screenshots Grid with Interactive Delete Option -->
            @php $existingImages = $article->comment_images ?? []; @endphp
            @if(!empty($existingImages))
                <div class="pt-4 border-t border-slate-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                        <label class="block text-xs font-bold text-slate-700 uppercase font-heading">🖼️ Screenshot Komentar yang Sudah Terpasang ({{ count($existingImages) }} Foto)</label>
                        <span class="text-[11px] text-slate-500">Centang opsi <strong class="text-rose-600">Hapus</strong> pada foto yang ingin dibuang.</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($existingImages as $idx => $imgPath)
                            <div class="existing-img-card relative group p-2.5 bg-white rounded-2xl border border-slate-200 text-center space-y-2 shadow-2xs transition-all">
                                <div class="relative overflow-hidden rounded-xl bg-slate-100 h-24">
                                    <img src="{{ asset('storage/' . $imgPath) }}" class="w-full h-full object-cover">
                                    <div class="delete-overlay hidden absolute inset-0 bg-rose-900/80 backdrop-blur-xs flex items-center justify-center text-white text-xs font-bold font-heading">
                                        <i class="ri-delete-bin-line mr-1"></i> Akan Dihapus
                                    </div>
                                </div>
                                <div class="flex items-center justify-between px-1 text-xs">
                                    <span class="text-[10px] font-bold text-slate-600">#{{ $idx + 1 }}</span>
                                    <label class="flex items-center gap-1.5 text-[11px] font-bold text-rose-600 hover:text-rose-700 cursor-pointer bg-rose-50 hover:bg-rose-100 px-2 py-0.5 rounded-lg transition-colors">
                                        <input type="checkbox" name="delete_comment_images[]" value="{{ $imgPath }}" onchange="toggleDeleteCard(this)" class="rounded border-rose-300 text-rose-600 focus:ring-0">
                                        <span>Hapus</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Dynamic Screenshot Komentar Uploader (Hingga 10 Foto Total) -->
            <div class="pt-4 border-t border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-900 font-heading">📷 Tambah Foto Screenshot Komentar Baru (Hingga 10 Foto)</label>
                        <p class="text-[11px] text-slate-500">Pilih file foto tangkapan layar komentar baru. Klik tombol **+ Tambah Foto** untuk menambah file berikutnya.</p>
                    </div>
                    <span id="photoCounter" class="text-xs font-bold text-crimson-700 font-heading bg-red-50 border border-red-200 px-3 py-1 rounded-full">
                        {{ count($existingImages) }} / 10 Foto Total
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
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5 font-heading">Tambah Komentar Netizen Baru (Opsional - Satu Komentar per Baris)</label>
            <textarea name="comments_text" rows="3" placeholder="Tempelkan (copy-paste) komentar netizen baru dari media sosial di sini..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-900 focus:outline-none focus:border-crimson-600 focus:bg-white transition-colors leading-relaxed font-normal"></textarea>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100 font-heading">
            <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" {{ $article->is_featured ? 'checked' : '' }} class="rounded bg-slate-50 border-slate-200 text-crimson-600 focus:ring-0">
                <span>Tampilkan sebagai Headline Utama (Featured)</span>
            </label>

            <button type="submit" class="px-6 py-3 rounded-2xl bg-crimson-700 hover:bg-crimson-800 text-white font-extrabold text-xs shadow-md transition-all">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const existingInitialCount = {{ count($existingImages) }};
    const maxPhotos = 10;

    function toggleDeleteCard(checkbox) {
        const card = checkbox.closest('.existing-img-card');
        const overlay = card.querySelector('.delete-overlay');
        if (checkbox.checked) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            card.classList.add('border-rose-400', 'bg-rose-50/50');
        } else {
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
            card.classList.remove('border-rose-400', 'bg-rose-50/50');
        }
        updateCounter();
    }

    function updateCounter() {
        const deletedCount = document.querySelectorAll('input[name="delete_comment_images[]"]:checked').length;
        const remainingExisting = existingInitialCount - deletedCount;
        const rows = document.querySelectorAll('.image-input-row');
        const total = remainingExisting + rows.length;
        
        document.getElementById('photoCounter').innerText = `${total} / ${maxPhotos} Foto Total`;
        
        const addBtn = document.getElementById('addPhotoButton');
        if (total >= maxPhotos) {
            addBtn.classList.add('hidden');
        } else {
            addBtn.classList.remove('hidden');
        }
    }

    function addPhotoInput() {
        const container = document.getElementById('commentImagesContainer');
        const rows = container.querySelectorAll('.image-input-row');
        const deletedCount = document.querySelectorAll('input[name="delete_comment_images[]"]:checked').length;
        const remainingExisting = existingInitialCount - deletedCount;
        
        if ((remainingExisting + rows.length) >= maxPhotos) {
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

            const deletedCount = document.querySelectorAll('input[name="delete_comment_images[]"]:checked').length;
            const remainingExisting = existingInitialCount - deletedCount;
            const rows = document.querySelectorAll('.image-input-row');
            
            if ((remainingExisting + rows.length) < maxPhotos && row === rows[rows.length - 1]) {
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
