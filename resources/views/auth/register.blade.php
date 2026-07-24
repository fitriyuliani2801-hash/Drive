@extends('layouts.app')

@section('title', 'Daftar Akun - Metrologi Kota Metro')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-500 to-emerald-400 p-0.5 mx-auto">
                <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center">
                    <i class="ri-user-add-line text-2xl text-teal-400"></i>
                </div>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Daftar Akun Metrologi</h1>
            <p class="text-xs text-slate-400">Bergabunglah sebagai warga aktif Kota Metro untuk melaporkan dan memantau isu lingkungan.</p>
        </div>

        @if ($errors->any())
            <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Budi Santoso" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-teal-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="budi@warga.metro.id" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-teal-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="081234567890" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-teal-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Kata Sandi</label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-teal-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-2">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi" class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-teal-500">
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-white font-extrabold text-sm shadow-xl shadow-teal-500/25 transition-all">
                Daftar Sekarang
            </button>
        </form>

        <div class="text-center pt-4 border-t border-slate-800 text-xs text-slate-400">
            Sudah memiliki akun? <a href="{{ route('login') }}" class="text-teal-400 font-bold hover:underline">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
