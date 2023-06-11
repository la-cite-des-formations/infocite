@extends('layouts.usage-app')

@section('tabSubtitle', " | ".__('Login'))

@section('end-space')
<div class="col-1"></div>
@endsection

@section('content')
@include('includes.usage.header', ['fixedTop' => FALSE])
<section id="corps_page" class="d-flex align-items-center pt-4 pb-0">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2>Connexion à l'intranet</h2>
        </div>
    </div>
</section>
<section id="corps_page" class="services section-bg pt-4">
    <div class="container col-9">
        <div class="text-center">
            <p class="fw-bold">
                Vous possédez une adresse email <i>citeformations.com</i>
            </p>
            <a href="{{ url('login/google') }}" role="button" class="btn btn-outline-dark mb-3">
                <img src="{{ asset('img/google.png') }}" style="height: 32px"
                     alt="Connectez vous avec votre compte Google">
                {{ __('Log in with Google') }}
            </a>
        </div>
        <div class="section-title">
            <h2 class="mb-0">ou</h2>
        </div>
        <p class="text-center fw-bold">
            Vous ne possédez pas d'adresse email <i>citeformations.com</i>,
            mais l'administrateur vous a octroyé un compte d'accès à l'intranet
        </p>
        <div class="row">
            <form method="POST" action="{{ route('login') }}" class="col-6 border-end pe-4 py-2">
                @csrf
                <div class="form-group row mb-3">
                    <label for="email" class="col-sm-4 col-form-label text-end">{{ __('E-Mail Address') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="icofont-email"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ __('Login E-Mail') }}">
                        </div>
                    </div>
                  @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                <div class="form-group row mb-3">
                    <label for="password" class="col-sm-4 col-form-label text-end">{{ __('Password') }}</label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="icofont-ui-password"></i></span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">
                        </div>
                    </div>
                  @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                <div class="form-group row mb-3">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary"><i class="icofont-login"></i> {{ __('Login') }}</button>
                    </div>
                </div>
            </form>
            <div class="col-6 d-flex flex-column justify-content-center">
                <h5 class="text-center mb-3">{{ __('Forgot Your Password ?') }}<br/>{{ __('First Login ?') }}</h5>
              @if (Route::has('password.request'))
                <a class="btn btn-success mx-auto" href="{{ route('password.request') }}"><i class="icofont-ui-keyboard me-1"></i>{{ __('Reset Password') }}</a>
              @endif
            </div>
        </div>
    </div>
</section>
@include('includes.usage.footer')
@endsection
