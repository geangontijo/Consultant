<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

class UserController extends Controller
{
    /**
     * @param   \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'password' => ['required', 'min:8']
        ]);
        DB::setDefaultConnection('mongodb');

        $userAttributes = $request->only(
            'name',
            'password'
        );

        $user = new User(
            $userAttributes
        );
        $user->save();

        $token = $user->createToken('access-token')->plainTextToken;
        return new JsonResponse(compact('user', 'token'));
    }

    public function becomeProfessional(Request $request)
    {
        # TODO: receive certificate
        $request->validate([
            'crm' => ['required'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'min:11', 'max:11'],
            'photo_url' => ['required', 'url', 'max:255'],
        ]);

        /** @var User $user */
        $user = $request->user();

        if (DB::table('professionals')->where('user_id', '=', $user->id)
                                     ->first()) {
            return new JsonResponse([
                'message' => 'User already is a professional'
            ], 400);
        }

        $professional = new Professional();
        $professional->email = $request->email;
        $professional->crm = $request->crm;
        $professional->phone_number = $request->phone_number;
        $professional->photo_url = $request->photo_url;
        $professional->user()->associate($user);
        $professional->save();

        $token = $user->createToken('access-token', ['consultation:self-manage'])
            ->plainTextToken;

        return new JsonResponse(compact('professional', 'token'));
    }
}
