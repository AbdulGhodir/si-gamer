<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerDashboardCatalogTest extends TestCase
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

    public function test_customer_dashboard_disambiguates_type_and_category_filters(): void
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertOk();
        $response->assertSee('Semua tipe');
        $response->assertSee('Semua kategori');
        $response->assertSee('Belum ada perangkat tersedia di katalog.');
    }

    public function test_customer_dashboard_lists_devices_with_their_categories(): void
    {
        $customer = $this->createCustomer();
        Device::create([
            'nama_device' => 'PC Reguler Test',
            'tipe' => 'PC',
            'kategori' => 'Reguler',
            'harga_per_jam' => 15000,
            'status' => 'tersedia',
        ]);
        Device::create([
            'nama_device' => 'PS5 VIP Test',
            'tipe' => 'PS5',
            'kategori' => 'VIP',
            'harga_per_jam' => 25000,
            'status' => 'bermain',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.dashboard'));

        $response->assertOk();
        $response->assertSee('PC Reguler Test');
        $response->assertSee('PS5 VIP Test');
        $response->assertSee('data-category="Reguler"', false);
        $response->assertSee('data-category="VIP"', false);
        $response->assertSee('Sedang digunakan');
        $response->assertSee('min-h-16', false);
        $response->assertDontSee('id="btn-category-VVIP"', false);
    }

    public function test_category_migration_backfills_existing_devices_for_customer_filters(): void
    {
        DB::table('devices')->insert([
            [
                'nama_device' => 'PC Lama',
                'tipe' => 'PC',
                'harga_per_jam' => 15000,
                'status' => 'tersedia',
                'kategori' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_device' => 'PS5 Lama',
                'tipe' => 'PS5',
                'harga_per_jam' => 25000,
                'status' => 'tersedia',
                'kategori' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $migration = require database_path('migrations/2026_11_27_090000_backfill_device_categories.php');
        $migration->up();

        $this->assertDatabaseHas('devices', [
            'nama_device' => 'PC Lama',
            'kategori' => 'Reguler',
        ]);
        $this->assertDatabaseHas('devices', [
            'nama_device' => 'PS5 Lama',
            'kategori' => 'VIP',
        ]);
    }

    public function test_admin_cannot_save_a_device_without_a_catalog_category(): void
    {
        $admin = User::create([
            'nama' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.devices.store'), [
            'nama_device' => 'PC Tanpa Kategori',
            'tipe' => 'PC',
            'harga_per_jam' => 15000,
            'status' => 'tersedia',
        ]);

        $response->assertSessionHasErrors('kategori');
        $this->assertDatabaseMissing('devices', ['nama_device' => 'PC Tanpa Kategori']);
    }

    private function createCustomer(): User
    {
        return User::create([
            'nama' => 'Customer Test',
            'email' => 'customer@example.test',
            'password' => 'password',
            'role' => 'customer',
        ]);
    }
}
