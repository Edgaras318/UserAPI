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

  public function test_invalid_data_cannot_create_user()
  {
    $invalidUserData = [
      'first_name' => 'John',
      // Missing 'last_name', 'email', 'password', 'password_confirmation', 'address'
    ];

    // Send a POST request to create a user with invalid data
    $response = $this->withHeaders(['Accept' => 'application/json'])->post('/api/register', $invalidUserData);

    // Assert response status and content
    $response->assertStatus(422)
      ->assertJsonValidationErrors(['last_name', 'email', 'password']);
  }

  public function test_authenticated_user_cannot_update_with_invalid_data()
  {
    // Invalid update payload without required fields
    $invalidUpdateData = [
      // Missing 'first_name', 'last_name', 'email', 'password'
      'first_name' => '', // Empty first name
      'last_name' => '', // Empty last name
      'email' => '', // Empty email
      // Password is not provided
      // Include other fields required for update, if any
    ];

    // Simulate authentication by acting as the user
    // Send a PUT request to update the user's own profile with invalid data
    $response = $this->withHeaders(['Accept' => 'application/json'])
      ->actingAs($this->user)
      ->put("/api/users/{$this->user->id}", $invalidUpdateData);

    // Assert response status and content
    $response->assertStatus(422)
      ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);
  }


  private function createUser(): User
  {
    return User::factory()->create();
  }
}
