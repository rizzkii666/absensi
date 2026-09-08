@extends('layouts.app', ['title' => 'Laporan Bulanan'])
@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">@include('admin.partials.nav')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3"><h1 class="text-2xl font-bold">Laporan Bulanan</h1><form><input type="month" name="month" value="{{ $month }}" class="rounded border p-2"><button class="rounded bg-slate-800 px-3 py-2 text-white">Filter</button></form></div>
    <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="border-b text-left"><tr><th class="p-2">Tanggal</th><th class="p-2">Nama</th><th class="p-2">NIM</th><th class="p-2">Masuk</th><th class="p-2">Keluar</th><th class="p-2">Status</th></tr></thead><tbody>@forelse($attendances as $attendance)<tr class="border-b"><td class="p-2">{{ $attendance->attendance_date->format('d-m-Y') }}</td><td class="p-2">{{ $attendance->employee->name }}</td><td class="p-2">{{ $attendance->employee->nim }}</td><td class="p-2">{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td><td class="p-2">{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td><td class="p-2">{{ $attendance->status }}</td></tr>@empty<tr><td colspan="6" class="p-4 text-center text-slate-500">Tidak ada data.</td></tr>@endforelse</tbody></table></div>
</div>
@endsection
