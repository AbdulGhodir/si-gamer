<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffStatusTest extends TestCase
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

    public function test_admin_can_deactivate_a_staff_account(): void
    {
        $admin = $this->createUser('admin', 'admin@example.test');
        $staff = $this->createUser('staff', 'staff@example.test');

        $response = $this->actingAs($admin)->put(route('admin.users.update', $staff->id), [
            'nama' => $staff->nama,
            'email' => $staff->email,
            'status' => 'nonaktif',
        ]);

        $response->assertRedirect(route('admin.users'));
        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'status' => 'nonaktif',
        ]);
    }

    public function test_inactive_staff_cannot_log_in(): void
    {
        $staff = $this->createUser('staff', 'staff@example.test', 'nonaktif');

        $response = $this->post(route('login.post'), [
            'email' => $staff->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_deactivated_staff_session_is_logged_out_on_next_request(): void
    {
        $staff = $this->createUser('staff', 'staff@example.test');
        $this->actingAs($staff);

        $staff->status = 'nonaktif';
        $staff->save();

        $response = $this->get(route('staff.dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    private function createUser(string $role, string $email, string $status = 'aktif'): User
    {
        return User::create([
            'nama' => ucfirst($role) . ' Test',
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => $status,
        ]);
    }
}
