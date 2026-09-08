@extends('layouts.app', ['title' => 'Rekap Absensi'])
@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">
    @include('admin.partials.nav')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3"><h1 class="text-2xl font-bold">Rekap Absensi</h1><form><input type="date" name="date" value="{{ $date }}" class="rounded border p-2"><button class="rounded bg-slate-800 px-3 py-2 text-white">Tampilkan</button></form></div>
    <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="border-b text-left"><tr><th class="p-2">Nama / NIM</th><th class="p-2">Masuk</th><th class="p-2">Keluar</th><th class="p-2">Status</th><th class="p-2">Foto</th></tr></thead><tbody>
    @forelse($attendances as $attendance)<tr class="border-b"><td class="p-2"><strong>{{ $attendance->employee->name }}</strong><br><span class="text-slate-500">{{ $attendance->employee->nim }}</span></td><td class="p-2">{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td><td class="p-2">{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td><td class="p-2">{{ $attendance->status }}</td><td class="p-2">@if($attendance->check_in_photo)<a target="_blank" class="text-blue-700" href="{{ Storage::url($attendance->check_in_photo) }}">Masuk</a>@endif @if($attendance->check_out_photo)<a target="_blank" class="text-blue-700" href="{{ Storage::url($attendance->check_out_photo) }}">Keluar</a>@endif</td></tr>@empty<tr><td class="p-4 text-center text-slate-500" colspan="5">Belum ada absensi.</td></tr>@endforelse
    </tbody></table></div><div class="mt-4">{{ $attendances->links() }}</div>
</div>
@endsection
