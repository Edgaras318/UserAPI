<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
  public function create(array $attributes): User
  {
    // Hash the password before creating the user
    $attributes['password'] = bcrypt($attributes['password']);

    return User::create($attributes);
  }

  public function update(User $user, array $attributes): bool
  {
    return $user->update($attributes);
  }

  public function delete(User $user): bool
  {
    return $user->delete();
  }

  public function all(): Collection
  {
    return User::all();
  }
}
