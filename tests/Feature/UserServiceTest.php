<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserServiceTest extends TestCase
{
  use RefreshDatabase;

  protected $userService;

  public function setUp(): void
  {
    parent::setUp();
    $this->userService = app(UserService::class);
  }

  public function testCreateUser()
  {
    $userData = [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'john@example.com',
      'password' => 'password123',
    ];
    $address = '123 Main St';

    $result = $this->userService->createUser($userData, $address);

    $this->assertInstanceOf(User::class, $result['user']);
    $this->assertArrayHasKey('token', $result);
  }

  public function testUpdateUser()
  {
    $user = User::factory()->create();
    $newUserData = [
      'first_name' => 'John',
      'last_name' => 'Doe',
      'email' => 'jane@example.com',
    ];
    $newAddress = '456 Oak Ave';

    $this->userService->updateUser($user, $newUserData, $newAddress);

    $updatedUser = User::find($user->id);
    $this->assertEquals($newUserData['first_name'], $updatedUser->first_name);
    $this->assertEquals($newUserData['last_name'], $updatedUser->last_name);
    $this->assertEquals($newUserData['email'], $updatedUser->email);

    $this->assertEquals($newAddress, $updatedUser->userDetails->address);
  }

  public function testDeleteUser()
  {
    $user = User::factory()->create();

    $result = $this->userService->deleteUser($user);

    $this->assertTrue($result);
    $this->assertNull(User::find($user->id));
  }

  public function testGetAllUsers()
  {
    $users = User::factory()->count(3)->create();

    $retrievedUsers = $this->userService->getAllUsers();

    $this->assertCount(3, $retrievedUsers);
    $this->assertEquals($users->pluck('id'), $retrievedUsers->pluck('id'));
  }
}
