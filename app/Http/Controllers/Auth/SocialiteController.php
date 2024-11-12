<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Socialite as ModelsSocialite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    public function redirect($provider) {
        return Socialite::driver($provider)->redirect();
       }
    
       public function callback($provider) {
            $socialUser = Socialite::driver($provider)->user();
    
            $authUser = $this->store($socialUser, $provider);
    
            Auth::login($authUser);

            if(Auth::user()->hasRole('student')) {
               $student = auth()->user();

            if (Str::startsWith($student->avatar, 'http') && $student->provider == 'google') {
               $avatarContents = Http::get($student->avatar)->body();
               $avatarPath = 'avatars/' . $student->id . '.jpg';  // Sesuaikan nama file sesuai kebutuhan
               Storage::put($avatarPath, $avatarContents);
               $student->avatar = $avatarPath;  // Simpan path lokal di database
               $student->save();
            }
               return redirect()->route('dashboard.learning.index');
           }
    
            return redirect('/dashboard');
       }
    
       public function store($socialUser, $provider) {
        $socialAccount = ModelsSocialite::where('provider_id', $socialUser->id)->where('provider_name', $provider)->first();
    
        if (!$socialAccount) {
          $user = User::where('email', $socialUser->getEmail())->first();
    
          if(!$user) {
             $user = User::updateOrCreate([
                'name' => $socialUser->getName() ? $socialUser->getName() : $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make('socialite'),
                'avatar' => $socialUser->getAvatar(),
             ]);
          }
    
          $user->socialites()->create([
             'provider_id' => $socialUser->getId(),
             'provider_name' => $provider,
             'provider_token' => $socialUser->token,
             'provider_refresh_token' => $socialUser->refreshToken,
          ]);

          $user->assignRole('student');
    
          return $user;
        }
    
        return $socialAccount->user;
       }
}
