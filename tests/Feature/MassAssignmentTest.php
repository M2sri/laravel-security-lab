<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MassAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_vulnerable_endpoint_allows_role_manipulation(): void
    {
        $response = $this->postJson('/labs/mass-assignment/vulnerable', [
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'role' => 'admin',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('role', 'admin');

        $this->assertDatabaseHas('profiles', [
            'email' => 'demo@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_vulnerable_endpoint_allows_is_verified_manipulation(): void
    {
        $response = $this->postJson('/labs/mass-assignment/vulnerable', [
            'name' => 'Verified User',
            'email' => 'verified@example.com',
            'is_verified' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('is_verified', true);

        $this->assertDatabaseHas('profiles', [
            'email' => 'verified@example.com',
            'is_verified' => true,
        ]);
    }

    public function test_secure_endpoint_ignores_role(): void
    {
        $response = $this->postJson('/labs/mass-assignment/secure', [
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'role' => 'admin',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('role', 'user');

        $this->assertDatabaseHas('profiles', [
            'email' => 'regular@example.com',
            'role' => 'user',
        ]);
    }

    public function test_secure_endpoint_ignores_is_verified(): void
    {
        $response = $this->postJson('/labs/mass-assignment/secure', [
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'is_verified' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('is_verified', false);

        $this->assertDatabaseHas('profiles', [
            'email' => 'unverified@example.com',
            'is_verified' => false,
        ]);
    }

    public function test_secure_endpoint_stores_only_validated_fields(): void
    {
        $response = $this->postJson('/labs/mass-assignment/secure', [
            'name' => 'Allowed User',
            'email' => 'allowed@example.com',
            'role' => 'admin',
            'is_verified' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name', 'Allowed User')
            ->assertJsonPath('email', 'allowed@example.com')
            ->assertJsonPath('role', 'user')
            ->assertJsonPath('is_verified', false);

        $this->assertDatabaseHas('profiles', [
            'name' => 'Allowed User',
            'email' => 'allowed@example.com',
            'role' => 'user',
            'is_verified' => false,
        ]);
    }
}
