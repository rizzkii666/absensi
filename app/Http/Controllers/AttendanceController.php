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
}
