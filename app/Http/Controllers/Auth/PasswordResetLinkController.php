<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\PhoneNumberFormat;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function store(
        Request $request,
        User $user,
        Hasher $hasher,
        ConnectionInterface $connection
    ): RedirectResponse {
        $data = $request->validate([
            'email' => ['required_without:phone_number', 'nullable', 'email'],
            'phone_number' => ['required_without:email', 'nullable', new PhoneNumberFormat()]
        ]);
        $phoneNumber = $request->request->getDigits('phone_number');

        $hasEmail = $data['email'] && $user->where('email', $data['email'])->exists();
        $hasPhone = $phoneNumber && $connection->table('professionals')->where('phone_number', $phoneNumber)->exists();

        $email = $hasEmail ? $data['email'] : null;
        $phoneNumber = $hasPhone ? $phoneNumber : null;

        if (!$hasEmail && !$hasPhone) {
            throw ValidationException::withMessages([
                'email' => [trans(PasswordBroker::INVALID_USER)],
            ]);
        }

        $user->email = $email;
        $user->phone_number = $phoneNumber;

        $passwordResetsTable = $connection->table('password_resets');
        $createdAt = $passwordResetsTable->where(array_filter([
            'email' => $email,
            'phone_number' => $phoneNumber
        ]), boolean: 'or')->latest()->value('created_at');

        $expire = config('auth.passwords.users.expire');
        if (!is_null($createdAt) && (new Carbon($createdAt))->greaterThan(now()->subMinutes($expire))) {
            return Redirect::back()->with('error', trans(PasswordBroker::RESET_THROTTLED));
        }

        $connection->transaction(function () use ($user, $hasher, $phoneNumber, $email, $passwordResetsTable) {
            $token = uuid_create();
            $hash = $hasher->make($token);
            $passwordResetsTable->insert([
                'email' => $email,
                'phone_number' => $phoneNumber,
                'token' => $hash,
                'created_at' => now()
            ]);
            $user->sendPasswordResetNotification($token);
        });

        return Redirect::back()->with('success', trans(Password::RESET_LINK_SENT));
    }
}
