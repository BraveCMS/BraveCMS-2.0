<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
  public function redirect()
  {
    return Socialite::driver('google')->redirect();
  }

  public function callback()
  {
    $googleUser = Socialite::driver('google')->user();

    /*
     * First find the user by provider + provider ID.
     */
    $user = User::where('provider', 'google')
      ->where('provider_id', $googleUser->getId())
      ->first();

    /*
     * If this Google account is not linked yet,
     * check whether the email already exists.
     */
    if (!$user) {
      $user = User::where('email', $googleUser->getEmail())->first();

      if ($user) {
        $user->provider = 'google';
        $user->provider_id = $googleUser->getId();
      } else {
        $user = new User();

        $user->role_id = 1;
        $user->first_name = $googleUser->user['given_name'] ?? $googleUser->getName();
        $user->last_name = $googleUser->user['family_name'] ?? '';
        $user->email = $googleUser->getEmail();
        $user->password = Hash::make(bin2hex(random_bytes(32)));
        $user->provider = 'google';
        $user->provider_id = $googleUser->getId();
        $user->active = 1;
      }
    }

    /*
     * Download the Google profile picture only if
     * the user does not already have a local avatar.
     */
    if (
      empty($user->avatar) ||
      $user->avatar === 'default.png'
    ) {
      $avatarUrl = $googleUser->getAvatar();

      if ($avatarUrl) {
        $response = Http::get($avatarUrl);

        if ($response->successful()) {
          $filename = Str::random(32) . '.jpg';

          file_put_contents(
            public_path('images/avatars/' . $filename),
            $response->body()
          );

          $user->avatar = $filename;
        }
      }
    }

    $user->save();

    Auth::login($user, true);

    return redirect('/');
  }
}
