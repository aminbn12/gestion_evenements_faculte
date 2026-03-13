<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // guests should be redirected to login
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_root_redirects_to_dashboard(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_index_alias_redirects_for_authenticated_user(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/index');
        $response->assertRedirect(route('dashboard'));
    }
}
