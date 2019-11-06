<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Http\Request;

class StoreUploadFile extends Controller
{
    public function __invoke(UpdateAvatarRequest $request)
    {
        resolve('upload')
            ->handler('avatar')
            ->withUser($request->user())
            ->withFile($request->file('file'))
            ->store();

        return response()->json([
            'message' => 'Change avatar successfully.!'
        ], 201);
    }
}
