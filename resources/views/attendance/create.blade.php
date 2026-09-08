@extends('layouts.app', ['title' => 'Absensi Magang'])

@section('content')
<div class="mx-auto max-w-lg rounded-2xl bg-white p-6 shadow-sm">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold">Absensi Magang</h1>
        <p class="text-sm text-slate-500">Silakan isi NIM dan ambil foto sebagai bukti kehadiran.</p>
    </div>
    @if(session('success')) <div class="mb-4 rounded bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="mb-4 rounded bg-red-50 p-3 text-sm text-red-700"><ul class="list-disc pl-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
    <form method="POST" action="{{ route('attendance.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div><label class="mb-1 block text-sm font-medium">NIM</label><input name="nim" value="{{ old('nim') }}" required maxlength="50" class="w-full rounded border p-3" placeholder="Masukkan NIM"></div>
        <div>
            <span class="mb-1 block text-sm font-medium">Jenis absensi</span>
            <label class="mr-4"><input type="radio" name="type" value="in" checked> Masuk</label>
            <label><input type="radio" name="type" value="out"> Keluar</label>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium">Foto selfie</label>
            <video id="camera" class="hidden w-full rounded bg-slate-900" autoplay playsinline></video>
            <canvas id="canvas" class="hidden"></canvas>
            <img id="preview" class="hidden w-full rounded" alt="Pratinjau foto">
            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" capture="user" required class="mt-2 block w-full text-sm">
            <button type="button" id="open-camera" class="mt-2 rounded bg-slate-200 px-3 py-2 text-sm">Gunakan kamera</button>
            <button type="button" id="capture" class="hidden mt-2 rounded bg-slate-800 px-3 py-2 text-sm text-white">Ambil foto</button>
            <p id="camera-message" class="mt-1 text-xs text-slate-500"></p>
        </div>
        <button class="w-full rounded bg-blue-700 px-4 py-3 font-semibold text-white hover:bg-blue-800">Kirim absensi</button>
    </form>
    <p class="mt-5 text-center text-sm"><a href="{{ route('login') }}" class="text-blue-700 hover:underline">Masuk admin</a></p>
</div>
<script>
const input = document.getElementById('photo'), video = document.getElementById('camera'), canvas = document.getElementById('canvas'), preview = document.getElementById('preview');
let stream;
document.getElementById('open-camera').onclick = async () => {
  try { stream = await navigator.mediaDevices.getUserMedia({video: {facingMode: 'user'}, audio: false}); video.srcObject = stream; video.classList.remove('hidden'); document.getElementById('capture').classList.remove('hidden'); }
  catch { document.getElementById('camera-message').textContent = 'Kamera tidak dapat diakses. Unggah foto secara manual.'; }
};
document.getElementById('capture').onclick = () => {
  canvas.width = video.videoWidth; canvas.height = video.videoHeight; canvas.getContext('2d').drawImage(video, 0, 0); canvas.toBlob(blob => {
    const file = new File([blob], 'selfie.jpg', {type: 'image/jpeg'}); const transfer = new DataTransfer(); transfer.items.add(file); input.files = transfer.files;
    preview.src = URL.createObjectURL(file); preview.classList.remove('hidden'); video.classList.add('hidden'); stream?.getTracks().forEach(t => t.stop());
  }, 'image/jpeg', .85);
};
</script>
@endsection
