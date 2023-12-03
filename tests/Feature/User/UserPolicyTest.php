<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPolicyTest extends TestCase
{

    use RefreshDatabase;

    public function testUserCannotDeleteOwnAccount()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an instance of the policy
        $userPolicy = new UserPolicy();

        // Attempt to delete own account
        $result = $userPolicy->delete($user, $user);

        // Assert that the result is false
        $this->assertFalse($result);
    }

    public function testUserCanDeleteAnotherAccount()
    {
        // Create two users
        $loggedInUser = User::factory()->create();
        $userToDelete = User::factory()->create();

        // Create an instance of the policy
        $userPolicy = new UserPolicy();

        // Attempt to delete another user's account
        $result = $userPolicy->delete($loggedInUser, $userToDelete);

        // Assert that the result is true
        $this->assertTrue($result);
    }
}
