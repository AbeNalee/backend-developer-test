<?php

namespace Tests\Feature;

use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create();
        $this->seed(DatabaseSeeder::class);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
    }

    public function testApplicationResponseForNewUserIsCorrectFormat()
    {
        $user = User::factory()->create();
        $this->seed(DatabaseSeeder::class);

        $response = $this->get("/users/{$user->id}/achievements");

        $this->assertTrue(empty($response['unlocked_achievements']));
        $response->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['unlocked_achievements', 'next_available_achievements',
                'current_badge', 'next_badge', 'remaining_to_unlock_next_badge'])
        );
    }
}
