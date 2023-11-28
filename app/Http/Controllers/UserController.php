<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function delete(User $user)
    {
        try {
            $this->authorize('delete', $user);

            $this->userService->deleteUser($user);

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'You cannot delete your own account'], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user'], 500);
        }
    }

    public function create(CreateUserRequest $request)
    {
        try {
            $userData = $request->only(['first_name', 'last_name', 'email', 'password']);
            $address = $request->input('address');

            $user = $this->userService->createUser($userData, $address);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user['user'],
                'token' => $user['token'],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to create user'], 500);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $userData = $request->only(['first_name', 'last_name', 'email', 'password']);
            $address = $request->input('address');

            $this->userService->updateUser($user, $userData, $address);

            return response()->json(['message' => 'User updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update user'], 500);
        }
    }

    public function all()
    {
        $users = $this->userService->getAllUsers();

        return response()->json(['users' => $users], 200);
    }
}
