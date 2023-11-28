<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\UserDetails;

interface UserDetailsRepositoryInterface
{
  public function create(array $userDetailsData);

  public function update(UserDetails $userDetails, array $newUserDetailsData);

  public function delete(UserDetails $userDetails);
}
