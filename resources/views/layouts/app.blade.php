<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Oh Garage - Gestion des Stocks') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Styles supplémentaires -->
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div id="app">
    <nav class="navbar navbar-expand-md navbar-light fixed-top" style="background: #fada02; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold text-dark" href="{{ url('/') }}">
            <img src="https://ohgarage.com/wp-content/uploads/2025/02/logo1.png" alt="Logo" class="img-fluid" style="width: 150px;">
        </a>

        <!-- Bouton responsive -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Liens de gauche -->
            @auth
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-dark fw-bold" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-bold" href="{{ route('sites.index') }}">
                        <i class="fas fa-building"></i> Sites
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-bold" href="{{ route('departments.index') }}">
                        <i class="fas fa-sitemap"></i> Départements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-bold" href="{{ route('products.index') }}">
                        <i class="fas fa-box"></i> Produits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark fw-bold" href="{{ route('checklists.index') }}">
                        <i class="fas fa-clipboard-check"></i> Checklists
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-warehouse"></i> Stock
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('stock-movements.index') }}"><i class="fas fa-exchange-alt"></i> Mouvements</a></li>
                        <li><a class="dropdown-item" href="{{ route('stock-movements.create') }}"><i class="fas fa-plus-circle"></i> Nouveau mouvement</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/reports/stock"><i class="fas fa-chart-bar"></i> Rapport de stock</a></li>
                    </ul>
                </li>
            </ul>
            @endauth

            <!-- Liens de droite -->
            <ul class="navbar-nav ms-auto">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link text-dark fw-bold" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> Connexion
                            </a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle text-dark fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-cog"></i> Profile</a>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar .nav-link {
        transition: color 0.3s ease-in-out;
    }
    .navbar .nav-link:hover {
        color: white !important;
    }
</style>


        <main class="py-4">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="fas fa-exclamation-triangle"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts supplémentaires -->
    @stack('scripts')
</body>
</html>
