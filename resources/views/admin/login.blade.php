@extends('layouts.app', ['title' => 'Login Admin'])
@section('content')
<form method="POST" action="{{ route('login') }}" class="mx-auto max-w-md rounded-2xl bg-white p-6 shadow-sm">@csrf
    <h1 class="mb-6 text-2xl font-bold">Login Admin</h1>
    @error('email')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror
    <label class="mb-1 block text-sm">Email</label><input type="email" name="email" value="{{ old('email') }}" required class="mb-4 w-full rounded border p-3">
    <label class="mb-1 block text-sm">Kata sandi</label><input type="password" name="password" required class="mb-4 w-full rounded border p-3">
    <label class="mb-4 block text-sm"><input type="checkbox" name="remember"> Ingat saya</label>
    <button class="w-full rounded bg-slate-800 py-3 font-semibold text-white">Masuk</button>
    <a href="{{ route('attendance.create') }}" class="mt-4 block text-center text-sm text-blue-700">Kembali ke absensi</a>
</form>
@endsection
