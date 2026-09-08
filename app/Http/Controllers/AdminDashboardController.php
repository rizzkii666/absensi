<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->string('date')->toString() ?: now('Asia/Jakarta')->toDateString();
        $attendances = Attendance::with('employee')->whereDate('attendance_date', $date)->latest('check_in_at')->paginate(20)->withQueryString();
        return view('admin.dashboard', compact('attendances', 'date'));
    }

    public function employees()
    {
        return view('admin.employees', ['employees' => Employee::latest()->paginate(20)]);
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $data = $request->validate(['nim' => ['required', 'string', 'max:50', 'unique:employees,nim'], 'name' => ['required', 'string', 'max:100']]);
        Employee::create($data);
        return back()->with('success', 'Pegawai magang berhasil ditambahkan.');
    }

    public function destroyEmployee(Employee $employee): RedirectResponse
    {
        $employee->delete();
        return back()->with('success', 'Pegawai dan riwayat absensinya berhasil dihapus.');
    }

    public function report(Request $request)
    {
        $month = $request->string('month')->toString() ?: now('Asia/Jakarta')->format('Y-m');
        $attendances = Attendance::with('employee')->where('attendance_date', 'like', $month.'%')->orderBy('attendance_date')->get();
        return view('admin.report', compact('attendances', 'month'));
    }
}
