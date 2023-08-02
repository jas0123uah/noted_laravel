@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0">
                <!-- <div class="card-header">{{ __('Login') }}</div> -->

                <div class="card-body">
                    <p class="text-center">Learn something today.</p>
                    <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="row mb-3">
        <div class="col-md-6 offset-md-3"> <!-- Modified: Added 'offset-md-3' class -->
            <input id="email" placeholder="email" type="email" class="form-control border-dark bg-white @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 offset-md-3"> <!-- Modified: Added 'offset-md-3' class -->
            <input id="password" placeholder="password" type="password" class="form-control border-dark bg-white @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 offset-md-3"> <!-- Modified: Added 'offset-md-3' class -->
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                <label class="form-check-label" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div>
        </div>
    </div>

    <div class="row mb-0">
        <div class="col-md-6 offset-md-3 d-flex flex-column"> <!-- Modified: Added 'offset-md-3' class -->
            <button type="submit" class="btn button text-white">
                {{ __('Login') }}
            </button>

            @if (Route::has('password.request'))
                <a class="btn" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
        </div>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
