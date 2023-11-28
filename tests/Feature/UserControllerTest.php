<?php

namespace Tests\Unit\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
  use RefreshDatabase;

  public function test_can_create_user()
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

  public function test_authenticated_user_can_update_own_profile()
  {
    // Create a user
    $user = User::factory()->create();

    // Simulate authentication by acting as the user
    $this->actingAs($user);

    // Update payload
    $updateData = [
      'first_name' => 'Jane',
      'last_name' => 'Doe',
      'email' => 'john1@example.com',
      'password' => 'password1',
    ];

    // Send a PUT request to update the user's own profile
    $response = $this->put("/api/users/{$user->id}", $updateData);

    // Assert response status and content
    $response->assertStatus(200)
      ->assertJson([
        'message' => 'User updated successfully',
      ]);
  }

  public function test_authenticated_user_cannot_delete_own_account()
  {
    // Create a user
    $user = User::factory()->create();

    // Simulate authentication by acting as the user
    $this->actingAs($user);

    // Send a DELETE request to delete the user's own account
    $response = $this->delete("/api/users/{$user->id}");

    // Assert response status and content
    $response->assertStatus(403)
      ->assertJson([
        'message' => 'You cannot delete your own account',
      ]);
  }
  public function test_authenticated_user_can_delete_other_user_account()
  {
    // Create two users
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // Simulate authentication as user A
    $this->actingAs($userA);

    // Send a DELETE request to delete user B's account
    $response = $this->delete("/api/users/{$userB->id}");

    // Assert response status and content
    $response->assertStatus(200)
      ->assertJson([
        'message' => 'User deleted successfully',
      ]);
  }

  public function test_authenticated_user_can_get_all_users()
  {
    // Create an authenticated user
    $user = User::factory()->create();

    // Simulate authentication by acting as the user
    $this->actingAs($user);

    // Send a GET request to retrieve all users
    $response = $this->get('/api/users');

    // Retrieve all users from the database
    $users = User::all()->toArray();

    // Assert response status and content
    $response->assertStatus(200)
      ->assertJson([
        'users' => $users, // Compare directly to the retrieved users
        // Add more assertions based on the expected response structure
      ]);
  }
}
