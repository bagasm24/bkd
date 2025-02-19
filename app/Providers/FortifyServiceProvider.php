<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\PublikasiController;
use App\Models\User;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bind user-related actions
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);


        Fortify::username(function () {
            return 'nidn'; // Mengubah kolom autentikasi dari email ke nidn
        });

        Fortify::authenticateUsing(function (Request $request) {
            // Validasi input NIDN dan password
            $request->validate([
                'nidn' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = DB::table('users')->where('nidn', $request->nidn)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                session(['nidn' => $user->nidn]);
                return User::find($user->id);
            }

            return null;
        });

        // Rate limiter for login attempts
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('nidn')) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        // Rate limiter for two-factor authentication
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
