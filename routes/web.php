<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\ConsultationAnnouncement;
use App\Models\Order;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Inertia::share('app', fn (\Illuminate\Http\Request $request) => [
    'auth' => [
        'user' => $request->user()
            ? $request->user()->only('id', 'name', 'email', 'email_verified_at')
            : null,
    ],
    'flash' => [
        'info' => fn () => $request->session()->get('message'),
        'success' => fn () => $request->session()->get('success'),
        'error' => fn () => $request->session()->get('error'),
    ],
    'data' => [
        'home.search' => fn () => $request->session()->get('home.search'),
    ]
]);

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('home.search');

Route::get('/announce/{id}', [HomeController::class, 'announce'])->name('announce');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('user/verify', [UserController::class, 'verify'])->name('user.verify');

    Route::get('/professional', function () {
        return Inertia::render('Professional');
    })->name('professional');

    Route::post('/professional', [UserController::class, 'storeProfessional'])->name('professional.store');

    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/checkout/{order}', function (string $order) {
        $order = Order::with('payments')->findOrFail($order);
        return Inertia::render('Checkout', compact('order'));
    })->name('checkout');

    Route::post('/checkout/{order}/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $announces = ConsultationAnnouncement::with('appointmentTimes')
            ->where('professional_id', auth()->id())
            ->get();
        return Inertia::render('Dashboard', compact('announces'));
    })->name('dashboard');

    Route::post('/announce', [ProfessionalController::class, 'storeAnnounce'])->name('announce.store');
    Route::delete('/announce/{id}', [ProfessionalController::class, 'destroyAnnounce'])->name('announce.destroy');
});

require __DIR__.'/auth.php';
