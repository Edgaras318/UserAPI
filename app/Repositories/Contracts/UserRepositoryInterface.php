<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use App\Models\User;

interface UserRepositoryInterface
{
  public function create(array $userData): User;

  public function update(User $user, array $newUserData): bool;

  public function delete(User $user): bool;

  public function all(): Collection;
}
