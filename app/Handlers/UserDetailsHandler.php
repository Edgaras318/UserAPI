<?php

namespace App\Handlers;

use App\Models\User;
use App\Repositories\UserDetailsRepository;

class UserDetailsHandler
{
  protected UserDetailsRepository $userDetailsRepository;

  public function __construct(UserDetailsRepository $userDetailsRepository)
  {
    $this->userDetailsRepository = $userDetailsRepository;
  }

  /**
   * Create user details.
   *
   * @param User $user
   * @param string $address
   * @return void
   */
  public function createUserDetails(User $user, string $address): void
  {
    $userDetailsData = [
      'user_id' => $user->id,
      'address' => $address,
    ];

    $this->userDetailsRepository->create($userDetailsData);
  }

  /**
   * Update user details.
   *
   * @param User $user
   * @param string|null $address
   * @return void
   */
  public function updateUserDetails(User $user, ?string $address): void
  {
    if ($address !== null) {
      $userDetails = $user->userDetails;

      if ($userDetails) {
        $this->userDetailsRepository->update($userDetails, ['address' => $address]);
      } else {
        $this->createUserDetails($user, $address);
      }
    }
  }

  /**
   * Delete user details.
   *
   * @param User $user
   * @return void
   */
  public function deleteUserDetails(User $user): void
  {
    $userDetails = $user->userDetails;

    if ($userDetails) {
      $this->userDetailsRepository->delete($userDetails);
    }
  }
}
