<?php

namespace Tests\Feature;

use App\Models\Translation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TranslationApiTest extends TestCase
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

    /**
     * ***************************
     * Tests for index route
     * ***************************
     */
    public function test_index_returns_translations_for_authenticated_user()
    {
        // Create a user and authenticate with Sanctum
        $user = $this->getUser();

        // Create some translations
        Translation::factory()->count(5)->create();

        // Hit the API
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/translations');
        // Assert successful response and correct structure
        $response->assertStatus(200)
            ->assertJsonStructure(
                ['data' => ['*' => ['id', 'key', 'locale', 'value', 'tag']]]
            );
    }

    public function test_index_requires_authentication()
    {
        $response = $this->getJson('/api/translations');

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * ***************************
     * Tests for store route
     * ***************************
     */
    public function test_store_translation_successfully()
    {

        $user = $this->getUser();

        $data = [
            'key' => 'test',
            'locale' => 'ts',
            'value' => 'Welcome',
            'tag' => 'web',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/translations', $data);

        $response->assertStatus(201)
            ->assertJson(['status' => 1, 'message' => 'Translation Created!']);

        $this->assertDatabaseHas('translations', ['key' => 'test', 'locale' => 'ts']);
    }

    public function test_store_translation_already_exists()
    {
        $user =  $this->getUser();


        Translation::factory()->create([
            'key' => 'welcome',
            'locale' => 'en',
            'value' => 'Welcome',
            'tag' => 'web'
        ]);

        $data = [
            'key' => 'welcome',
            'locale' => 'en',
            'value' => 'Welcome',
            'tag' => 'web',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/translations', $data);

        $response->assertStatus(422)
            ->assertJson(['status' => 1, 'message' => 'Translation with key and locale exits!']);
    }

    public function test_store_translation_handles_exception()
    {
        $user = $this->getUser();

        // Override service to throw exception
        $this->mock(\App\Services\TranslationService::class, function ($mock) {
            $mock->shouldReceive('findByKeyAndLocale')->andThrow(new \Exception('DB error'));
        });

        $data = [
            'key' => 'error_test',
            'locale' => 'en',
            'value' => 'Fail',
            'tag' => 'web',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/translations', $data);

        $response->assertStatus(200)
            ->assertJson(['status' => 0]);
    }

    /**
     * ***************************
     * Tests for show route
     * ***************************
     */
    public function test_show_returns_translation_for_authenticated_user()
    {
        // Create and authenticate user
        $user = $this->getUser();

        // Create a translation record
        $translation = Translation::factory()->create();

        // Make the API request
        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/translations/{$translation->id}");

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'id' => $translation->id,
                'key' => $translation->key,
                'locale' => $translation->locale,
                'value' => $translation->value,
                'tag' => $translation->tag,
            ]);
    }

    public function test_show_returns_404_for_invalid_id()
    {
        // Create and authenticate user
        $user = $this->getUser();

        // Try to fetch non-existing translation
        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/translations/999999");

        $response->assertStatus(404);
    }

    public function test_show_requires_authentication()
    {
        // Create a translation
        $translation = Translation::factory()->create();

        // Hit the endpoint without Sanctum authentication
        $response = $this->getJson("/api/translations/{$translation->id}");

        // Should return 401 Unauthorized
        $response->assertStatus(401);
    }

    /**
     * ***************************
     * Tests for update route
     * ***************************
     */
    public function test_translation_update_successfully()
    {
        // Authenticated user
        $user =$this->getUser();

        $translation = Translation::factory()->create([
            'key' => 'greeting',
            'locale' => 'en',
            'value' => 'Hello',
            'tag' => 'web'
        ]);

        $payload = [
            'key' => 'greeting_updated',
            'locale' => 'en',
            'tag' => 'mobile',
            'value' => 'Hello world'
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/translations/{$translation->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Translation updated!'
            ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'key' => 'greeting_updated',
            'locale' => 'en',
        ]);
    }

    public function test_translation_update_unauthenticated()
    {
        $translation = Translation::factory()->create();

        $response = $this->putJson("/api/translations/{$translation->id}", [
            'key' => 'new_key',
            'locale' => 'en',
            'tag' => 'mobile'
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    public function test_translation_update_not_found()
    {
        $user = $this->getUser();

        $response = $this->actingAs($user, 'sanctum')->putJson('/api/translations/99999', [
            'key' => 'missing_key',
            'locale' => 'en',
            'tag' => 'web',
            'value' => 'web missing'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 0,
                'message' => 'Not found!'
            ]);
    }


    /**
     * ***************************
     * Tests for destroy route
     * ***************************
     */
    public function test_destroy_translation_successfully()
    {
        $user = $this->getUser();

        $translation = Translation::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'message' => 'Deleted successfully'
            ]);

        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    public function test_destroy_translation_not_found()
    {
        $user = $this->getUser();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/translations/999999');

        $response->assertStatus(404)
            ->assertJson([
                'status' => 0,
                'message' => 'Not found!'
            ]);
    }

    public function test_destroy_translation_unauthenticated()
    {
        $translation = Translation::factory()->create();

        $response = $this->deleteJson("/api/translations/{$translation->id}");

        $response->assertStatus(401); // Unauthorized
    }

    /**
     * ***************************
     * Tests for search route
     * ***************************
     */
    public function test_search_translations_by_key()
    {
        $user = $this->getUser();

        Translation::factory()->create([
            'key' => 'welcome.message',
            'value' => 'Welcome to the app!',
            'locale' => 'en',
            'tag' => 'web'
        ]);

        Translation::factory()->create([
            'key' => 'login.button',
            'value' => 'Login',
            'locale' => 'en',
            'tag' => 'web'
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/translations/search?key=welcome.message');

        $response->assertStatus(200)
            ->assertJsonFragment(['key' => 'welcome.message']);
    }

    public function test_search_translations_by_multiple_fields()
    {
        $user = $this->getUser();

        Translation::factory()->create([
            'key' => 'logout.button',
            'value' => 'Logout',
            'locale' => 'fr',
            'tag' => 'mobile'
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/translations/search?key=logout.button&locale=fr&tag=mobile');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'key' => 'logout.button',
                'locale' => 'fr',
                'tag' => 'mobile'
            ]);
    }

    public function test_search_translations_unauthenticated()
    {
        $response = $this->getJson('/api/translations/search?key=test');

        $response->assertStatus(401); // Unauthorized
    }
}
