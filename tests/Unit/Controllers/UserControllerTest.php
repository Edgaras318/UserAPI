<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;
use App\Http\Controllers\UserController;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;

class UserControllerTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    // Migrate your database
    $this->artisan('migrate');
  }

  public function testDeleteMethodAuthorized()
  {
    // Create two user instances
    $userToDelete = User::factory()->create();
    $authenticatedUser = User::factory()->create();

    // Create mocks
    $userService = Mockery::mock(UserService::class);
    $userService->shouldReceive('deleteUser')->once()->andReturn(true);

    // Set up UserController with mocked UserService
    $controller = new UserController($userService);

    // Simulate authentication with the authenticated user
    $this->actingAs($authenticatedUser);

    // Call the delete method with the user to delete
    $response = $controller->delete($userToDelete);

    // Assert the response
    $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
  }

  public function testDeleteMethodUnauthorized()
  {
    // Create a user instance
    $user = new User(); // You can set attributes if needed

    // Create mocks
    $userService = Mockery::mock(UserService::class);
    $userService->shouldReceive('deleteUser')->andThrow(new AuthorizationException());

    // Set up UserController with mocked UserService
    $controller = new UserController($userService);

    // Simulate authorization
    // Replace with your logic to simulate authentication if required

    // Call the delete method
    $response = $controller->delete($user);

    // Assert the response
    $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
  }

  public function testDeleteOwnAccount()
  {
    // Create a user instance
    $user = User::factory()->create();

    // Create a mock service
    $userService = Mockery::mock(UserService::class);

    // Set up UserController with mocked UserService
    $controller = new UserController($userService);

    // Simulate authentication with the user attempting to delete their own account
    $this->actingAs($user);

    // Call the delete method with the user's own account
    $response = $controller->delete($user);

    // Assert the response
    $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
  }
}
