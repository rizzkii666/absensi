@extends('layouts.app', ['title' => 'Kelola Pegawai'])
@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">@include('admin.partials.nav')
    <h1 class="mb-5 text-2xl font-bold">Kelola Pegawai Magang</h1>
    @if(session('success'))<p class="mb-4 rounded bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('success') }}</p>@endif
    @if($errors->any())<p class="mb-4 rounded bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</p>@endif
    <form method="POST" action="{{ route('admin.employees.store') }}" class="mb-6 grid gap-3 md:grid-cols-3">@csrf<input name="nim" required placeholder="NIM" class="rounded border p-3"><input name="name" required placeholder="Nama lengkap" class="rounded border p-3"><button class="rounded bg-blue-700 p-3 font-semibold text-white">Tambah pegawai</button></form>
    <table class="w-full text-sm"><thead class="border-b text-left"><tr><th class="p-2">NIM</th><th class="p-2">Nama</th><th></th></tr></thead><tbody>@foreach($employees as $employee)<tr class="border-b"><td class="p-2">{{ $employee->nim }}</td><td class="p-2">{{ $employee->name }}</td><td class="p-2 text-right"><form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Hapus pegawai ini dan semua absensinya?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form></td></tr>@endforeach</tbody></table><div class="mt-4">{{ $employees->links() }}</div>
</div>
@endsection
