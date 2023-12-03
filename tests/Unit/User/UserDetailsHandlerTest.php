<?php

namespace Tests\Unit\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserDetails;
use App\Handlers\UserDetailsHandler;
use App\Repositories\UserDetailsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserDetailsHandlerTest extends TestCase
{
  use RefreshDatabase;

  private User $user;

  protected function setUp(): void
  {
    parent::setUp();

    $this->user = $this->createUser();
  }

  public function testCreateUserDetails()
  {
    // Mock UserDetailsRepository
    $userDetailsRepository = $this->createMock(UserDetailsRepository::class);
    $userDetailsRepository->expects($this->once())
      ->method('create')
      ->with(['user_id' => $this->user->id, 'address' => 'Test Address']);

    // Create UserDetailsHandler instance
    $userDetailsHandler = new UserDetailsHandler($userDetailsRepository);

    // Test createUserDetails method
    $userDetailsHandler->createUserDetails($this->user, 'Test Address');
  }

  public function testDeleteUserDetails()
  {
    // Create a user details record for the user
    $userDetails = UserDetails::factory()->create(['user_id' => $this->user->id]);

    // Mock UserDetailsRepository
    $userDetailsRepository = $this->createMock(UserDetailsRepository::class);

    // Expect the delete method to be called once with any UserDetails instance
    $userDetailsRepository->expects($this->once())
      ->method('delete')
      ->with($this->isInstanceOf(UserDetails::class));

    // Create UserDetailsHandler instance
    $userDetailsHandler = new UserDetailsHandler($userDetailsRepository);

    // Test deleteUserDetails method
    $userDetailsHandler->deleteUserDetails($this->user);
  }

  public function testUpdateUserDetails()
  {
    // Create a user and user details record for testing
    $user = User::factory()->create();
    $userDetails = UserDetails::factory()->create(['user_id' => $user->id]);

    // Mock UserDetailsRepository
    $userDetailsRepository = $this->createMock(UserDetailsRepository::class);

    // Expect the update method to be called once with any UserDetails instance and the new address
    $userDetailsRepository->expects($this->once())
      ->method('update')
      ->with($this->isInstanceOf(UserDetails::class), ['address' => 'New Test Address'])
      ->willReturn(true); // Adjust return value based on your actual implementation

    // Create UserDetailsHandler instance
    $userDetailsHandler = new UserDetailsHandler($userDetailsRepository);

    // Test updateUserDetails method
    $userDetailsHandler->updateUserDetails($user, 'New Test Address');
  }


  private function createUser(): User
  {
    return User::factory()->create();
  }
}
