<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerProfileTest extends TestCase
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

    public function test_customer_can_update_profile_and_password(): void
    {
        $customer = User::create([
            'nama' => 'Customer Test',
            'email' => 'customer@example.test',
            'password' => 'old-password',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->put(route('customer.profile.update'), [
            'nama' => 'Customer Updated',
            'email' => 'customer@example.test',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('customer.profile'));
        $this->assertSame('Customer Updated', $customer->fresh()->nama);
        $this->assertTrue(Hash::check('new-password', $customer->fresh()->password));
    }
}
