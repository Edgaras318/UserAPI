<?php

namespace App\Services;

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

    public function deleteUser(User $user)
    {

        return DB::transaction(function () use ($user) {
            $deletedUser = $this->userRepository->delete($user);

            if ($user->userDetails) {
                $deletedUserDetails = $this->userDetailsRepository->delete($user->userDetails);
            } else {
                // If there are no user details, consider it as successfully deleted
                $deletedUserDetails = true;
            }

            return $deletedUser && $deletedUserDetails;
        });
    }

    public function updateUser(User $user, array $userData, ?string $address = null)
    {
        return DB::transaction(function () use ($user, $userData, $address) {
            $this->userRepository->update($user, $userData);

            if ($address !== null) {
                if ($user->userDetails) {

                    $this->userDetailsRepository->update($user->userDetails, ['address' => $address]);
                } else {
                    $userDetailsData = [
                        'user_id' => $user->id,
                        'address' => $address,
                    ];
                    $this->userDetailsRepository->create($userDetailsData);
                }
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
