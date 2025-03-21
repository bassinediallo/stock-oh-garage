@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100 bg-light-body">
    <div class="card shadow-lg p-4 w-100" style="max-width: 450px; border-radius: 10px; background-color: #fada02">
        <!-- Logo -->
        <div class="text-center mb-4">
            <img src="https://ohgarage.com/wp-content/uploads/2025/02/logo1.png" alt="logo" class="img-fluid" style="max-width: 250px;">
        </div>
        
        <!-- Message de connexion -->
        <p class="text-center mb-4" style="color: black">Connectez-vous pour accéder à votre compte</p>
        
        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success mb-4">{{ session('status') }}</div>
        @endif

        <!-- Formulaire de connexion -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label" style="color: black">{{ __('Email') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="Entrez votre e-mail">
                @error('email')
                    <div class="mt-2 text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label" style="color: black">{{ __('Mot de passe') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Entrez votre mot de passe">
                @error('password')
                    <div class="mt-2 text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center">
                @if (Route::has('password.request'))
                    <a class="text-sm mb-2 mb-sm-0" href="{{ route('password.request') }}" style="color: black;">
                        {{ __('Mot de passe oublié?') }}
                    </a>
                @endif

                <button type="submit" class="btn" style="color: #fada02; background-color: black;">
                    {{ __('Se connecter') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
