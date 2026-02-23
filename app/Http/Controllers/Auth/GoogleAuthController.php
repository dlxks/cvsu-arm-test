<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->getEmail();

            //  Domain Restriction
            if (! Str::endsWith($email, ['@cvsu.edu.ph', '@gmail.com'])) {
                return redirect('/')->with('error', 'CvSU - Academic Resource Management can only be used within its organization.');
            }

            // Manual User Check
            $user = User::where('email', $email)->first();

            if (! $user) {
                return redirect('/')->with('error', 'Access denied. You are not registered in the system.');
            }

            // Update & Login
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            Auth::login($user);

            // Role Redirection matching web.php
            return redirect()->route('dashboard.resolve');

            // Fallback for users with no role
            return redirect('/')->with('error', 'No role assigned to this account.');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Authentication failed. Please try again.');
        }
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
