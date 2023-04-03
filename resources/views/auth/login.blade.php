@extends('layouts.app')

@section('content')
    <div class="row h-100 align-items-center p-3">
        <div class="col-sm-6 mx-auto my-5 p-0 login-form">
            <div class="form-group text-center mb-2 bg-form-head p-3">
                <h3 class="text-white"> Login Form </h3>
            </div>
            <form method="POST" class="login-form p-5" action="{{ route('login') }}" autocomplete="off">
                @csrf

                <div class="form-group my-3">
                    <label for="" class="text-muted mb-2 fw-600"> Email Id</label>
                    <div>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group my-3">
                    <label for="" class="text-muted mb-2 fw-600"> Password </label>
                    <div>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                {{-- <div class="form-group my-3 text-end">
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none fw-bold" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div> --}}
                <div class="form-group my-5 text-center">
                    <button type="submit" class="btn btn-login w-100 text-white h-50px">
                        {{ __('Login') }}
                    </button>

                </div>
                @if (Route::has('register'))
               
                <div class="form-group text-center">
                    <label for="" class="text-muted">Don't have an account ? </label>
                    <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Sign Up </a>
                </div>
            @endif
            </form>
        </div>
    </div>
    </div>
@endsection
