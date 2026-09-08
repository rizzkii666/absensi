@extends('layouts.app', ['title' => 'Absensi Magang'])

@section('content')
<div class="mx-auto max-w-xl rounded-2xl bg-white p-6 shadow-sm">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-800">Sistem Absensi Magang</h1>
        <p class="text-sm text-slate-500">Scan QR Code atau gunakan absensi manual foto selfie.</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-6 flex rounded-xl bg-slate-100 p-1 font-medium text-slate-600">
        <button type="button" id="tab-btn-qr" class="flex-1 rounded-lg py-2.5 text-center text-sm font-semibold transition-all bg-white text-blue-700 shadow-sm" onclick="switchTab('qr')">
            📷 Scan QR Code
        </button>
        <button type="button" id="tab-btn-manual" class="flex-1 rounded-lg py-2.5 text-center text-sm font-semibold transition-all text-slate-600 hover:text-slate-900" onclick="switchTab('manual')">
            ✍️ Manual & Foto
        </button>
    </div>

    <!-- TAB 1: SCAN QR CODE -->
    <div id="tab-content-qr" class="space-y-4">
        <!-- Mode Absensi -->
        <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3 text-sm border border-slate-200">
            <span class="font-medium text-slate-700">Jenis Absensi:</span>
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="qr_type" value="auto" checked class="form-radio text-blue-600">
                    <span class="ml-1.5 font-medium text-slate-700">Otomatis</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="qr_type" value="in" class="form-radio text-blue-600">
                    <span class="ml-1.5 text-slate-700">Masuk</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" name="qr_type" value="out" class="form-radio text-blue-600">
                    <span class="ml-1.5 text-slate-700">Keluar</span>
                </label>
            </div>
        </div>

        <!-- Notification / Scanner Info -->
        <div id="camera-notice" class="hidden rounded-xl bg-amber-50 p-3 text-xs text-amber-800 border border-amber-200">
            📌 <b>Akses Kamera Terblokir Browser?</b><br>
            Akses kamera langsung membutuhkan <b>http://localhost:8000</b> atau <b>HTTPS</b>. Jika kamera terblokir, Anda dapat memilih gambar QR Code secara manual menggunakan tombol di bawah ini.
        </div>

        <!-- Scanner Container -->
        <div class="relative overflow-hidden rounded-2xl bg-slate-900 border border-slate-200 min-h-[250px] flex items-center justify-center">
            <div id="qr-reader" class="w-full"></div>
            <div id="scanner-placeholder" class="flex flex-col items-center justify-center p-8 text-center text-slate-400">
                <svg class="mb-3 h-10 w-10 text-slate-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <p class="text-sm font-medium">Memulai Kamera Scanner...</p>
                <button type="button" onclick="startScanner()" class="mt-3 rounded-lg bg-slate-800 px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700">
                    🔄 Coba Buka Kamera Lagi
                </button>
            </div>
        </div>

        <!-- Opsi Alternatif: Upload Gambar QR Code -->
        <div class="rounded-xl border border-dashed border-slate-300 p-4 text-center bg-slate-50">
            <p class="mb-2 text-xs font-semibold text-slate-600">Opsi Alternatif: Unggah Foto/File QR Code</p>
            <input type="file" id="qr-file-input" accept="image/*" class="hidden" onchange="scanQrFromFile(this)">
            <button type="button" onclick="document.getElementById('qr-file-input').click()" class="rounded-lg bg-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-300 transition-colors">
                📁 Pilih Gambar / Foto QR Code
            </button>
        </div>

        <!-- Notification Result Overlay -->
        <div id="qr-result" class="hidden rounded-xl p-4 transition-all"></div>
    </div>

    <!-- TAB 2: MANUAL & FOTO -->
    <div id="tab-content-manual" class="hidden space-y-5">
        @if(session('success')) 
            <div class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700 border border-emerald-200">{{ session('success') }}</div> 
        @endif
        @if($errors->any()) 
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700 border border-red-200">
                <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div> 
        @endif

        <form method="POST" action="{{ route('attendance.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">NIM Pegawai</label>
                <input name="nim" value="{{ old('nim') }}" required maxlength="50" class="w-full rounded-xl border border-slate-300 p-3 text-sm focus:border-blue-500 focus:outline-none" placeholder="Masukkan NIM (Contoh: 2026001)">
            </div>
            <div>
                <span class="mb-1 block text-sm font-medium text-slate-700">Jenis Absensi</span>
                <div class="flex items-center gap-6 mt-1">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="type" value="in" checked class="form-radio text-blue-600"> 
                        <span class="ml-2 text-slate-700 font-medium">Masuk</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="type" value="out" class="form-radio text-blue-600"> 
                        <span class="ml-2 text-slate-700 font-medium">Keluar</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Foto Selfie Bukti</label>
                <video id="camera" class="hidden w-full rounded-xl bg-slate-900 mb-2" autoplay playsinline></video>
                <canvas id="canvas" class="hidden"></canvas>
                <img id="preview" class="hidden w-full rounded-xl mb-2" alt="Pratinjau foto">
                <input id="photo" name="photo" type="file" accept="image/*" capture="user" required class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
                <div class="flex gap-2 mt-2">
                    <button type="button" id="open-camera" class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">📷 Ambil Dari Kamera Web</button>
                    <button type="button" id="capture" class="hidden rounded-lg bg-slate-800 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-900">📸 Jepret Foto</button>
                </div>
                <p id="camera-message" class="mt-1 text-xs text-slate-400"></p>
            </div>
            <button class="w-full rounded-xl bg-blue-700 px-4 py-3 font-semibold text-white shadow-sm hover:bg-blue-800 transition-colors">Kirim Absensi Manual</button>
        </form>
    </div>

    <div class="mt-6 border-t border-slate-100 pt-4 text-center">
        <a href="{{ route('login') }}" class="text-xs font-semibold text-blue-700 hover:underline">🔐 Portal Login Admin</a>
    </div>
</div>

<!-- HTML5 QR Code Scanner Library -->
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let isProcessingScan = false;

function playAudioBeep(isSuccess = true) {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(isSuccess ? 880 : 220, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + (isSuccess ? 0.2 : 0.4));
    } catch (e) {}
}

function switchTab(tab) {
    const btnQr = document.getElementById('tab-btn-qr');
    const btnManual = document.getElementById('tab-btn-manual');
    const contentQr = document.getElementById('tab-content-qr');
    const contentManual = document.getElementById('tab-content-manual');

    if (tab === 'qr') {
        btnQr.className = "flex-1 rounded-lg py-2.5 text-center text-sm font-semibold transition-all bg-white text-blue-700 shadow-sm";
        btnManual.className = "flex-1 rounded-lg py-2.5 text-center text-sm font-semibold transition-all text-slate-600 hover:text-slate-900";
        contentQr.classList.remove('hidden');
        contentManual.classList.add('hidden');
        startScanner();
    } else {
        btnManual.className = "flex-1 rounded-lg py-2.5 text-center text-sm font-semibold transition-all bg-white text-blue-700 shadow-sm";
        btnQr.className = "flex-1 rounded-lg py-2.5 text-center text-sm font-semibold transition-all text-slate-600 hover:text-slate-900";
        contentManual.classList.remove('hidden');
        contentQr.classList.add('hidden');
        stopScanner();
    }
}

function startScanner() {
    if (html5QrCode && html5QrCode.isScanning) return;

    document.getElementById('scanner-placeholder').classList.remove('hidden');
    document.getElementById('camera-notice').classList.add('hidden');

    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("qr-reader");
    }

    const config = { fps: 10, qrbox: { width: 220, height: 220 } };

    html5QrCode.start(
        { facingMode: "environment" },
        config,
        onQrScanSuccess,
        onQrScanError
    ).then(() => {
        document.getElementById('scanner-placeholder').classList.add('hidden');
    }).catch(() => {
        html5QrCode.start(
            { facingMode: "user" },
            config,
            onQrScanSuccess,
            onQrScanError
        ).then(() => {
            document.getElementById('scanner-placeholder').classList.add('hidden');
        }).catch(err => {
            document.getElementById('scanner-placeholder').innerHTML = `
                <p class="text-xs text-red-400 px-4">Kamera tidak dapat diakses langsung oleh browser.<br>Gunakan opsi <b>Unggah Foto/File QR Code</b> di bawah ini.</p>
                <button type="button" onclick="document.getElementById('qr-file-input').click()" class="mt-3 rounded-lg bg-blue-600 px-3 py-1.5 text-xs text-white hover:bg-blue-700">
                    📁 Unggah Gambar QR Code
                </button>
            `;
            document.getElementById('camera-notice').classList.remove('hidden');
        });
    });
}

function stopScanner() {
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
        }).catch(() => {});
    }
}

async function scanQrFromFile(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];
    
    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("qr-reader");
    }

    try {
        const decodedText = await html5QrCode.scanFile(file, true);
        onQrScanSuccess(decodedText);
    } catch (err) {
        playAudioBeep(false);
        const resultBox = document.getElementById('qr-result');
        resultBox.className = "rounded-xl p-4 border bg-red-50 border-red-300 text-red-900 text-sm";
        resultBox.innerHTML = `❌ Gambar tidak mengandung QR Code yang valid atau terbaca.`;
        resultBox.classList.remove('hidden');
        setTimeout(() => resultBox.classList.add('hidden'), 3500);
    }
}

async function onQrScanSuccess(decodedText) {
    if (isProcessingScan) return;
    isProcessingScan = true;

    const typeRadios = document.getElementsByName('qr_type');
    let selectedType = 'auto';
    for (const r of typeRadios) {
        if (r.checked) { selectedType = r.value; break; }
    }

    const resultBox = document.getElementById('qr-result');
    resultBox.className = "rounded-xl p-4 border bg-blue-50 border-blue-200 text-blue-800 text-sm animate-pulse";
    resultBox.innerHTML = `⏳ Memproses data QR... (${decodedText})`;
    resultBox.classList.remove('hidden');

    try {
        const response = await fetch("{{ route('attendance.store_qr') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nim: decodedText,
                type: selectedType
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            playAudioBeep(true);
            resultBox.className = "rounded-xl p-4 border bg-emerald-50 border-emerald-300 text-emerald-900 text-sm shadow-sm";
            resultBox.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="text-2xl">✅</span>
                    <div>
                        <h4 class="font-bold text-base text-emerald-800">${data.message}</h4>
                        <p class="mt-1 font-medium">Pegawai: <span class="font-bold">${data.employee_name}</span> (${data.nim})</p>
                        <p class="text-xs text-emerald-700">Jenis: <b>${data.type}</b> | Waktu: <b>${data.time}</b> | Status: <b>${data.status}</b></p>
                    </div>
                </div>
            `;
        } else {
            playAudioBeep(false);
            resultBox.className = "rounded-xl p-4 border bg-red-50 border-red-300 text-red-900 text-sm shadow-sm";
            resultBox.innerHTML = `
                <div class="flex items-start gap-3">
                    <span class="text-2xl">❌</span>
                    <div>
                        <h4 class="font-bold text-base text-red-800">Absensi Gagal</h4>
                        <p class="mt-1 font-medium">${data.message || 'Terjadi kesalahan saat memproses QR Code.'}</p>
                    </div>
                </div>
            `;
        }
    } catch (err) {
        playAudioBeep(false);
        resultBox.className = "rounded-xl p-4 border bg-red-50 border-red-300 text-red-900 text-sm";
        resultBox.innerHTML = `❌ Network Error: Gagal menghubungi server.`;
    }

    setTimeout(() => {
        isProcessingScan = false;
        resultBox.classList.add('hidden');
    }, 3500);
}

function onQrScanError(error) {}

document.addEventListener('DOMContentLoaded', () => {
    startScanner();
});

// Manual Camera Setup
const input = document.getElementById('photo'), video = document.getElementById('camera'), canvas = document.getElementById('canvas'), preview = document.getElementById('preview');
let manualStream;
document.getElementById('open-camera').onclick = async () => {
  try {
    manualStream = await navigator.mediaDevices.getUserMedia({video: {facingMode: 'user'}, audio: false});
    video.srcObject = manualStream; video.classList.remove('hidden'); document.getElementById('capture').classList.remove('hidden');
  } catch {
    document.getElementById('camera-message').textContent = 'Kamera tidak dapat diakses langsung oleh browser. Gunakan tombol pilih file foto di atas.';
  }
};
document.getElementById('capture').onclick = () => {
  canvas.width = video.videoWidth; canvas.height = video.videoHeight; canvas.getContext('2d').drawImage(video, 0, 0); canvas.toBlob(blob => {
    const file = new File([blob], 'selfie.jpg', {type: 'image/jpeg'}); const transfer = new DataTransfer(); transfer.items.add(file); input.files = transfer.files;
    preview.src = URL.createObjectURL(file); preview.classList.remove('hidden'); video.classList.add('hidden'); manualStream?.getTracks().forEach(t => t.stop());
  }, 'image/jpeg', .85);
};
</script>
@endsection
