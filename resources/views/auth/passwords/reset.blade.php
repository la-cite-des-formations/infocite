@extends('layouts.usage-app')

@section('tabSubtitle', " | Réinitialisation mdp")

@section('end-space')
<div class="col-1"></div>
@endsection

@section('content')
@include('includes.usage.header', ['fixedTop' => FALSE])
<section id="corps_page" class="d-flex align-items-center pt-4 pb-0">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2>{{ __('Reset Password') }}</h2>
        </div>
    </div>
</section>
<section id="corps_page" class="services section-bg pt-4 pb-5">
    <div class="container col-9">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group row mb-3">
                        <label for="email" class="col-md-5 col-form-label text-md-end">{{ __('E-Mail Address') }}</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="icofont-email"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                            </div>
                          @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <label for="password" class="col-md-5 col-form-label text-md-end">{{ __('Password') }}</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="icofont-key-hole"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            </div>
                          @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <label for="password-confirm" class="col-md-5 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="icofont-ui-password"></i></span>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-5">
                            <button type="submit" class="btn btn-primary">
                                <i class="icofont-ui-reply"></i> {{ __('Reset Password') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@include('includes.usage.footer')
@endsection
