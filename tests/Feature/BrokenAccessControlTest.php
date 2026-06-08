<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrokenAccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/labs/broken-access-control/vulnerable/admin-report');

        $response->assertRedirect('/login');
    }

    public function test_normal_user_can_access_vulnerable_admin_report(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this
            ->actingAs($user)
            ->getJson('/labs/broken-access-control/vulnerable/admin-report');

        $response
            ->assertOk()
            ->assertJsonPath('access_pattern', 'Authenticated, but not authorized by role.');
    }

    public function test_normal_user_cannot_access_secure_admin_report(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this
            ->actingAs($user)
            ->getJson('/labs/broken-access-control/secure/admin-report');

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_secure_admin_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this
            ->actingAs($admin)
            ->getJson('/labs/broken-access-control/secure/admin-report');

        $response
            ->assertOk()
            ->assertJsonPath('access_pattern', 'Authorized admin user.');
    }
}
