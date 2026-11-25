<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\Maintenance;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StaffMaintenanceReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Artisan::call('migrate:fresh', [
            '--database' => 'sqlite',
            '--force' => true,
        ]);
    }

    public function test_staff_report_creates_ticket_and_marks_device_for_repair(): void
    {
        $staff = User::create([
            'nama' => 'Staff Test',
            'email' => 'staff@example.test',
            'password' => 'password',
            'role' => 'staff',
            'status' => 'aktif',
        ]);
        $device = Device::create([
            'nama_device' => 'PC Test',
            'tipe' => 'PC',
            'harga_per_jam' => 10000,
        ]);

        $response = $this->actingAs($staff)->post(route('staff.maintenance.store'), [
            'id_device' => $device->id,
            'keterangan' => 'Port HDMI bermasalah',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('maintenances', [
            'id_device' => $device->id,
            'keterangan' => 'Port HDMI bermasalah',
            'status' => 'berlangsung',
        ]);
        $this->assertDatabaseHas('devices', [
            'id' => $device->id,
            'status' => 'perbaikan',
        ]);
    }

    public function test_staff_cannot_create_a_second_active_ticket_for_the_same_device(): void
    {
        $staff = User::create([
            'nama' => 'Staff Test',
            'email' => 'staff@example.test',
            'password' => 'password',
            'role' => 'staff',
            'status' => 'aktif',
        ]);
        $device = Device::create([
            'nama_device' => 'PC Test',
            'tipe' => 'PC',
            'harga_per_jam' => 10000,
        ]);
        Maintenance::create([
            'id_device' => $device->id,
            'keterangan' => 'Kerusakan sebelumnya',
            'waktu_mulai' => now(),
            'status' => 'berlangsung',
        ]);

        $response = $this->actingAs($staff)->from('/staff/dashboard')->post(route('staff.maintenance.store'), [
            'id_device' => $device->id,
            'keterangan' => 'Kerusakan lain',
        ]);

        $response->assertRedirect('/staff/dashboard');
        $response->assertSessionHasErrors('id_device');
        $this->assertDatabaseCount('maintenances', 1);
    }

    public function test_admin_maintenance_status_change_requires_a_confirmation_action(): void
    {
        $admin = User::create([
            'nama' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $device = Device::create([
            'nama_device' => 'PC Test',
            'tipe' => 'PC',
            'harga_per_jam' => 10000,
        ]);
        Maintenance::create([
            'id_device' => $device->id,
            'keterangan' => 'Port HDMI bermasalah',
            'waktu_mulai' => now(),
            'status' => 'berlangsung',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.maintenance'));

        $response->assertOk();
        $response->assertSee('Dalam Penanganan');
        $response->assertSee('confirm(');
        $response->assertSee('Klik status untuk mengubah setelah konfirmasi');
    }
}
