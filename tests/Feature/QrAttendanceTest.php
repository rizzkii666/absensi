<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_attendance_via_qr_scan(): void
    {
        $employee = Employee::create([
            'nim' => '2026001',
            'name' => 'Budi Santoso',
        ]);

        $response = $this->postJson(route('attendance.store_qr'), [
            'nim' => '2026001',
            'type' => 'in',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'employee_name' => 'Budi Santoso',
                'nim' => '2026001',
                'type' => 'Masuk',
            ]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
        ]);

    }

    public function test_rejects_unregistered_nim_qr(): void
    {
        $response = $this->postJson(route('attendance.store_qr'), [
            'nim' => '9999999',
            'type' => 'in',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
