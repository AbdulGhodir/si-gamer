<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
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

    public function test_each_role_can_open_its_own_dashboard_but_not_other_role_dashboards(): void
    {
        $admin = $this->createUser('admin', 'admin@example.test');
        $staff = $this->createUser('staff', 'staff@example.test');
        $customer = $this->createUser('customer', 'customer@example.test');

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->get(route('staff.dashboard'))->assertForbidden();
        $this->get(route('customer.dashboard'))->assertForbidden();

        $this->actingAs($staff)->get(route('staff.dashboard'))->assertOk();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('customer.dashboard'))->assertForbidden();

        $this->actingAs($customer)->get(route('customer.dashboard'))->assertOk();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('staff.dashboard'))->assertForbidden();
    }

    public function test_role_checks_also_protect_write_routes(): void
    {
        $customer = $this->createUser('customer', 'customer@example.test');
        $this->actingAs($customer)
            ->post(route('admin.users.store'), [
                'nama' => 'Unauthorized Staff',
                'email' => 'unauthorized-staff@example.test',
                'password' => 'password',
            ])
            ->assertForbidden();

        $admin = $this->createUser('admin', 'admin@example.test');
        $this->actingAs($admin)
            ->post(route('staff.booking.store'), [])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'unauthorized-staff@example.test']);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_guest_is_redirected_to_login_for_role_protected_pages(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('staff.dashboard'))->assertRedirect(route('login'));
        $this->get(route('customer.dashboard'))->assertRedirect(route('login'));
    }

    private function createUser(string $role, string $email): User
    {
        return User::create([
            'nama' => ucfirst($role) . ' Test',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'aktif',
        ]);
    }
}
