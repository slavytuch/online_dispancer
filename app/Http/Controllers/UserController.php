<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\PatientInfoResource;
use App\Http\Resources\UserListItem;
use App\Models\User;

class UserController extends Controller
{
    public function getById($userId)
    {
        return User::findOrFail($userId);
    }

    public function getList()
    {
        return User::all();
    }

    public function update($userId, UserUpdateRequest $request)
    {
        return User::findOrFail($userId)->update($request->toArray());
    }

    public function add(UserCreateRequest $request)
    {
        return User::create($request->toArray());
    }
}
