@extends('layouts.app')

@section('title', 'Masuk Akun Admin - Metrologi Kota Metro')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-8 shadow-sm space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-teal-600 to-emerald-500 p-0.5 mx-auto shadow-md">
                <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center">
                    <i class="ri-shield-keyhole-line text-2xl text-teal-600"></i>
                </div>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900">Masuk Portal Admin</h1>
            <p class="text-xs text-slate-500">Masuk untuk memverifikasi pengaduan dan mengelola berita perkotaan.</p>
        </div>

        @if ($errors->any())
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Alamat Email Admin</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@metrologi.go.id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Kata Sandi</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 focus:outline-none focus:border-teal-600 focus:bg-white transition-colors">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-50 border-slate-200 text-teal-600 focus:ring-0">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white font-extrabold text-sm shadow-md shadow-teal-600/20 transition-all">
                Masuk ke Portal Admin
            </button>
        </form>

        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-[11px] text-slate-600 space-y-1">
            <span class="font-bold text-slate-800 block"><i class="ri-information-line text-teal-600"></i> Akun Demo Admin:</span>
            <p>Email: <code class="text-teal-700 font-bold">admin@metrologi.go.id</code> | Password: <code class="text-teal-700 font-bold">password123</code></p>
        </div>
    </div>
</div>
@endsection
