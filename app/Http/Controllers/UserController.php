<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\User;
use App\Models\Verification;
use App\Notifications\VerifyWithCode;
use App\Rules\PhoneNumberFormat;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Sanctum\Sanctum;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::setDefaultConnection('mongodb');
        $request->validate([
            'name' => ['required', 'max:255'],
            'password' => ['required', 'min:8'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $userAttributes = $request->only(
            'name',
            'password',
            'email'
        );

        $user = new User(
            $userAttributes
        );
        $user->save();

        $token = $user->createToken('access-token')->plainTextToken;
        return new JsonResponse(compact('user', 'token'));
    }

    /**
     * @throws Throwable
     */
    public function storeProfessional(Request $request): RedirectResponse
    {
        # TODO: receive certificate
        $request->validate([
            'telefone' => ['required', new PhoneNumberFormat()],
            'cpf' => ['required', 'cpf'],
            'foto_perfil' => ['required', File::image()->max(1024)],
            'codigo_verificacao' => ['required', 'digits:6'],
        ]);

        /** @var User $user */
        $user = $request->user();

        if ($user->verification->hasExpired()) {
            throw ValidationException::withMessages([
                'codigo_verificacao' => trans('auth.verify.expired')
            ]);
        }
        if ($user->verification->code !== $request->integer('codigo_verificacao')) {
            throw ValidationException::withMessages([
                'codigo_verificacao' => trans('auth.verify.failed_code')
            ]);
        }
        $user->verified_at = new \DateTime();
        $user->verification = null;
        $user->save();
        $filePath = 'profile/';
        $fileName = "$user->id.jpg";

        if ($user->verified_at !== null) {
            return Redirect::route('dashboard');
        }
        $professional = new Professional();
        $professional->email = $user->email;
        $professional->phone_number = $request->telefone;
        $professional->photo_url = Env::get('APP_URL') . $filePath . $fileName;
        $professional->user()->associate($user);
        $professional->saveOrFail();

        $request->file('foto_perfil')->storePubliclyAs($filePath, $fileName);

        return Redirect::route('dashboard');
    }

    /**
     * @throws UnknownProperties
     */
    public function verify(Request $request)
    {
        $request->validate([
            'telefone' => ['required', new PhoneNumberFormat()],
        ]);

        /** @var User $user */
        $user = $request->user();
        if ($user->verification === null || ($user->verification instanceof Verification && $user->verification->hasExpired())) {
            $user->verification = new Verification([
                'code' => rand(100000, 999999),
                'expires_at' => now()->addMinutes(5),
            ]);
        }
        $user->phone_number = $request->telefone;
        $user->notify(new VerifyWithCode());
        $user->save();
    }
}
