<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_profile_uses_a_person_icon_without_a_photo_upload_control(): void
    {
        $customer = User::create([
            'nama' => 'Customer Test',
            'email' => 'customer@example.test',
            'password' => 'password',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.profile'));

        $response->assertOk();
        $response->assertSee('ph ph-user', false);
        $response->assertDontSee('name="foto_profil"', false);
        $response->assertDontSee('Ganti Foto Profil');
    }

    public function test_profile_update_does_not_store_an_uploaded_photo(): void
    {
        Storage::fake('public');
        $customer = User::create([
            'nama' => 'Customer Test',
            'email' => 'customer@example.test',
            'password' => 'password',
            'role' => 'customer',
            'foto_profil' => 'existing/avatar.png',
        ]);

        $response = $this->actingAs($customer)->put(route('customer.profile.update'), [
            'nama' => 'Updated Customer',
            'email' => 'customer@example.test',
            'foto_profil' => UploadedFile::fake()->create('new-avatar.png', 10, 'image/png'),
        ]);

        $response->assertRedirect(route('customer.profile'));
        $this->assertSame('Updated Customer', $customer->fresh()->nama);
        $this->assertSame('existing/avatar.png', $customer->fresh()->foto_profil);
        Storage::disk('public')->assertDirectoryEmpty('foto-profil');
    }
}
