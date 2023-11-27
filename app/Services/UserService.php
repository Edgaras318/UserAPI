<?php

namespace App\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\UserDetailsRepository;

class UserService
{
    protected $userRepository;
    protected $userDetailsRepository;

    public function __construct(UserRepository $userRepository, UserDetailsRepository $userDetailsRepository)
    {
        $this->userRepository = $userRepository;
        $this->userDetailsRepository = $userDetailsRepository;
    }

    public function deleteUser(User $authenticatedUser, User $user)
    {
        // Authorization logic
        if ($authenticatedUser->id === $user->id) {
            throw new AuthorizationException('You cannot delete your own account');
        }

        return DB::transaction(function () use ($user) {
            $this->userRepository->delete($user);

            if ($user->userDetails) {
                $this->userDetailsRepository->delete($user->userDetails);
            }
        });
    }

    public function createUser(array $userData, ?string $address = null)
    {
        return DB::transaction(function () use ($userData, $address) {
            $user = $this->userRepository->create($userData);

            if ($address) {
                $userDetailsData = [
                    'user_id' => $user->id,
                    'address' => $address,
                ];
                $this->userDetailsRepository->create($userDetailsData);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    public function getAllUsers()
    {
        return $this->userRepository->all();
    }
}
