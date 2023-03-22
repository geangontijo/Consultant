<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\User;
use App\Models\Verification;
use App\Notifications\VerifyWithCode;
use App\Rules\PhoneNumberFormat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class UserController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function storeProfessional(Request $request, Professional $professional): RedirectResponse
    {
        // TODO: receive certificate
        $request->validate([
            'phone_number' => ['required', new PhoneNumberFormat()],
            'taxpayer_id' => ['required', 'cpf'],
            'profile_photo' => ['required', File::image()->max(1024)],
            'verification_code' => ['required', 'digits:6'],
        ]);

        /** @var User $user */
        $user = $request->user();

        if (null !== $user->verified_at) {
            return Redirect::route('dashboard');
        }
        if ($user->verification->hasExpired()) {
            throw ValidationException::withMessages(['verification_code' => trans('auth.verify.expired')]);
        }
        if ($user->verification->code !== $request->integer('verification_code')) {
            throw ValidationException::withMessages(['verification_code' => trans('auth.verify.failed_code')]);
        }
        $user->verified_at = new \DateTime();
        $user->verification = null;
        $user->save();
        $filePath = 'profile/';
        $fileName = "$user->id.jpg";

        $professional->email = $user->email;
        $professional->phone_number = $request->query->getDigits('telefone');
        $professional->photo_url = Env::get('APP_URL').$filePath.$fileName;
        $professional->user()->associate($user);
        $professional->saveOrFail();

        $request->file('profile_photo')->storePubliclyAs($filePath, $fileName);

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
        if (null === $user->verification || ($user->verification instanceof Verification && $user->verification->hasExpired())) {
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
