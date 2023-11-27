<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Repositories\UserDetailsRepository;
use App\Models\User;

class UserController extends Controller
{
    protected $userRepository;
    protected $userDetailsRepository;

    public function __construct(UserRepository $userRepository, UserDetailsRepository $userDetailsRepository)
    {
        $this->userRepository = $userRepository;
        $this->userDetailsRepository = $userDetailsRepository;
    }


    public function create(CreateUserRequest $request)
    {

        try {
            DB::beginTransaction();

            $userData = $request->only(['first_name', 'last_name', 'email', 'password']);
            $user = $this->userRepository->create($userData);

            if ($request->has('address')) {
                $userDetailsData = [
                    'user_id' => $user->id,
                    'address' => $request->input('address'),
                ];
                $this->userDetailsRepository->create($userDetailsData);
            }

            // Generate token for the user
            $token = $user->createToken('api-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    public function delete(User $user)
    {
        try {
            DB::beginTransaction();

            $this->userRepository->delete($user);

            if ($user->userDetails) {
                $this->userDetailsRepository->delete($user->userDetails);
            }

            DB::commit();

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to delete user'], 500);
        }
    }


    public function all()
    {
        $users = $this->userRepository->all();

        return response()->json(['users' => $users], 200);
    }
}
