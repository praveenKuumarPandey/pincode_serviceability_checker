<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name='csrf-token' content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pincode Serviceability Checker') }}</title>


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">


    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css')}}">

    <!-- js -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    {{-- <script src="{{ asset('js/manifest.js') }}" defer></script> --}}



</head>

<body>

    <nav class="navbar navbar-expand-lg ">
        <div class="container">
            <a class="navbar-brand" href="#">{{ config('app.name', 'Laravel App') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('pincode.view') }}">Home</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Pincode Serviceability Checker</a>
                        </a>
                        <ul class="dropdown-menu bg-dark">
                            <li><a class="dropdown-item" href="{{ route('pincode.view') }}">Product
                                    Pincode Checker Tool</a></li>
                            <li><a class="dropdown-item" href="{{ route('pincode.view') }}">Upload Updated
                                    Serviceability Data</a></li>
                        </ul>
                    </li>

                </ul>
                {{-- <div class="d-flex">
                    @if (Route::has('login'))
                    <div>
                        @auth
                        <a href="{{ url('/productManagement') }}">Home</a>
                        <a href="{{ url('/logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout')}}" method="POST" style="display: none">
                            @csrf
                        </form>
                        @else
                        <a href="{{ route('login') }}">Login</a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                        @endif
                        @endif

                    </div>
                    @endif
                </div> --}}
            </div>
        </div>
    </nav>


    <main class="container">
        @yield('content')

    </main>


    @stack('scripts')

</body>

</html>