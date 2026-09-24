@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header">{{ __('Login') }}</div>

        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="row mb-2">
                    <label for="email" class="col-md-12">{{ __('Email Address') }}</label>

                    <div class="col-md-12 @error('email') has-error @enderror">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="password" class="col-md-12">{{ __('Password') }}</label>

                    <div class="col-md-12 @error('password') has-error @enderror">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-12">
                        <button type="submit" class="w-100 btn btn-success">
                            {{ __('Login') }}
                        </button>

                        @if (Route::has('password.request'))
                            <a class="btn btn-link px-0 text-success" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-12">
            <a href="{{ route('google.login') }}" class="w-100 btn btn-light shadow-sm btn-social-login bg-white">
                <span class="btn-icon">
                    <img src="{{ asset('images/google-logo.png') }}" alt="Google" width="20" height="20">
                </span>
                {{ __('Login with Google') }}
            </a>
        </div>

        <div class="col-md-12 mt-1">
            <a href="{{ route('facebook.login') }}"
                class="w-100 btn shadow-sm btn-social-login btn-fb-login text-white">
                <span class="btn-icon">
                    <img src="{{ asset('images/facebook-logo.png') }}" alt="Facebook" width="20" height="20">
                </span>
                {{ __('Login with Facebook') }}
            </a>
        </div>
    </div>
@endsection
