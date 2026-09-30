<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_documents_returns_401(): void
    {
        $this
            ->withHeaders([
                'Origin' => 'http://localhost',
                'Referer' => 'http://localhost/',
            ])
            ->getJson('/api/documents')
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_documents(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->getJson('/api/documents')
            ->assertOk();
    }
}
