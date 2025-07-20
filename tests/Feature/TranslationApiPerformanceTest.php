<?php

namespace Tests\Feature;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TranslationApiPerformanceTest extends TestCase
{

    use RefreshDatabase;

    public function getUser()
    {
        return  User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Testing',
                'password' => Hash::make('test12345')
            ]);
    }

    public function test_index_returns_translations_within_500ms_for_authenticated_user()
    {
        // Create a user and authenticate with Sanctum
        $user = $this->getUser();
        // Create sample translations
        Translation::factory()->count(5)->create();

        // Start performance timer
        $start = microtime(true);

        // Hit the API
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/translations');

        // End performance timer
        $end = microtime(true);
        $durationMs = ($end - $start) * 1000;

        // Log duration to terminal
        fwrite(STDOUT, "\nResponse time: {$durationMs}ms\n");

        // Assert success and performance
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['*' => ['id', 'key', 'locale', 'value', 'tag']]
            ]);

        $this->assertLessThanOrEqual(500, $durationMs, 'API response exceeded 500ms');
    }

    public function test_translation_creation_performance_under_500ms()
    {
        $user = $this->getUser();

        $data = [
            'key' => 'fast_key',
            'locale' => 'en',
            'value' => 'Quick',
            'tag' => 'web',
        ];

        $start = microtime(true);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/translations', $data);

        $durationMs = (microtime(true) - $start) * 1000;

        $response->assertStatus(201);
        $this->assertLessThan(500, $durationMs, "Request took too long: {$durationMs}ms");
    }

    public function test_show_endpoint_responds_under_500ms()
    {
        // Authenticate the user
        $user = $this->getUser();

        // Create a translation
        $translation = Translation::factory()->create();

        // Start timing
        $start = microtime(true);

        // Call the API
        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/translations/{$translation->id}");

        // End timing
        $duration = (microtime(true) - $start) * 1000;

        // Output duration to terminal (optional)
        fwrite(STDOUT, "\nShow endpoint duration: {$duration} ms\n");

        // Assert API is fast
        $response->assertStatus(200);
        $this->assertLessThan(500, $duration, "API took longer than 500ms.");
    }

    public function test_translation_update_response_under_500ms()
    {
        // Authenticate
        $user = $this->getUser();

        $translation = Translation::factory()->create();

        $payload = [
            'key' => 'perf_key',
            'locale' => 'en',
            'tag' => 'performance',
            'value' => 'performance test'
        ];

        $start = microtime(true);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/translations/{$translation->id}", $payload);

        $duration = (microtime(true) - $start) * 1000;

        fwrite(STDOUT, "\nUpdate endpoint duration: {$duration} ms\n");

        $response->assertStatus(200);
        $this->assertLessThan(500, $duration, 'Update endpoint exceeded 500ms.');
    }

    public function test_destroy_translation_response_under_500ms()
    {
        $user = $this->getUser();

        $translation = Translation::factory()->create();

        $start = microtime(true);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/translations/{$translation->id}");

        $duration = (microtime(true) - $start) * 1000; // milliseconds

        fwrite(STDOUT, "\nDestroy endpoint duration: {$duration} ms\n");

        $response->assertStatus(200);
        $this->assertLessThan(500, $duration, 'Destroy endpoint exceeded 500ms.');
    }


    public function test_search_translations_responds_under_500ms()
    {
        $user = $this->getUser();

        // Seed many translations
        Translation::factory()->count(100)->create();

        // One match
        Translation::factory()->create([
            'key' => 'performance.test.key',
            'value' => 'Performance test value',
            'locale' => 'en',
            'tag' => 'web'
        ]);

        $start = microtime(true);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/translations/search?key=performance.test.key');

        $duration = (microtime(true) - $start) * 1000;

        fwrite(STDOUT, "\nSearch endpoint duration: {$duration} ms\n");

        $response->assertStatus(200);
        $this->assertLessThan(500, $duration, 'Search endpoint exceeded 500ms.');
    }

    public function test_login_response_is_fast()
    {
        $user = User::factory()->create([
            'email' => 'fast@example.com',
            'password' => bcrypt('securepass')
        ]);

        $start = microtime(true);

        $response = $this->postJson('/api/login', [
            'email' => 'fast@example.com',
            'password' => 'securepass'
        ]);

        $duration = microtime(true) - $start;

        $response->assertStatus(200);
        $this->assertLessThan(0.5, $duration, 'Login response took too long: ' . $duration . ' seconds');
    }

    public function test_logout_response_is_fast()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $start = microtime(true);

        $response = $this->postJson('/api/logout');

        $duration = microtime(true) - $start;

        $response->assertStatus(200);
        $this->assertLessThan(0.5, $duration, "Logout response took too long: {$duration} seconds");
    }
}
