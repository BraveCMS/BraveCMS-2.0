<?php

namespace App\Providers;

use Laravel\Socialite\Two\FacebookProvider as SocialiteFacebookProvider;

class FacebookProvider extends SocialiteFacebookProvider
{
    protected $version = 'v23.0';

    protected $scopes = [];
}