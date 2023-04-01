@extends('layouts.app')

@section('content')
    <div class="container-fluid h-100">
        <div class="row h-100 align-items-center p-3">
            <div class="col-sm-6 mx-auto my-5 p-0 login-form">
                <div class="form-group text-center bg-form-head p-3">
                    <h3 class="text-white"> Create Account</h3>
                </div>
                <form method="POST" action="{{ route('register') }}" class=" p-5 ">
                    @csrf
                    <div class="form-group my-3">
                        <label for="" class="text-muted mb-2 fw-600"> User Name</label>
                        <div>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group my-3">
                        <label for="" class="text-muted mb-2 fw-600"> Email Id</label>
                        <div>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group my-3">
                        <label for="" class="text-muted mb-2 fw-600"> Password</label>
                        <div class="">
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" name="password" required
                                autocomplete="new-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group my-3">
                        <label for="" class="text-muted mb-2 fw-600"> Confirm Password</label>
                        <div class="">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                                required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-group my-5 text-center">
                        <button class="btn btn-login w-100 text-white"> Sign Up</button>
                    </div>
                    @if (Route::has('login'))
                        <div class="form-group text-center">
                            <label for="" class="text-muted">Already Have an account ?</label>
                            <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Sign in</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
