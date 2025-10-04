<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponder;
use Dedoc\Scramble\Attributes\Group;

class UserController extends Controller
{
    use ApiResponder;

    /**
     * Get all accounts
     */
    #[Group('User')]
    public function index()
    {
        $users = User::all();

        return $this->success(UserResource::collection($users));
    }

    /**
     * Create new account
     */
    #[Group('User')]
    public function store(CreateUserRequest $request)
    {
        $user = User::create($request->all());

        return $this->created(new UserResource($user));
    }

    /**
     * Get detail of account
     */
    #[Group('User')]
    public function show(User $user)
    {
        return $this->success(new UserResource($user));
    }

    /**
     * Update an account
     */
    #[Group('User')]
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());

        return $this->success(new UserResource($user));
    }

    /**
     * Delete account
     */
    #[Group('User')]
    public function destroy(User $user)
    {
        $count = User::count();
        if ($count > 1) {
            $user->delete();

            return $this->deleted();
        } else {
            return $this->error('At least system have one account');
        }
    }
}
