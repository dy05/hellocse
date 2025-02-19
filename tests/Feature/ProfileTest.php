<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\ProfilStatusEnum;
use App\Models\Administrator;
use App\Models\Profil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private static function getHeaders(): array
    {
        return ['accept' => 'application/json'];
    }

    public function test_user_controller_index_returns_all_users(): void
    {
        // Arrange: create dummy users
        Profil::factory()->count(5)->create(['status' => ProfilStatusEnum::INACTIF->value]);
        Profil::factory()->count(5)->create(['status' => ProfilStatusEnum::WAITING->value]);
        $users = Profil::factory()->count(2)->create(['status' => ProfilStatusEnum::ACTIF->value]);

        // Act: make a GET request to fetch all users
        $response = $this->get('/api/users');

        // Assert: ensure a successful response and correct data structure
        $response->assertStatus(200)
            ->assertJsonCount($users->count(), 'profils');
    }

    public function test_user_controller_store_creates_new_user_unauthorized(): void
    {
        // Arrange: prepare fake data for a new user
        $userData = Profil::factory()->make()->toArray();
        $data = [
            'lastname' => $userData['lastname'],
            'firstname' => $userData['firstname'],
            'status' => $userData['status'],
        ];

        // Act: send the data with a POST request
        $response = $this->post('/api/users', $data, self::getHeaders());

        // Assert: ensure the response is unauthorized error
        $response->assertStatus(401);
    }

    public function test_user_controller_store_failed_creating_new_user_without_good_data(): void
    {
        // Arrange: prepare fake Administrator
        $admin = Administrator::factory()->create();
        $this->actingAs($admin);

        // Arrange: prepare fake data for a new user
        $userData = Profil::factory()->make()->toArray();
        $data = [
            'lastname' => $userData['lastname'],
            'status' => $userData['status'],
        ];

        // Act: send the data with a POST request
        $response = $this->post('/api/users', $data, self::getHeaders());

        // Assert: ensure the response is validation errors
        $response->assertStatus(422);
    }

    public function test_user_controller_store_creates_new_user(): void
    {
        // Arrange: prepare fake Administrator
        $admin = Administrator::factory()->create();
        $this->actingAs($admin);

        // Arrange: prepare fake data for a new user
        $userData = Profil::factory()->make()->toArray();
        $data = [
            'lastname' => $userData['lastname'],
            'firstname' => $userData['firstname'],
            'status' => $userData['status'],
        ];

        // Act: send the data with a POST request
        $response = $this->post('/api/users', $data, self::getHeaders());

        // Assert: ensure the response is successful and user is created
        $response->assertStatus(201); // assuming 201 Created is returned
        $this->assertDatabaseHas('profils', $data);
    }

    public function test_user_controller_update_correctly_updates_user(): void
    {
        // Arrange: prepare fake Administrator
        $admin = Administrator::factory()->create();
        $this->actingAs($admin);

        // Arrange: create a dummy user and prepare updated data
        $user = Profil::factory()->create();
        $updatedData = [
            'firstname' => 'Updated Name',
            'lastname' => $user->lastname,
        ];

        // Act: send a PUT request to update the user's data
        $response = $this->post("/api/users/{$user->id}", array_merge($updatedData, [
            '_method' => 'PUT',
        ]), self::getHeaders());

        // Assert: ensure the response is successful and data is updated
        $response->assertStatus(200);
        $this->assertDatabaseHas('profils', array_merge($user->only('id'), $updatedData));
    }

    public function test_user_controller_destroy_deletes_user(): void
    {
        // Arrange: prepare fake Administrator
        $admin = Administrator::factory()->create();
        $this->actingAs($admin);

        // Arrange: create a dummy user
        $user = Profil::factory()->create();

        // Act: send a DELETE request to remove the user
        $response = $this->delete("/api/users/{$user->id}", [], self::getHeaders());

        // Assert: ensure the response is successful and data is deleted
        $response->assertStatus(200);
        $this->assertDatabaseMissing('profils', $user->toArray());
    }
}
