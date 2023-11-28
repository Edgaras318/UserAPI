<?php

namespace Tests\Unit\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
}
