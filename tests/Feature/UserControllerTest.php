<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
  use RefreshDatabase;

  private User $user;

  protected function setUp(): void
  {
    parent::setUp();

    $this->user = $this->createUser();
  }

  public function test_can_create_user_successful()
  {
    // Create a user request payload
    $userData = [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john@example.com',
      'password' => 'password',
      'password_confirmation' => 'password',
      'address' => '123 Main St',
    ];

    // Send a POST request to create a user
    $response = $this->post('/api/register', $userData);

    // Assert response status and content
    $response->assertStatus(201)
      ->assertJson([
        'message' => 'User created successfully',
      ]);
  }

  public function test_authenticated_user_can_update_own_profile_successful()
  {
    // Update payload
    $updateData = [
      'first_name' => 'Jane',
      'last_name' => 'Doe',
      'email' => 'john1@example.com',
      'password' => 'password1',
    ];

    // Simulate authentication by acting as the user
    // Send a PUT request to update the user's own profile
    $response = $this->actingAs($this->user)->put("/api/users/{$this->user->id}", $updateData);

    // Assert response status and content
    $response->assertStatus(200)
      ->assertJson([
        'message' => 'User updated successfully',
      ]);
  }

  public function test_authenticated_user_cannot_delete_own_account()
  {
    // Simulate authentication by acting as the user
    // Send a DELETE request to delete the user's own account
    $response = $this->actingAs($this->user)->delete("/api/users/{$this->user->id}");

    // Assert response status and content
    $response->assertStatus(403)
      ->assertJson([
        'message' => 'You cannot delete your own account',
      ]);
  }

  public function test_authenticated_user_can_delete_other_user_account_successful()
  {
    // Create User
    $createdUser = User::factory()->create();

    // Send a DELETE request to delete user B's account
    $response = $this->actingAs($this->user)->delete("/api/users/{$createdUser->id}");

    // Assert response status and content
    $response->assertStatus(200)
      ->assertJson([
        'message' => 'User deleted successfully',
      ]);

    // Check if deleted user is not prsent in database anymore
    $this->assertDatabaseMissing('users', $createdUser->toArray());
  }

  public function test_authenticated_user_can_get_all_users_successful()
  {
    // Simulate authentication by acting as the user
    // Send a GET request to retrieve all users
    $response = $this->actingAs($this->user)->get('/api/users');

    // Retrieve all users from the database
    $users = User::all()->toArray();

    // Assert response status and content
    $response->assertStatus(200)
      ->assertJson([
        'users' => $users, // Compare directly to the retrieved users
      ]);
  }

  private function createUser(): User
  {
    return User::factory()->create();
  }
}
