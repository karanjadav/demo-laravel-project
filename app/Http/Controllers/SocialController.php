<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirect($provider) {
        return Socialite::driver($provider)->redirect();
    }

    public function Callback($provider) {
        $userSocial = Socialite::driver($provider)->stateless()->user();
        $user =   User::where(['email' => $userSocial->getEmail()])->first();
        if ($user) {
            Auth::login($user);
        } else {
            $user = User::create([
                'name'          => $userSocial->getName(),
                'email'         => $userSocial->getEmail(),
                'image'         => $userSocial->getAvatar(),
                'provider_id'   => $userSocial->getId(),
                'provider'      => $provider,
                'password'      => Hash::make('123456')
            ]);
        }
        if(!$user->hasRole('user')) {
            $user->assignRole('user');
        }
        return redirect()->route('home');
    }
}
