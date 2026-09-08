<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function create()
    {
        return view('attendance.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nim' => ['required', 'string', 'max:50'],
            'type' => ['required', Rule::in(['in', 'out'])],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $employee = Employee::where('nim', $data['nim'])->first();
        if (! $employee) {
            return back()->withInput()->withErrors(['nim' => 'NIM belum terdaftar. Hubungi admin.']);
        }

        $now = now('Asia/Jakarta');
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'attendance_date' => $now->toDateString(),
        ]);

        if ($data['type'] === 'in' && $attendance->check_in_at) {
            return back()->withInput()->withErrors(['type' => 'Absen masuk sudah tercatat hari ini.']);
        }
        if ($data['type'] === 'out' && $attendance->check_out_at) {
            return back()->withInput()->withErrors(['type' => 'Absen keluar sudah tercatat hari ini.']);
        }
        if ($data['type'] === 'out' && ! $attendance->check_in_at) {
            return back()->withInput()->withErrors(['type' => 'Silakan absen masuk terlebih dahulu.']);
        }

        $photoPath = $request->file('photo')->store("attendance/{$now->format('Y/m/d')}", 'public');
        if ($data['type'] === 'in') {
            $attendance->check_in_at = $now;
            $attendance->check_in_photo = $photoPath;
            $attendance->status = $now->format('H:i:s') > '08:00:00' ? 'Terlambat' : 'Hadir';
        } else {
            $attendance->check_out_at = $now;
            $attendance->check_out_photo = $photoPath;
        }

        try {
            $attendance->save();
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($photoPath);
            report($exception);
            return back()->withInput()->withErrors(['nim' => 'Absensi tidak dapat disimpan. Silakan coba lagi.']);
        }

        $label = $data['type'] === 'in' ? 'masuk' : 'keluar';

        return redirect()->route('attendance.create')->with('success', "Absensi {$label} {$employee->name} berhasil dicatat.");
    }

    public function storeQr(Request $request)
    {
        $data = $request->validate([
            'nim' => ['required', 'string', 'max:50'],
            'type' => ['nullable', Rule::in(['auto', 'in', 'out'])],
            'photo' => ['nullable', 'string'],
        ]);

        $nim = trim($data['nim']);
        if (str_starts_with($nim, '{')) {
            $json = json_decode($nim, true);
            if (isset($json['nim'])) {
                $nim = trim($json['nim']);
            }
        } elseif (str_ireplace('NIM:', '', $nim) !== $nim) {
            $nim = trim(str_ireplace('NIM:', '', $nim));
        }

        $employee = Employee::where('nim', $nim)->first();
        if (! $employee) {
            return response()->json([
                'success' => false,
                'message' => "NIM ({$nim}) belum terdaftar di sistem. Hubungi admin.",
            ], 404);
        }

        $now = now('Asia/Jakarta');
        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'attendance_date' => $now->toDateString(),
        ]);

        $type = $data['type'] ?? 'auto';
        if ($type === 'auto' || empty($type)) {
            $type = $attendance->check_in_at ? 'out' : 'in';
        }

        if ($type === 'in' && $attendance->check_in_at) {
            return response()->json([
                'success' => false,
                'message' => "{$employee->name} (NIM: {$employee->nim}) sudah melakukan Absen Masuk hari ini.",
            ], 422);
        }

        if ($type === 'out' && $attendance->check_out_at) {
            return response()->json([
                'success' => false,
                'message' => "{$employee->name} (NIM: {$employee->nim}) sudah melakukan Absen Keluar hari ini.",
            ], 422);
        }

        if ($type === 'out' && ! $attendance->check_in_at) {
            return response()->json([
                'success' => false,
                'message' => "{$employee->name} (NIM: {$employee->nim}) belum melakukan Absen Masuk hari ini.",
            ], 422);
        }

        $photoPath = null;
        if (! empty($data['photo']) && preg_match('/^data:image\/(\w+);base64,/', $data['photo'], $matches)) {
            $photoData = base64_decode(substr($data['photo'], strpos($data['photo'], ',') + 1));
            $extension = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
            $photoPath = 'attendance/' . $now->format('Y/m/d') . '/' . uniqid('qr_') . '.' . $extension;
            Storage::disk('public')->put($photoPath, $photoData);
        }

        if ($type === 'in') {
            $attendance->check_in_at = $now;
            if ($photoPath) {
                $attendance->check_in_photo = $photoPath;
            }
            $attendance->status = $now->format('H:i:s') > '08:00:00' ? 'Terlambat' : 'Hadir';
        } else {
            $attendance->check_out_at = $now;
            if ($photoPath) {
                $attendance->check_out_photo = $photoPath;
            }
        }

        try {
            $attendance->save();
        } catch (\Throwable $exception) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data absensi ke database.',
            ], 500);
        }

        $label = $type === 'in' ? 'Masuk' : 'Keluar';
        $timeStr = $now->format('H:i:s');

        return response()->json([
            'success' => true,
            'message' => "Absen {$label} Berhasil!",
            'employee_name' => $employee->name,
            'nim' => $employee->nim,
            'type' => $label,
            'time' => $timeStr,
            'status' => $attendance->status,
        ]);
    }
}
