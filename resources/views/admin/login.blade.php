@extends('layouts.app', ['title' => 'Login Admin'])

@section('content')
<div class="mx-auto max-w-md rounded-2xl bg-white p-6 shadow-sm border border-slate-100">
    <h1 class="mb-2 text-2xl font-bold text-slate-800">Login Portal Admin</h1>
    <p class="mb-6 text-sm text-slate-500">Masuk untuk mengelola data pegawai, laporan absensi, dan kartu QR Code.</p>

    <!-- Info Akun Default Admin -->
    <div class="mb-5 rounded-xl bg-blue-50 p-3.5 text-xs text-blue-900 border border-blue-200 space-y-1">
        <p class="font-bold text-blue-800">🔑 Akun Admin Default (.env):</p>
        <p>Email: <code class="bg-blue-100 px-1.5 py-0.5 rounded font-mono text-blue-900 font-bold">admin@absensi.local</code></p>
        <p>Kata Sandi: <code class="bg-blue-100 px-1.5 py-0.5 rounded font-mono text-blue-900 font-bold">GantiSekarang!2026</code></p>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 p-3 text-xs text-red-700 border border-red-200 font-medium">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Email Admin</label>
            <input type="email" name="email" value="{{ old('email', 'admin@absensi.local') }}" required class="w-full rounded-xl border border-slate-300 p-3 text-sm focus:border-blue-500 focus:outline-none" placeholder="admin@absensi.local">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Kata Sandi</label>
            <input type="password" name="password" required class="w-full rounded-xl border border-slate-300 p-3 text-sm focus:border-blue-500 focus:outline-none" placeholder="Masukkan kata sandi">
        </div>
        <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center cursor-pointer text-slate-600">
                <input type="checkbox" name="remember" class="form-checkbox text-blue-600 rounded"> 
                <span class="ml-2 text-xs">Ingat Sesi Saya</span>
            </label>
        </div>
        <button class="w-full rounded-xl bg-slate-800 py-3 text-sm font-semibold text-white hover:bg-slate-900 transition-colors shadow-sm">
            Masuk ke Portal Admin
        </button>
        <a href="{{ route('attendance.create') }}" class="block text-center text-xs font-semibold text-blue-700 hover:underline">
            ← Kembali ke Halaman Absensi
        </a>
    </form>
</div>
@endsection
