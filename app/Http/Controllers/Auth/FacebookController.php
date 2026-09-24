<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
  public function redirect()
  {
    return Socialite::driver('facebook')->redirect();
  }

  public function callback()
  {
    $facebookUser = Socialite::driver('facebook')->user();

    /*
     * Get the Facebook profile picture directly from Graph API.
     */
    $graphResponse = Http::get('https://graph.facebook.com/v23.0/me', [
      'fields' => 'id,name,email,picture.type(large)',
      'access_token' => $facebookUser->token,
    ]);

    $graphUser = $graphResponse->json();

    /*
     * First find the user by provider + provider ID.
     */
    $user = User::where('provider', 'facebook')
      ->where('provider_id', $facebookUser->getId())
      ->first();

    /*
     * If this Facebook account is not linked yet,
     * check whether the email already exists.
     */
    if (!$user) {
      $user = User::where('email', $facebookUser->getEmail())->first();

      if ($user) {
        $user->provider = 'facebook';
        $user->provider_id = $facebookUser->getId();
      } else {
        $name = $facebookUser->getName();
        $nameParts = explode(' ', $name, 2);

        $user = new User();

        $user->role_id = 1;
        $user->first_name = $nameParts[0] ?? '';
        $user->last_name = $nameParts[1] ?? '';
        $user->email = $facebookUser->getEmail();
        $user->password = Hash::make(bin2hex(random_bytes(32)));
        $user->provider = 'facebook';
        $user->provider_id = $facebookUser->getId();
        $user->active = 1;
      }
    }

    /*
     * Download the Facebook profile picture only if
     * the user does not already have a local avatar.
     */
    if (
      empty($user->avatar) ||
      $user->avatar === 'default.png'
    ) {
      if (!empty($graphUser['picture']['data']['url'])) {
        $avatarResponse = Http::get(
          $graphUser['picture']['data']['url']
        );

        if ($avatarResponse->successful()) {
          $filename = Str::random(32) . '.jpg';

          file_put_contents(
            public_path('images/avatars/' . $filename),
            $avatarResponse->body()
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
