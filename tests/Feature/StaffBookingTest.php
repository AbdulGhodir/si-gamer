<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Device;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StaffBookingTest extends TestCase
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

    public function test_staff_can_create_a_confirmed_booking_for_a_customer(): void
    {
        $staff = $this->createUser('staff', 'staff@example.test');
        $customer = $this->createUser('customer', 'customer@example.test');
        $device = $this->createDevice();
        $start = now()->addHours(2)->startOfMinute();

        $response = $this->actingAs($staff)->post(route('staff.booking.store'), [
            'id_user' => $customer->id,
            'id_device' => $device->id,
            'waktu_mulai' => $start->format('Y-m-d\TH:i'),
            'durasi' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id_user' => $customer->id,
            'id_device' => $device->id,
            'id_staff' => $staff->id,
            'status' => 'menunggu',
            'status_konfirmasi' => 'dikonfirmasi',
            'total_harga' => 20000,
        ]);
        $this->assertDatabaseHas('payments', [
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'menunggu',
            'jumlah' => 20000,
        ]);
    }

    public function test_staff_dashboard_links_to_booking_management(): void
    {
        $staff = $this->createUser('staff', 'staff@example.test');
        $customer = $this->createUser('customer', 'customer@example.test');
        $device = $this->createDevice();
        $booking = $this->createBooking($customer, $device, now()->addHours(2), 1);

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertOk();
        $response->assertSee('Agenda booking terdekat');
        $response->assertSee($booking->kode_booking);
        $response->assertSee($customer->nama);
        $response->assertDontSee('Akses cepat');
    }

    public function test_staff_features_open_on_separate_pages_with_the_active_navigation_highlighted(): void
    {
        $staff = $this->createUser('staff', 'staff@example.test');
        $pages = [
            ['staff.bookings', 'Daftar booking'],
            ['staff.sessions', 'Sesi aktif'],
            ['staff.booking.walkin', 'Booking walk-in'],
            ['staff.devices', 'Status perangkat'],
            ['staff.maintenance', 'Riwayat maintenance'],
        ];

        foreach ($pages as [$routeName, $heading]) {
            $response = $this->actingAs($staff)->get(route($routeName));

            $response->assertOk();
            $response->assertSee($heading);
            $response->assertSee('bg-purple-600/15 text-purple-400 font-semibold', false);
        }
    }

    public function test_admin_booking_page_only_displays_monitoring_controls(): void
    {
        $admin = $this->createUser('admin', 'admin@example.test');
        $customer = $this->createUser('customer', 'customer@example.test');
        $device = $this->createDevice();
        $booking = $this->createBooking($customer, $device, now()->addHours(2), 1);
        Payment::create([
            'id_booking' => $booking->id,
            'kode_transaksi' => 'TR-TEST-1',
            'metode_pembayaran' => 'qris',
            'jumlah' => $booking->total_harga,
            'status_pembayaran' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bookings'));

        $response->assertOk();
        $response->assertSee('Pantau jadwal pemesanan perangkat');
        $response->assertSee('Belum bayar');
        $response->assertSee('Menunggu');
        $response->assertSee('Informasi booking dan pembayaran');
        $response->assertDontSee('confirm(');

        $this->patch("/admin/bookings/{$booking->id}/toggle-status")->assertNotFound();
        $this->patch("/admin/bookings/{$booking->id}/toggle-payment")->assertNotFound();
    }

    public function test_report_download_is_on_reports_page_not_admin_dashboard(): void
    {
        $admin = $this->createUser('admin', 'admin@example.test');

        $dashboardResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertDontSee('Unduh laporan');

        $reportResponse = $this->get(route('admin.reports'));
        $reportResponse->assertOk();
        $reportResponse->assertSee('Download Laporan (.CSV)');
        $reportResponse->assertSee('@media print', false);
        $reportResponse->assertSee('report-print-controls');
    }

    public function test_admin_dashboard_chart_shows_device_status_counts(): void
    {
        $admin = $this->createUser('admin', 'admin@example.test');
        $this->createDevice();
        $this->createDevice()->update(['status' => 'tersedia']);
        $this->createDevice()->update(['status' => 'bermain']);
        $this->createDevice()->update(['status' => 'dipesan']);
        $this->createDevice()->update(['status' => 'perbaikan']);
        $this->createDevice()->update(['status' => 'offline']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Distribusi status perangkat');
        $response->assertSee('2 <span class="text-gray-600">(33%)</span>', false);
        $response->assertSee('Tersedia');
        $response->assertSee('Perbaikan');
        $response->assertSee('Offline');
        $response->assertDontSee('[ Area Grafik Batang Perangkat ]');
    }

    public function test_staff_cannot_create_a_booking_that_overlaps_an_existing_booking(): void
    {
        $staff = $this->createUser('staff', 'staff@example.test');
        $customer = $this->createUser('customer', 'customer@example.test');
        $device = $this->createDevice();
        $start = now()->addHours(2)->startOfMinute();
        $this->createBooking($customer, $device, $start, 2);

        $response = $this->actingAs($staff)->from('/staff/dashboard')->post(route('staff.booking.store'), [
            'id_user' => $customer->id,
            'id_device' => $device->id,
            'waktu_mulai' => $start->copy()->addHour()->format('Y-m-d\TH:i'),
            'durasi' => 1,
        ]);

        $response->assertRedirect('/staff/dashboard');
        $response->assertSessionHasErrors('waktu_mulai');
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_adjacent_bookings_are_allowed(): void
    {
        $staff = $this->createUser('staff', 'staff@example.test');
        $customer = $this->createUser('customer', 'customer@example.test');
        $device = $this->createDevice();
        $start = now()->addHours(2)->startOfMinute();
        $this->createBooking($customer, $device, $start, 1);

        $response = $this->actingAs($staff)->post(route('staff.booking.store'), [
            'id_user' => $customer->id,
            'id_device' => $device->id,
            'waktu_mulai' => $start->copy()->addHour()->format('Y-m-d\TH:i'),
            'durasi' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_online_booking_rejects_an_overlapping_schedule(): void
    {
        $customer = $this->createUser('customer', 'customer@example.test');
        $device = $this->createDevice();
        $start = now()->addHours(2)->startOfMinute();
        $this->createBooking($customer, $device, $start, 2);

        $response = $this->actingAs($customer)->from('/customer/booking')->post(route('customer.booking.store'), [
            'device_id' => $device->id,
            'tanggal' => $start->copy()->addHour()->toDateString(),
            'jam' => $start->copy()->addHour()->format('H:i'),
            'durasi' => 1,
        ]);

        $response->assertRedirect('/customer/booking');
        $response->assertSessionHasErrors('jam');
        $this->assertDatabaseCount('bookings', 1);
    }

    private function createUser(string $role, string $email): User
    {
        return User::create([
            'nama' => ucfirst($role) . ' Test',
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'status' => 'aktif',
        ]);
    }

    private function createDevice(): Device
    {
        return Device::create([
            'nama_device' => 'PC Test',
            'tipe' => 'PC',
            'harga_per_jam' => 10000,
        ]);
    }

    private function createBooking(User $customer, Device $device, $start, int $duration): Booking
    {
        return Booking::create([
            'kode_booking' => 'TEST-' . uniqid(),
            'id_user' => $customer->id,
            'id_device' => $device->id,
            'waktu_mulai' => $start,
            'waktu_selesai' => $start->copy()->addHours($duration),
            'durasi' => $duration,
            'subtotal' => $device->harga_per_jam * $duration,
            'diskon' => 0,
            'total_harga' => $device->harga_per_jam * $duration,
            'status' => 'menunggu',
            'status_konfirmasi' => 'dikonfirmasi',
            'jenis_booking' => 'langsung',
        ]);
    }
}
