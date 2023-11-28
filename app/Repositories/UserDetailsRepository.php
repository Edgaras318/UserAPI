<?php

namespace App\Repositories;

use App\Models\UserDetails;
use App\Repositories\Contracts\UserDetailsRepositoryInterface;


class UserDetailsRepository implements UserDetailsRepositoryInterface
{
  public function create(array $attributes): UserDetails
  {
    return UserDetails::create($attributes);
  }

  public function delete(UserDetails $userDetails)
  {
    $userDetails->delete();
  }

  public function update(UserDetails $userDetails, array $attributes): bool
  {
    return $userDetails->update($attributes);
  }
}
