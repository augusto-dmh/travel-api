<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\CreateUserRequest;

class CreateUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        return new UserResource($user);
    }
}
