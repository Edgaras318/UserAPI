<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Handlers\UserDetailsHandler;

class UserService
{
    protected $userRepository;
    protected $userDetailsHandler;

    public function __construct(UserRepository $userRepository, UserDetailsHandler $userDetailsHandler)
    {
        $this->userRepository = $userRepository;
        $this->userDetailsHandler = $userDetailsHandler;
    }


    public function createUser(array $userData, ?string $address = null)
    {
        return DB::transaction(function () use ($userData, $address) {
            // Hash the password before creating the user
            $userData['password'] = bcrypt($userData['password']);

            $user = $this->userRepository->create($userData);

            if ($address) {
                $this->userDetailsHandler->createUserDetails($user, $address);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    public function updateUser(User $user, array $userData, ?string $address = null)
    {
        return DB::transaction(function () use ($user, $userData, $address) {
            $this->userRepository->update($user, $userData);

            $this->userDetailsHandler->updateUserDetails($user, $address);
        });
    }

    public function deleteUser(User $user)
    {
        return DB::transaction(function () use ($user) {
            $this->userDetailsHandler->deleteUserDetails($user);

            return $this->userRepository->delete($user);
        });
    }

    public function getAllUsers()
    {
        return $this->userRepository->all();
    }
}
