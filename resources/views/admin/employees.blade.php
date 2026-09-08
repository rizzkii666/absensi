@extends('layouts.app', ['title' => 'Kelola Pegawai'])

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">
    @include('admin.partials.nav')
    
    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Kelola Pegawai Magang</h1>
            <p class="text-sm text-slate-500">Tambah pegawai baru dan cetak Kartu QR Code unik untuk absensi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700 border border-emerald-200">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 p-4 text-sm text-red-700 border border-red-200">{{ $errors->first() }}</div>
    @endif

    <!-- Form Tambah Pegawai -->
    <form method="POST" action="{{ route('admin.employees.store') }}" class="mb-6 grid gap-3 md:grid-cols-3">
        @csrf
        <input name="nim" required placeholder="NIM / ID Pegawai" class="rounded-xl border border-slate-300 p-3 text-sm focus:border-blue-500 focus:outline-none">
        <input name="name" required placeholder="Nama Lengkap Pegawai" class="rounded-xl border border-slate-300 p-3 text-sm focus:border-blue-500 focus:outline-none">
        <button class="rounded-xl bg-blue-700 p-3 text-sm font-semibold text-white hover:bg-blue-800 transition-colors">
            + Tambah Pegawai
        </button>
    </form>

    <!-- Tabel Data Pegawai -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="border-b border-slate-200 text-slate-500 font-medium">
                    <th class="p-3">NIM</th>
                    <th class="p-3">Nama Pegawai</th>
                    <th class="p-3 text-center">Kartu QR Code</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($employees as $employee)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-3 font-mono font-semibold text-slate-700">{{ $employee->nim }}</td>
                        <td class="p-3 font-medium text-slate-900">{{ $employee->name }}</td>
                        <td class="p-3 text-center">
                            <button type="button" 
                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-50 hover:text-blue-800 transition-colors"
                                onclick="showQrModal('{{ $employee->nim }}', '{{ addslashes($employee->name) }}')">
                                📇 Lihat / Unduh QR
                            </button>
                        </td>
                        <td class="p-3 text-right">
                            <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Hapus pegawai {{ addslashes($employee->name) }} dan semua riwayat absensinya?')">
                                @csrf 
                                @method('DELETE')
                                <button class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-400">Belum ada pegawai yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $employees->links() }}</div>
</div>

<!-- MODAL POPUP QR CODE CARD -->
<div id="qr-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
    <div class="relative w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl transition-all">
        <!-- Close Button -->
        <button type="button" onclick="closeQrModal()" class="absolute right-4 top-4 rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
            ✕
        </button>

        <h3 class="mb-4 text-center text-lg font-bold text-slate-800">Kartu QR Absensi</h3>

        <!-- Printable Card Container -->
        <div id="qr-card-print" class="flex flex-col items-center justify-center rounded-2xl border border-slate-200 bg-gradient-to-b from-slate-50 to-white p-6 text-center shadow-sm">
            <div class="mb-3 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-700">
                KARTU ABSENSI MAGANG
            </div>
            
            <div id="qrcode-box" class="my-3 flex justify-center rounded-xl bg-white p-4 shadow-inner border border-slate-100"></div>

            <h4 id="modal-employee-name" class="mt-2 text-lg font-bold text-slate-900"></h4>
            <p id="modal-employee-nim" class="font-mono text-sm font-semibold text-slate-500"></p>
        </div>

        <!-- Action Buttons -->
        <div class="mt-5 flex gap-2">
            <button type="button" onclick="downloadQrCode()" class="flex-1 rounded-xl bg-blue-700 py-2.5 text-xs font-semibold text-white hover:bg-blue-800 transition-colors">
                📥 Unduh Gambar QR
            </button>
            <button type="button" onclick="printQrCard()" class="flex-1 rounded-xl bg-slate-800 py-2.5 text-xs font-semibold text-white hover:bg-slate-900 transition-colors">
                🖨️ Cetak Kartu
            </button>
        </div>
    </div>
</div>

<!-- QRCode JS Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
let currentNim = '';
let currentName = '';

function showQrModal(nim, name) {
    currentNim = nim;
    currentName = name;

    document.getElementById('modal-employee-name').textContent = name;
    document.getElementById('modal-employee-nim').textContent = 'NIM: ' + nim;

    const qrContainer = document.getElementById('qrcode-box');
    qrContainer.innerHTML = '';

    new QRCode(qrContainer, {
        text: nim,
        width: 180,
        height: 180,
        colorDark: "#0f172a",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    document.getElementById('qr-modal').classList.remove('hidden');
}

function closeQrModal() {
    document.getElementById('qr-modal').classList.add('hidden');
}

function downloadQrCode() {
    const img = document.querySelector('#qrcode-box img');
    const canvas = document.querySelector('#qrcode-box canvas');
    
    let imageSrc = '';
    if (img && img.src) {
        imageSrc = img.src;
    } else if (canvas) {
        imageSrc = canvas.toDataURL("image/png");
    }

    if (!imageSrc) return;

    const link = document.createElement('a');
    link.href = imageSrc;
    link.download = `QR_Absensi_${currentNim}_${currentName.replace(/\s+/g, '_')}.png`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printQrCard() {
    const cardContent = document.getElementById('qr-card-print').outerHTML;
    const printWindow = window.open('', '', 'width=600,height=600');
    printWindow.document.write(`
        <html>
            <head>
                <title>Cetak Kartu QR Absensi - ${currentName}</title>
                <script src="https://cdn.tailwindcss.com"><\/script>
                <style>
                    body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #fff; }
                </style>
            </head>
            <body>
                <div style="width: 320px;">
                    ${cardContent}
                </div>
                <script>
                    setTimeout(() => { window.print(); window.close(); }, 500);
                <\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endsection
