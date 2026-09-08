<nav class="mb-6 flex flex-wrap gap-3 border-b pb-4 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-blue-700">Rekap hari ini</a>
    <a href="{{ route('admin.employees') }}" class="text-blue-700">Pegawai</a>
    <a href="{{ route('admin.report') }}" class="text-blue-700">Laporan bulanan</a>
    <a href="{{ route('attendance.create') }}" class="ml-auto text-slate-600">Halaman absensi</a>
    <form action="{{ route('admin.logout') }}" method="POST">@csrf<button class="text-red-600">Keluar</button></form>
</nav>
