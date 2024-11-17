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
        return PatientInfoResource::make(User::findOrFail($userId));
    }

    public function getList()
    {
        return UserListItem::collection(User::all());
    }

    public function update($patientId, UserUpdateRequest $request)
    {
        //TODO:
    }

    public function add(UserCreateRequest $request)
    {
        //TODO:
    }
}
