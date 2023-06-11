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
            <div class="col-md-9">
                <h5 class="col-md-6 offset-md-4 ps-2">Oubli de votre mot de passe ou première connexion à l'intranet :</h5>
                <p class="col-md-6 offset-md-4 ps-2">Veuillez renseigner votre <i>email de connexion</i> afin de recevoir un lien de réinitialisation</p>
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-Mail Address') }}</label>
                        <div class="col-md-6">
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="icofont-email"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>
                          @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-7 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="icofont-paper-plane me-1"></i>{{ __('Send Password Reset Link') }}
                            </button>
                        </div>
                    </div>
                </form>
              @if (session('status'))
                <div class="alert alert-success mt-3 mb-0 text-center" role="alert">
                    {{ session('status') }}
                </div>
              @endif
            </div>
        </div>
    </div>
</section>
@include('includes.usage.footer')
@endsection
